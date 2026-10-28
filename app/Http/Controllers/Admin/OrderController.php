<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOrderConfirmationMail;
use App\Models\AuditLog;
use App\Models\FinancialEntry;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\OrderActivationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display the Orders management list.
     */
    public function index(Request $request, OrderActivationService $activationService): View
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search', '');

        // Query real database orders with log history
        $query = Order::with('statusLogs.admin')->latest();

        // Filter by Delivery / Workflow Stage
        if ($statusFilter === 'perlu_dikirim') {
            $query->whereIn('status', [
                Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
                Order::STATUS_DESAIN_DISETUJUI,
                Order::STATUS_DICETAK,
                Order::STATUS_DIRAKIT_DAN_DIUJI,
            ]);
        } elseif ($statusFilter === 'terkirim') {
            $query->whereIn('status', [
                Order::STATUS_SIAP_DIAMBIL_DIKIRIM,
                Order::STATUS_SELESAI,
            ]);
        } elseif ($statusFilter === 'pending') {
            $query->where('status', Order::STATUS_MENUNGGU_PEMBAYARAN);
        }

        // Filter by Search Query
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_no', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('shape', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $orders = $query->get();

        // Calculate dynamic metrics directly from database
        $totalOrdersCount = Order::count();
        $pendingCount = Order::where('status', Order::STATUS_MENUNGGU_PEMBAYARAN)->count();
        $perluDikirimCount = Order::whereIn('status', [
            Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
            Order::STATUS_DESAIN_DISETUJUI,
            Order::STATUS_DICETAK,
            Order::STATUS_DIRAKIT_DAN_DIUJI,
        ])->count();
        $terkirimCount = Order::whereIn('status', [
            Order::STATUS_SIAP_DIAMBIL_DIKIRIM,
            Order::STATUS_SELESAI,
        ])->count();
        $totalQuantityOrdered = (int) Order::sum('quantity');

        $availableStockCount = $activationService->getAvailableStockCount();
        $isLowStock = $availableStockCount < OrderActivationService::LOW_STOCK_THRESHOLD;

        $metrics = [
            'total_pesanan' => $totalOrdersCount,
            'total_hardware' => $totalQuantityOrdered,
            'pending' => $pendingCount,
            'perlu_dikirim' => $perluDikirimCount,
            'terkirim' => $terkirimCount,
            'stok_tag_tersedia' => $availableStockCount,
            'is_low_stock' => $isLowStock,
        ];

        $availableStatuses = Order::allStatuses();

        return view('admin.orders', compact('metrics', 'orders', 'statusFilter', 'search', 'availableStatuses', 'availableStockCount', 'isLowStock'));
    }

    /**
     * Update order status to one of the 8 official SRS workflow stages.
     */
    public function updateStatus(Request $request, Order $order, OrderActivationService $activationService): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Order::allStatuses()))],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $previousStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        // Auto-record to FinancialEntry if order status is a paid status
        $paidStatuses = [
            Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
            Order::STATUS_DESAIN_DISETUJUI,
            Order::STATUS_DICETAK,
            Order::STATUS_DIRAKIT_DAN_DIUJI,
            Order::STATUS_SIAP_DIAMBIL_DIKIRIM,
            Order::STATUS_SELESAI,
        ];
        if (in_array($validated['status'], $paidStatuses, true)) {
            FinancialEntry::recordOrderPayment($order);
        }

        // Jika status diubah menjadi "pembayaran_dikonfirmasi", alokasikan tag random & kirim email aktivasi
        $activationNote = '';
        if ($validated['status'] === Order::STATUS_PEMBAYARAN_DIKONFIRMASI) {
            $activationResult = $activationService->processOrderConfirmation($order);
            $activationNote = ' '.$activationResult['message'];
        } else {
            // Evaluasi juga peringatan stok tag menipis ke admin
            $activationService->checkAndNotifyLowStock();
        }

        // Audit log status transition
        $statusLabel = Order::allStatuses()[$validated['status']] ?? $validated['status'];
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'changed_at' => now(),
            'note' => trim(($validated['note'] ?: ("Status diubah dari [{$previousStatus}] ke [{$statusLabel}] oleh administrator.")).$activationNote),
            'admin_id' => Auth::id() ?? 1,
        ]);

        AuditLog::record('update_order_status', "Status pesanan [{$order->order_no}] diubah dari [{$previousStatus}] ke [{$statusLabel}]");

        $flashMessage = "Status pesanan {$order->order_no} berhasil diperbarui ke: {$statusLabel}.".($activationNote ? " ({$activationNote})" : '');

        return back()->with('success', $flashMessage);
    }

    /**
     * Catat pesanan baru secara manual oleh Admin (Offline / Kasir / Pameran).
     */
    public function store(Request $request, OrderActivationService $activationService): RedirectResponse
    {
        // Deteksi salah ketik domain umum (misal: gmai.com -> gmail.com)
        $suggestedEmail = OrderActivationService::detectEmailDomainTypo((string) $request->input('email'));
        if ($suggestedEmail) {
            return back()->withErrors([
                'email' => "Apakah maksud Anda '{$suggestedEmail}'? Domain email terdeteksi salah ketik.",
            ])->withInput();
        }

        $emailRule = app()->environment('testing') ? 'email:rfc' : 'email:rfc,dns';

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:100'],
            'whatsapp' => ['required', 'string', 'min:8', 'max:30'],
            'email' => ['required', 'string', $emailRule, 'max:150'],
            'channel' => ['required', 'in:online,offline'],
            'delivery_method' => ['required', 'in:ambil,kirim'],
            'address' => ['nullable', 'string', 'max:500'],
            'shape' => ['required', 'string', 'max:50'],
            'size' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Order::allStatuses()))],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'customer_name.required' => 'Nama lengkap pelanggan wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'email.required' => 'Alamat email wajib diisi untuk pengiriman kode aktivasi.',
            'email.email' => 'Format alamat email tidak valid atau domain email tidak aktif. Pastikan email benar.',
        ]);

        $qty = (int) $validated['quantity'];
        $unitPrice = (float) $validated['unit_price'];
        $total = $qty * $unitPrice;

        $orderNo = Order::generateOrderNo();

        $order = Order::create([
            'order_no' => $orderNo,
            'channel' => $validated['channel'],
            'customer_name' => $validated['customer_name'],
            'whatsapp' => preg_replace('/\s+/', '', $validated['whatsapp']),
            'email' => strtolower(trim($validated['email'])),
            'delivery_method' => $validated['delivery_method'],
            'address' => (! empty($validated['address'])) ? $validated['address'] : 'Workshop FlexiTag Makassar',
            'shape' => $validated['shape'],
            'size' => $validated['size'],
            'color' => $validated['color'],
            'notes' => $validated['notes'] ?? null,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'total' => $total,
            'payment_method' => $validated['payment_method'],
            'status' => $validated['status'],
        ]);

        $statusLabel = Order::allStatuses()[$validated['status']] ?? $validated['status'];

        $initialNote = 'Pesanan offline/langsung dicatat oleh administrator ['.(Auth::user()->name ?? 'Admin')."]. Status awal: {$statusLabel}.";

        // Jika pesanan manual langsung disetel ke status "pembayaran_dikonfirmasi", alokasikan tag & kirim email sekarang
        if ($validated['status'] === Order::STATUS_PEMBAYARAN_DIKONFIRMASI) {
            $activationResult = $activationService->processOrderConfirmation($order);
            $initialNote .= ' '.$activationResult['message'];
        } else {
            $activationService->checkAndNotifyLowStock();
        }

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'changed_at' => now(),
            'note' => $initialNote,
            'admin_id' => Auth::id() ?? 1,
        ]);

        AuditLog::record('create_manual_order', "Admin mencatat pesanan manual [{$orderNo}] kanal {$validated['channel']}");

        if ($validated['status'] !== Order::STATUS_MENUNGGU_PEMBAYARAN && $validated['status'] !== Order::STATUS_DIBATALKAN) {
            FinancialEntry::recordOrderPayment($order);
        }

        // Kirim email konfirmasi ke pelanggan jika status awal belum langsung pembayaran dikonfirmasi
        if ($validated['status'] !== Order::STATUS_PEMBAYARAN_DIKONFIRMASI && ! empty($order->email) && filter_var($order->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($order->email)->send(new CustomerOrderConfirmationMail($order));
            } catch (\Throwable $e) {
                Log::warning("Gagal mengirim email konfirmasi pesanan manual ke {$order->email}: ".$e->getMessage());
            }
        }

        return back()->with('success', "Pesanan baru {$orderNo} ({$validated['customer_name']}) berhasil dicatat!");
    }

    /**
     * Kirim ulang email kode aktivasi ke pelanggan secara manual oleh admin.
     */
    public function resendActivation(Order $order, OrderActivationService $activationService): RedirectResponse
    {
        $result = $activationService->processOrderConfirmation($order);

        if ($result['success']) {
            AuditLog::record('resend_activation_email', "Admin mengirim ulang email aktivasi pesanan [{$order->order_no}] ke [{$order->email}]");

            return back()->with('success', "Email instruksi dan kode aktivasi berhasil dikirim ulang ke {$order->email}.");
        }

        return back()->with('error', "Gagal mengirimkan email aktivasi ke {$order->email}: {$result['message']}");
    }
}
