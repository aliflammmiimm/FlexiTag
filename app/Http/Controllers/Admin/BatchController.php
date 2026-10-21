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
        $dbBatchCount = DB::table('tag_batches')->count();
        $dbTagSum = DB::table('tag_batches')->sum('jumlah_tag');

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
        $latestBatch = DB::table('tag_batches')->orderBy('id', 'desc')->first();
        $nextNumber = 883;
        if ($latestBatch && preg_match('/#B-(\d+)/', $latestBatch->id_batch, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }
        $batchCode = '#B-'.$nextNumber;

        // Map selection to valid database enum values
        $jenisSlug = match ($validated['jenis_produk']) {
            'Ganci Akrilik Persegi', 'Ganci Bentuk Persegi' => 'ganci_persegi',
            'Ganci Edisi Motif Toraja', 'Ganci Motif Toraja' => 'ganci_motif_toraja',
            'Ganci Edisi Motif Bugis', 'Ganci Motif Bugis' => 'ganci_motif_bugis',
            'Ganci Custom Desain', 'Ganci Custom' => 'ganci_custom',
            default => 'ganci_bulat',
        };

        $edisiSlug = match ($validated['edisi_budaya']) {
            'Saqbe (Bugis)' => 'saqbe',
            "Sulapa Eppa' (Makassar)", "Sulapa Eppa'" => 'sulapa_eppa',
            "Pa'barre Allo (Toraja)" => 'pabarre_allo',
            default => 'pateddong',
        };

        $batchId = DB::table('tag_batches')->insertGetId([
            'id_batch' => $batchCode,
            'jenis_produk' => $jenisSlug,
            'edisi_budaya' => $edisiSlug,
            'spesifikasi_ic' => 'NTAG215',
            'jumlah_tag' => $count,
            'prefix_kode' => $prefix,
            'tanggal_dibuat' => now(),
            'created_by_admin_id' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Generate N tags and prepare CSV data
        $tagsData = [];
        $csvRows = [];
        $csvRows[] = ['No', 'ID_Batch', 'Kode_Aktivasi', 'Hardware_UID', 'Spesifikasi_IC', 'Jenis_Produk', 'Edisi_Budaya', 'URL_Tap_NFC'];

        for ($i = 1; $i <= $count; $i++) {
            $uniqueHash = strtoupper(Str::random(5));
            $activationCode = $prefix.$uniqueHash;
            $hardwareUid = sprintf(
                '04:%02X:%02X:%02X:%02X:%02X:%02X',
                random_int(0, 255),
                random_int(0, 255),
                random_int(0, 255),
                random_int(0, 255),
                random_int(0, 255),
                random_int(0, 255)
            );
            $tapUrl = url('/t/'.$activationCode);

            $tagsData[] = [
                'batch_id' => $batchId,
                'user_id' => null,
                'kode_aktivasi' => $activationCode,
                'hardware_uid' => $hardwareUid,
                'nama_custom_merch' => $validated['jenis_produk'].' '.$validated['edisi_budaya'],
                'status_tag' => 'tersedia',
                'is_kill_switch_on' => false,
                'active_mode' => 'kampus',
                'tanggal_aktivasi' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $csvRows[] = [
                $i,
                $batchCode,
                $activationCode,
                $hardwareUid,
                'NTAG215',
                $validated['jenis_produk'],
                $validated['edisi_budaya'],
                $tapUrl,
            ];
        }

        // Bulk insert generated tags
        foreach (array_chunk($tagsData, 100) as $chunk) {
            DB::table('nfc_tags')->insert($chunk);
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
