<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display the NFC Tags Inventory list.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search', '');

        // Summary Metric Baseline (matching design)
        $metrics = [
            'total_diproduksi' => 500,
            'tag_terhubung' => 124,
            'rasio_aktif' => '24.8%',
            'tag_stok' => 376,
            'tag_diblokir' => 0,
        ];

        // Raw items matching design specifications
        $tags = [
            [
                'id' => 1,
                'kode_aktivasi' => 'FLX-8X9Y2Z',
                'hardware_uid' => '04:A2:3F:89:1E',
                'batch_id' => '#B-882',
                'edisi' => "Pa'teddong",
                'sub_edisi' => 'Toraja Heritage Series',
                'status' => 'aktif',
                'pemilik' => [
                    'nama' => 'Andi Muhammad',
                    'initials' => 'AM',
                    'lokasi' => 'Kab. Gowa, Sul-Sel',
                ],
                'mode' => 'kampus',
                'is_kill_switch_on' => false,
            ],
            [
                'id' => 2,
                'kode_aktivasi' => 'FLX-9A2B4C',
                'hardware_uid' => '04:B8:5C:21:40',
                'batch_id' => '#B-881',
                'edisi' => 'Saqbe',
                'sub_edisi' => 'Bugis Silk Weave Edition',
                'status' => 'aktif',
                'pemilik' => [
                    'nama' => 'Siti Rahmawati',
                    'initials' => 'SR',
                    'lokasi' => 'Panakkukang, Makassar',
                ],
                'mode' => 'umkm',
                'is_kill_switch_on' => false,
            ],
            [
                'id' => 3,
                'kode_aktivasi' => 'FLX-3K7M9P',
                'hardware_uid' => '04:C4:72:EE:9A',
                'batch_id' => '#B-880',
                'edisi' => "Pa'barre Allo",
                'sub_edisi' => 'Toraja Sunburst Brass Edition',
                'status' => 'tersedia',
                'pemilik' => null,
                'mode' => null,
                'is_kill_switch_on' => false,
            ],
            [
                'id' => 4,
                'kode_aktivasi' => 'FLX-5T1W8Q',
                'hardware_uid' => '04:D1:AA:67:B2',
                'batch_id' => '#B-879',
                'edisi' => "Sulapa Eppa'",
                'sub_edisi' => 'Bugis Geometric Philosophy',
                'status' => 'aktif',
                'pemilik' => [
                    'nama' => 'Budi Hartono',
                    'initials' => 'BH',
                    'lokasi' => 'Turikale, Kab. Maros',
                ],
                'mode' => 'kerja',
                'is_kill_switch_on' => false,
            ],
            [
                'id' => 5,
                'kode_aktivasi' => 'FLX-2R4S6T',
                'hardware_uid' => '04:E5:10:99:3F',
                'batch_id' => '#B-878',
                'edisi' => "Pa'teddong",
                'sub_edisi' => 'Toraja Heritage Series',
                'status' => 'tersedia',
                'pemilik' => null,
                'mode' => null,
                'is_kill_switch_on' => false,
            ],
            [
                'id' => 6,
                'kode_aktivasi' => 'FLX-7D9F1G',
                'hardware_uid' => '04:FA:21:40:02',
                'batch_id' => '#B-877',
                'edisi' => 'Saqbe',
                'sub_edisi' => 'Bugis Silk Weave Edition',
                'status' => 'tersedia',
                'pemilik' => null,
                'mode' => null,
                'is_kill_switch_on' => false,
            ],
        ];

        // Filter by Status if specified
        if ($statusFilter !== 'all') {
            $tags = array_values(array_filter($tags, function ($tag) use ($statusFilter) {
                if ($statusFilter === 'aktif') {
                    return $tag['status'] === 'aktif';
                }
                if ($statusFilter === 'belum_aktif') {
                    return $tag['status'] === 'tersedia';
                }
                if ($statusFilter === 'diblokir') {
                    return $tag['status'] === 'diblokir';
                }

                return true;
            }));
        }

        // Filter by Search if specified
        if (! empty($search)) {
            $searchTerm = strtolower($search);
            $tags = array_values(array_filter($tags, function ($tag) use ($searchTerm) {
                $codeMatch = str_contains(strtolower($tag['kode_aktivasi']), $searchTerm);
                $uidMatch = str_contains(strtolower($tag['hardware_uid']), $searchTerm);
                $ownerMatch = $tag['pemilik'] && str_contains(strtolower($tag['pemilik']['nama']), $searchTerm);

                return $codeMatch || $uidMatch || $ownerMatch;
            }));
        }

        return view('admin.tags', compact('metrics', 'tags', 'statusFilter', 'search'));
    }
}
