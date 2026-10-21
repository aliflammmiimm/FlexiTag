<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display the Orders management list.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search', '');

        $metrics = [
            'total_hardware' => 500,
            'tag_aktif' => 124,
            'perlu_dikirim' => 12,
            'total_pesanan' => 48,
            'terkirim' => 32,
        ];

        // Orders exclusively for Ganci variants (Bulat, Persegi, Motif Bugis, Motif Toraja, Custom)
        $orders = [
            [
                'id_pesanan' => '#ORD-001',
                'nama_pembeli' => 'Budi Santoso',
                'kota' => 'Makassar',
                'jenis_produk' => "Ganci Akrilik Persegi – Pa'teddong (Toraja)",
                'bentuk_ganci' => 'Persegi',
                'status_tag' => 'aktif',
                'pengiriman' => 'dikirim',
            ],
            [
                'id_pesanan' => '#ORD-002',
                'nama_pembeli' => 'Nurul Aisyah',
                'kota' => 'Gowa',
                'jenis_produk' => 'Ganci Kulit Bulat – Motif Saqbe (Bugis)',
                'bentuk_ganci' => 'Bulat',
                'status_tag' => 'aktif',
                'pengiriman' => 'selesai',
            ],
            [
                'id_pesanan' => '#ORD-003',
                'nama_pembeli' => 'Rahmat Hidayat',
                'kota' => 'Maros',
                'jenis_produk' => 'Ganci Kayu Persegi – Ukir Khas Toraja',
                'bentuk_ganci' => 'Persegi',
                'status_tag' => 'belum_aktif',
                'pengiriman' => 'pending',
            ],
            [
                'id_pesanan' => '#ORD-004',
                'nama_pembeli' => 'Dewi Sartika',
                'kota' => 'Makassar',
                'jenis_produk' => "Ganci Bulat – Pa'barre Allo (Toraja)",
                'bentuk_ganci' => 'Bulat',
                'status_tag' => 'aktif',
                'pengiriman' => 'dikirim',
            ],
            [
                'id_pesanan' => '#ORD-005',
                'nama_pembeli' => 'Fajar Pratama',
                'kota' => 'Parepare',
                'jenis_produk' => 'Ganci Karet Persegi – Motif Saqbe (Bugis)',
                'bentuk_ganci' => 'Persegi',
                'status_tag' => 'belum_aktif',
                'pengiriman' => 'pending',
            ],
            [
                'id_pesanan' => '#ORD-006',
                'nama_pembeli' => 'Siti Nurhaliza',
                'kota' => 'Bone',
                'jenis_produk' => 'Ganci Custom Akrilik – Edisi Tenun Lagosi',
                'bentuk_ganci' => 'Custom',
                'status_tag' => 'aktif',
                'pengiriman' => 'selesai',
            ],
        ];

        // Filter by Delivery Status
        if ($statusFilter !== 'all') {
            $orders = array_values(array_filter($orders, function ($order) use ($statusFilter) {
                if ($statusFilter === 'perlu_dikirim') {
                    return $order['pengiriman'] === 'pending';
                }
                if ($statusFilter === 'terkirim') {
                    return in_array($order['pengiriman'], ['dikirim', 'selesai']);
                }

                return true;
            }));
        }

        // Filter by Search Query
        if (! empty($search)) {
            $searchTerm = strtolower($search);
            $orders = array_values(array_filter($orders, function ($order) use ($searchTerm) {
                $idMatch = str_contains(strtolower($order['id_pesanan']), $searchTerm);
                $nameMatch = str_contains(strtolower($order['nama_pembeli']), $searchTerm);
                $productMatch = str_contains(strtolower($order['jenis_produk']), $searchTerm);

                return $idMatch || $nameMatch || $productMatch;
            }));
        }

        return view('admin.orders', compact('metrics', 'orders', 'statusFilter', 'search'));
    }
}
