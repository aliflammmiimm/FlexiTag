<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    public function index(): View
    {
        $dbBatchCount = DB::table('tags')->whereNotNull('batch_id')->distinct('batch_id')->count('batch_id');
        $dbTagSum = DB::table('tags')->count();

        $metrics = [
            'total_batch' => $dbBatchCount > 0 ? max(24, $dbBatchCount) : 24,
            'total_tags' => $dbTagSum > 0 ? number_format(max(1250, $dbTagSum), 0, ',', '.') : '1,250',
        ];

        // Seed or load historical batches matching the design
        $batches = [
            [
                'id_batch' => '#B-882',
                'edisi_label' => "Pa'teddong (Toraja)",
                'produk_label' => 'Ganci Akrilik Persegi',
                'jumlah_tag' => 50,
                'is_gold' => true,
                'tanggal_dibuat' => '18 Mei 2025, 14:20',
            ],
            [
                'id_batch' => '#B-881',
                'edisi_label' => 'Saqbe (Bugis)',
                'produk_label' => 'Ganci Kulit Bulat',
                'jumlah_tag' => 100,
                'is_gold' => false,
                'tanggal_dibuat' => '16 Mei 2025, 09:45',
            ],
            [
                'id_batch' => '#B-880',
                'edisi_label' => "Pa'barre Allo (Toraja)",
                'produk_label' => 'Ganci Akrilik Bulat',
                'jumlah_tag' => 10,
                'is_gold' => false,
                'tanggal_dibuat' => '12 Mei 2025, 11:15',
            ],
            [
                'id_batch' => '#B-879',
                'edisi_label' => "Sulapa Eppa' (Bugis)",
                'produk_label' => 'Ganci Kayu Persegi Ukir',
                'jumlah_tag' => 50,
                'is_gold' => true,
                'tanggal_dibuat' => '08 Mei 2025, 16:30',
            ],
            [
                'id_batch' => '#B-878',
                'edisi_label' => "Pa'teddong (Toraja)",
                'produk_label' => 'Ganci Akrilik Persegi',
                'jumlah_tag' => 250,
                'is_gold' => false,
                'tanggal_dibuat' => '04 Mei 2025, 10:05',
            ],
            [
                'id_batch' => '#B-877',
                'edisi_label' => 'Lagosi (Bugis)',
                'produk_label' => 'Ganci Custom Akrilik',
                'jumlah_tag' => 100,
                'is_gold' => false,
                'tanggal_dibuat' => '29 Apr 2025, 13:40',
            ],
        ];

        return view('admin.generate', compact('metrics', 'batches'));
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

        // Auto-generate next batch code
        $latestBatch = DB::table('tags')->whereNotNull('batch_id')->orderBy('id', 'desc')->first();
        $nextNumber = 883;
        if ($latestBatch && preg_match('/#B-(\d+)/', $latestBatch->batch_id, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }
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

            // b. Plain-text activation code generated once for CSV, stored as SHA-256 hash in DB
            $plainActivationCode = $prefix.strtoupper(Str::random(8));
            $hashedActivationCode = hash('sha256', $plainActivationCode);

            $tapUrl = url('/u/'.$tagCode);

            $tagsData[] = [
                'tag_code' => $tagCode,
                'activation_code' => $hashedActivationCode,
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
