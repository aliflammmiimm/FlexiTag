<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOrderConfirmationMail;
use App\Mail\NewOrderAdminNotification;
use App\Models\AuditLog;
use App\Models\Design;
use App\Models\FinancialEntry;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\MidtransService;
use App\Services\OrderActivationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class OrderProcessController extends Controller
{
    /**
     * Tampilkan formulir kustomisasi dan pemesanan gantungan kunci NFC.
     */
    public function create(Request $request): View
    {
        $designs = Design::active()->get();
        $selectedDesignId = $request->query('design_id');
        $selectedDesign = $selectedDesignId ? Design::find($selectedDesignId) : null;

        $unitPrice = (float) config('flexitag.unit_price', 75000);
        $wholesaleThreshold = 10;
        $wholesaleUnitPrice = (float) config('flexitag.wholesale_unit_price', 60000);

        return view('user.order.create', compact(
            'designs',
            'selectedDesign',
            'unitPrice',
            'wholesaleThreshold',
            'wholesaleUnitPrice'
        ));
    }

    /**
     * Proses penyimpanan pesanan baru (Online maupun Offline).
     */
    public function store(Request $request, MidtransService $midtransService, OrderActivationService $activationService): RedirectResponse
    {
        // Pemeriksaan typo domain umum sebelum validasi ketat
        $suggestedEmail = OrderActivationService::detectEmailDomainTypo((string) $request->input('email'));
        if ($suggestedEmail) {
            return back()->withErrors([
                'email' => "Apakah maksud Anda '{$suggestedEmail}'? Domain email yang Anda masukkan kemungkinan salah ketik.",
            ])->withInput();
        }

        $emailRule = app()->environment('testing') ? 'email:rfc' : 'email:rfc,dns';

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:100'],
            'whatsapp' => ['required', 'string', 'min:8', 'max:30'],
            'email' => ['required', 'string', $emailRule, 'max:150'],
            'channel' => ['required', 'in:online,offline'],
            'delivery_method' => ['nullable', 'in:ambil,kirim'],
            'address' => ['nullable', 'string', 'max:500'],
            'shape' => ['required', 'string', 'max:50'],
            'size' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['nullable', 'string', 'in:midtrans,tunai_offline,transfer_bank,qris'],
            'design_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg,pdf,ai,psd,zip', 'max:10240'],
            'payment_proof' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
        ], [
            'customer_name.required' => 'Nama lengkap pemesan wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi untuk konfirmasi & pelacakan.',
            'email.required' => 'Alamat email wajib diisi untuk pengiriman kode aktivasi.',
            'email.email' => 'Format alamat email tidak valid atau domain email tidak ditemukan aktif. Pastikan Anda memasukkan email yang benar.',
            'email.max' => 'Alamat email tidak boleh melebihi 150 karakter.',
            'shape.required' => 'Bentuk gantungan kunci wajib dipilih.',
            'size.required' => 'Ukuran akrilik wajib dipilih.',
            'color.required' => 'Warna / motif akrilik wajib ditentukan.',
            'quantity.required' => 'Jumlah pesanan minimal 1 unit.',
        ]);

        $isOffline = $validated['channel'] === 'offline';
        $deliveryMethod = $isOffline ? 'ambil' : ($validated['delivery_method'] ?? 'kirim');

        // Jika kanal online dan metode kirim, alamat pengiriman wajib ada
        if (! $isOffline && $deliveryMethod === 'kirim' && empty(trim((string) $request->input('address')))) {
            return back()->withErrors(['address' => 'Alamat lengkap tujuan pengiriman wajib diisi bila memilih opsi kirim kurir.'])->withInput();
        }

        $address = (! $isOffline && $deliveryMethod === 'kirim' && ! empty($validated['address']))
            ? $validated['address']
            : 'Ambil di Workshop FlexiTag Makassar';

        // Hitung harga satuan & total secara server-side
        $baseUnitPrice = (float) config('flexitag.unit_price', 75000);
        $wholesaleUnitPrice = (float) config('flexitag.wholesale_unit_price', 60000);
        $wholesaleThreshold = 10;

        $qty = (int) $validated['quantity'];
        $unitPrice = ($qty >= $wholesaleThreshold) ? $wholesaleUnitPrice : $baseUnitPrice;
        $total = $unitPrice * $qty;

        // Upload berkas desain kustom (bila ada)
        $designFilePath = null;
        if ($request->hasFile('design_file')) {
            $designFilePath = $request->file('design_file')->store('orders/designs', 'public');
        }

        // Upload struk bukti pembayaran (bila ada)
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('orders/payments', 'public');
        }

        // Tentukan status awal
        $initialStatus = Order::STATUS_MENUNGGU_PEMBAYARAN;
        if ($paymentProofPath) {
            $initialStatus = Order::STATUS_PEMBAYARAN_DIKONFIRMASI;
        }

        $orderNo = Order::generateOrderNo();

        // Default metode pembayaran: offline -> tunai_offline, online -> midtrans
        $paymentMethod = $validated['payment_method'] ?? ($isOffline ? 'tunai_offline' : 'midtrans');

        $order = Order::create([
            'order_no' => $orderNo,
            'channel' => $validated['channel'],
            'customer_name' => $validated['customer_name'],
            'whatsapp' => preg_replace('/\s+/', '', $validated['whatsapp']),
            'email' => strtolower(trim($validated['email'])),
            'delivery_method' => $deliveryMethod,
            'address' => $address,
            'shape' => $validated['shape'],
            'size' => $validated['size'],
            'color' => $validated['color'],
            'notes' => $validated['notes'] ?? null,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'total' => $total,
            'payment_method' => $paymentMethod,
            'status' => $initialStatus,
            'design_file_path' => $designFilePath,
            'payment_proof_path' => $paymentProofPath,
        ]);

        // Buat Snap Token Midtrans untuk pesanan jika status menunggu pembayaran
        if ($initialStatus === Order::STATUS_MENUNGGU_PEMBAYARAN && $midtransService->isConfigured()) {
            $snapToken = $midtransService->createSnapToken($order);
            if ($snapToken) {
                $order->update(['snap_token' => $snapToken]);
            }
        }

        // Catat entri awal pada order_status_logs
        $initialNote = $validated['channel'] === 'offline'
            ? 'Pesanan offline / langsung dibuat oleh pelanggan.'
            : 'Pesanan online baru dibuat melalui formulir pemesanan web.';

        if ($paymentProofPath) {
            $initialNote .= ' Bukti pembayaran telah diunggah pelanggan.';
        }

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $initialStatus,
            'changed_at' => now(),
            'note' => $initialNote,
            'admin_id' => null,
        ]);

        AuditLog::record('create_order', "Pesanan baru [{$orderNo}] berhasil dibuat melalui kanal {$validated['channel']}");

        if ($initialStatus === Order::STATUS_PEMBAYARAN_DIKONFIRMASI) {
            FinancialEntry::recordOrderPayment($order);
            try {
                $activationService->processOrderConfirmation($order);
            } catch (\Throwable $e) {
                Log::error("Gagal memproses alokasi tag/email aktivasi pesanan #{$orderNo}: ".$e->getMessage());
            }
        }

        // Catat notifikasi pesanan baru ke Drawer Lonceng Admin
        try {
            Message::recordNewOrderNotification($order);
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat notifikasi pesanan ke drawer admin: '.$e->getMessage());
        }

        // Kirim email konfirmasi tanda terima pesanan ke Pemesan / Pelanggan
        if (! empty($order->email) && filter_var($order->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($order->email)->send(new CustomerOrderConfirmationMail($order));
            } catch (\Throwable $e) {
                Log::warning("Gagal mengirimkan email konfirmasi pesanan ke pelanggan #{$orderNo} ({$order->email}): ".$e->getMessage());
            }
        }

        // Kirim notifikasi email pesanan baru ke Admin jika aktif (FR-ADM-07)
        try {
            $notifEmail = DB::table('settings')->where('key', 'notifikasi_email_pesanan')->value('value');
            if ($notifEmail === null || in_array((string) $notifEmail, ['1', 'true'], true)) {
                $adminEmail = config('mail.from.address') ?? 'admin@flexitag.id';
                Mail::to($adminEmail)->send(new NewOrderAdminNotification($order));
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirimkan notifikasi email pesanan baru ke admin: '.$e->getMessage());
        }

        return redirect()->route('order.success', ['order_no' => $order->order_no])
            ->with('success', 'Pesanan Anda berhasil dikirim! Silakan selesaikan pembayaran.');
    }

    /**
     * Halaman konfirmasi sukses setelah pesanan dikirim.
     */
    public function success(string $order_no, MidtransService $midtransService): View
    {
        $order = Order::with('statusLogs')->where('order_no', $order_no)->firstOrFail();

        // Jika pesanan belum bayar dan belum memiliki snap token, generate sekarang jika Midtrans terkonfigurasi
        if ($order->status === Order::STATUS_MENUNGGU_PEMBAYARAN
            && empty($order->snap_token)
            && $midtransService->isConfigured()) {
            $snapToken = $midtransService->createSnapToken($order);
            if ($snapToken) {
                $order->update(['snap_token' => $snapToken]);
                $order->refresh();
            }
        }

        return view('user.order.success', compact('order'));
    }

    /**
     * Ambil / perbarui Snap Token untuk pesanan tertentu (AJAX).
     */
    public function getSnapToken(string $order_no, MidtransService $midtransService): JsonResponse
    {
        $order = Order::where('order_no', $order_no)->firstOrFail();

        if ($order->status !== Order::STATUS_MENUNGGU_PEMBAYARAN) {
            return response()->json([
                'error' => 'Pesanan sudah tidak memerlukan pembayaran.',
                'status' => $order->status,
            ], 400);
        }

        if (empty($order->snap_token)) {
            $snapToken = $midtransService->createSnapToken($order);
            if ($snapToken) {
                $order->update(['snap_token' => $snapToken]);
            }
        }

        return response()->json([
            'order_no' => $order->order_no,
            'snap_token' => $order->snap_token,
        ]);
    }

    /**
     * Cek status pembayaran pesanan secara real-time (AJAX polling).
     */
    public function checkStatus(string $order_no): JsonResponse
    {
        $order = Order::where('order_no', $order_no)->firstOrFail();

        return response()->json([
            'order_no' => $order->order_no,
            'status' => $order->status,
            'is_paid' => $order->status === Order::STATUS_PEMBAYARAN_DIKONFIRMASI || ! in_array($order->status, [Order::STATUS_MENUNGGU_PEMBAYARAN, Order::STATUS_DIBATALKAN], true),
            'status_label' => $order->detail_status_label,
            'status_badge' => $order->status_badge,
        ]);
    }

    /**
     * Pilih / ubah metode pembayaran menjadi Tunai di Kasir (Offline).
     */
    public function selectCash(string $order_no): JsonResponse|RedirectResponse
    {
        $order = Order::where('order_no', $order_no)->firstOrFail();

        if ($order->status !== Order::STATUS_MENUNGGU_PEMBAYARAN) {
            return request()->expectsJson()
                ? response()->json(['error' => 'Status pesanan tidak dapat diubah.'], 400)
                : back()->with('error', 'Status pesanan tidak dapat diubah.');
        }

        $order->update(['payment_method' => 'tunai_offline']);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'changed_at' => now(),
            'note' => 'Pelanggan memilih metode pembayaran Tunai di Kasir Workshop/Booth.',
            'admin_id' => null,
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'payment_method' => 'tunai_offline',
                'message' => 'Metode pembayaran disetel ke Tunai di Kasir.',
            ]);
        }

        return redirect()->route('order.success', ['order_no' => $order->order_no])
            ->with('success', 'Metode pembayaran disetel ke Tunai di Kasir.');
    }

    /**
     * Pilih / ubah metode pembayaran menjadi Midtrans (Online / QRIS / VA).
     */
    public function selectOnline(string $order_no, MidtransService $midtransService): JsonResponse|RedirectResponse
    {
        $order = Order::where('order_no', $order_no)->firstOrFail();

        if ($order->status !== Order::STATUS_MENUNGGU_PEMBAYARAN) {
            return request()->expectsJson()
                ? response()->json(['error' => 'Status pesanan tidak dapat diubah.'], 400)
                : back()->with('error', 'Status pesanan tidak dapat diubah.');
        }

        $order->update(['payment_method' => 'midtrans']);

        if (empty($order->snap_token) && $midtransService->isConfigured()) {
            $snapToken = $midtransService->createSnapToken($order);
            if ($snapToken) {
                $order->update(['snap_token' => $snapToken]);
            }
        }

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'changed_at' => now(),
            'note' => 'Pelanggan memilih metode pembayaran Online (Midtrans QRIS / Virtual Account).',
            'admin_id' => null,
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'payment_method' => 'midtrans',
                'snap_token' => $order->snap_token,
                'message' => 'Metode pembayaran disetel ke Pembayaran Online.',
            ]);
        }

        return redirect()->route('order.success', ['order_no' => $order->order_no])
            ->with('success', 'Metode pembayaran disetel ke Pembayaran Online.');
    }

    /**
     * Halaman publik untuk melacak status pesanan secara real-time.
     */
    public function track(Request $request): View
    {
        $orderNo = trim((string) $request->query('order_no', ''));
        $whatsapp = trim((string) $request->query('whatsapp', ''));
        $searched = ! empty($orderNo);

        $order = null;
        $error = null;

        if ($searched) {
            $query = Order::with(['statusLogs', 'tags'])->where('order_no', $orderNo);

            if (! empty($whatsapp)) {
                $cleanWa = preg_replace('/\D+/', '', $whatsapp);
                $query->whereRaw("REPLACE(REPLACE(REPLACE(whatsapp, ' ', ''), '-', ''), '+', '') LIKE ?", ["%{$cleanWa}%"]);
            }

            $order = $query->first();

            if (! $order) {
                $error = "Pesanan dengan nomor [{$orderNo}] tidak ditemukan. Mohon periksa kembali nomor pesanan Anda.";
            }
        }

        $allStatuses = Order::allStatuses();

        return view('user.order.track', compact('order', 'searched', 'orderNo', 'whatsapp', 'error', 'allStatuses'));
    }

    /**
     * Pemeriksaan real-time validitas format email dan deteksi typo domain via AJAX.
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $email = strtolower(trim((string) $request->input('email', '')));

        if (empty($email)) {
            return response()->json([
                'valid' => false,
                'message' => 'Alamat email wajib diisi.',
            ]);
        }

        // Cek typo domain
        $typoSuggestion = OrderActivationService::detectEmailDomainTypo($email);
        if ($typoSuggestion) {
            return response()->json([
                'valid' => false,
                'has_typo' => true,
                'suggestion' => $typoSuggestion,
                'message' => "Domain email terindikasi salah ketik. Apakah maksud Anda: {$typoSuggestion}?",
            ]);
        }

        // Validasi format RFC dan DNS MX record
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email:rfc,dns',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'has_typo' => false,
                'message' => 'Domain atau format email tidak valid. Pastikan email Anda aktif.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'has_typo' => false,
            'message' => 'Alamat email valid & aktif. Siap menerima kode aktivasi.',
        ]);
    }
}
