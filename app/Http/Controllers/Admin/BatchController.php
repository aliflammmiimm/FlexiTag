<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BatchController extends Controller
{
    /**
     * Display the Generate Tag page.
     */
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');

        $totalBatchCount = DB::table('tags')->whereNotNull('batch_id')->distinct()->count('batch_id');
        $totalTagCount = DB::table('tags')->count();

        $metrics = [
            'total_batch' => $totalBatchCount,
            'total_tags' => number_format($totalTagCount, 0, ',', '.'),
        ];

        // Query batches from database with live counts
        $query = DB::table('tags')
            ->whereNotNull('batch_id')
            ->select(
                'batch_id',
                DB::raw('count(*) as jumlah_tag'),
                DB::raw('sum(case when status = "aktif" then 1 else 0 end) as jumlah_aktif'),
                DB::raw('min(produced_at) as created_at')
            )
            ->groupBy('batch_id')
            ->orderByDesc('created_at');

        if ($filter === 'this_month') {
            $query->whereBetween('produced_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        $dbBatches = $query->get();

        if ($dbBatches->isNotEmpty()) {
            $batches = $dbBatches->map(function ($b, $idx) {
                return [
                    'id_batch' => $b->batch_id,
                    'edisi_label' => 'Toraja & Bugis Heritage',
                    'produk_label' => 'Ganci Pintar FlexiTag (NTAG215)',
                    'jumlah_tag' => (int) $b->jumlah_tag,
                    'jumlah_aktif' => (int) $b->jumlah_aktif,
                    'is_gold' => $idx === 0,
                    'tanggal_dibuat' => $b->created_at ? Carbon::parse($b->created_at)->translatedFormat('d M Y, H:i') : now()->translatedFormat('d M Y, H:i'),
                ];
            })->toArray();
        } else {
            $batches = [];
        }

        return view('admin.generate', compact('metrics', 'batches', 'filter', 'totalBatchCount'));
    }

    /**
     * Store and generate a new NFC Tag Batch with CSV export.
     */
    public function store(Request $request): StreamedResponse|RedirectResponse
    {
        $validated = $request->validate([
            'jenis_produk' => ['required', 'string'],
            'edisi_budaya' => ['required', 'string'],
            'jumlah_tag' => ['required', 'integer', 'in:10,50,100,250'],
            'prefix_kode' => ['required', 'string', 'max:20'],
        ]);

        $adminId = Auth::id();
        $count = (int) $validated['jumlah_tag'];
        $prefix = rtrim($validated['prefix_kode'], '-').'-';

        // Auto-generate next batch code sequentially
        $existingBatchIds = DB::table('tags')->whereNotNull('batch_id')->distinct()->pluck('batch_id');
        $maxNum = 880;
        foreach ($existingBatchIds as $bId) {
            if (preg_match('/#B-(\d+)/', (string) $bId, $matches)) {
                $maxNum = max($maxNum, (int) $matches[1]);
            }
        }
        $nextNumber = $maxNum + 1;
        $batchCode = '#B-'.$nextNumber;

        // Generate N tags and prepare CSV data
        $tagsData = [];
        $csvRows = [];
        $csvRows[] = ['No', 'ID_Batch', 'Tag_Code', 'Kode_Aktivasi_PlainText', 'Status', 'Jenis_Produk', 'Edisi_Budaya', 'URL_Publik_Tag'];

        $existingTagCodes = [];

        for ($i = 1; $i <= $count; $i++) {
            // a. Cryptographically random alphanumeric tag_code (10 characters: non-sequential)
            do {
                $tagCode = 'FLX-'.strtoupper(Str::random(7)); // Total 11 chars (fits VARCHAR 12)
            } while (isset($existingTagCodes[$tagCode]) || DB::table('tags')->where('tag_code', $tagCode)->exists());
            $existingTagCodes[$tagCode] = true;

            // b. Plain-text activation code: format sederhana & mudah diketik (contoh: KD-8821-XQ)
            $plainActivationCode = 'KD-'.rand(1000, 9999).'-'.strtoupper(Str::random(2));

            $tapUrl = url('/u/'.$tagCode);

            $tagsData[] = [
                'tag_code' => $tagCode,
                'activation_code' => $plainActivationCode,
                'user_id' => null,
                'status' => 'belum_aktif',
                'disabled_by' => null,
                'batch_id' => $batchCode,
                'produced_at' => now(),
                'activated_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Plain-text activation code only present in downloaded CSV
            $csvRows[] = [
                $i,
                $batchCode,
                $tagCode,
                $plainActivationCode,
                'belum_aktif',
                $validated['jenis_produk'],
                $validated['edisi_budaya'],
                $tapUrl,
            ];
        }

        // Bulk insert generated tags into tags table
        foreach (array_chunk($tagsData, 100) as $chunk) {
            DB::table('tags')->insert($chunk);
        }

        // Record administrative audit trail (FR-ADM-10)
        AuditLog::record('generate_batch', "Batch {$batchCode} ({$count} tag) produk '{$validated['jenis_produk']}' di-generate oleh admin");

        // Stream and download the CSV
        $filename = 'FlexiTag_'.str_replace('#', '', $batchCode).'_'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($csvRows) {
            $handle = fopen('php://output', 'w');
            foreach ($csvRows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
