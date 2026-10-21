<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Users
        $saparudding = User::updateOrCreate(
            ['email' => 'saparudding@flexitag.id'],
            [
                'name' => 'Saparudding',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'jabatan_admin' => 'Ops Lead Makassar',
                'no_whatsapp' => '081234567891',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@flexitag.id'],
            [
                'name' => 'Saparudding',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'jabatan_admin' => 'Ops Lead Makassar',
                'no_whatsapp' => '081234567890',
            ]
        );

        // 2. Sample Tag Batch
        $batchId = DB::table('tag_batches')->insertGetId([
            'id_batch' => '#B-882',
            'jenis_produk' => 'ganci_persegi',
            'edisi_budaya' => 'pateddong',
            'spesifikasi_ic' => 'NTAG215',
            'jumlah_tag' => 200,
            'prefix_kode' => 'FLX',
            'tanggal_dibuat' => now(),
            'created_by_admin_id' => $saparudding->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Sample NFC Tags
        $tagIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $tagIds[] = DB::table('nfc_tags')->insertGetId([
                'batch_id' => $batchId,
                'user_id' => $saparudding->id,
                'kode_aktivasi' => 'FLX-TAG'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'hardware_uid' => '04:A2:3F:8C:9B:10:'.str_pad($i, 2, '0', STR_PAD_LEFT),
                'nama_custom_merch' => 'Merchandise Sulsel '.$i,
                'status_tag' => ($i % 3 === 0) ? 'tersedia' : 'aktif',
                'is_kill_switch_on' => false,
                'active_mode' => match ($i % 4) {
                    0 => 'kampus',
                    1 => 'umkm',
                    2 => 'kerja',
                    default => 'hilang',
                },
                'tanggal_aktivasi' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Sample Orders matching ganci-only variations
        $sampleOrders = [
            [
                'id_pesanan' => '#ORD-048',
                'nama_pembeli' => 'Budi Santoso',
                'kota_pembeli' => 'Makassar',
                'no_wa_pembeli' => '081234567801',
                'jenis_produk_dipesan' => 'ganci_persegi',
                'edisi_budaya' => 'pateddong',
                'tag_id' => $tagIds[0] ?? null,
                'total_harga' => 125000,
                'status_pengiriman' => 'dikirim',
                'tanggal_pesanan' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_pesanan' => '#ORD-047',
                'nama_pembeli' => 'Nurul Aisyah',
                'kota_pembeli' => 'Gowa',
                'no_wa_pembeli' => '081234567802',
                'jenis_produk_dipesan' => 'ganci_bulat',
                'edisi_budaya' => 'saqbe',
                'tag_id' => null, // Belum aktif
                'total_harga' => 95000,
                'status_pengiriman' => 'pending',
                'tanggal_pesanan' => now()->subHours(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_pesanan' => '#ORD-046',
                'nama_pembeli' => 'Rahmat Hidayat',
                'kota_pembeli' => 'Maros',
                'no_wa_pembeli' => '081234567803',
                'jenis_produk_dipesan' => 'ganci_motif_toraja',
                'edisi_budaya' => 'sulapa_eppa',
                'tag_id' => $tagIds[1] ?? null,
                'total_harga' => 150000,
                'status_pengiriman' => 'selesai',
                'tanggal_pesanan' => now()->subHours(6),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_pesanan' => '#ORD-045',
                'nama_pembeli' => 'Dewi Sartika',
                'kota_pembeli' => 'Makassar',
                'no_wa_pembeli' => '081234567804',
                'jenis_produk_dipesan' => 'ganci_bulat',
                'edisi_budaya' => 'pabarre_allo',
                'tag_id' => $tagIds[2] ?? null,
                'total_harga' => 125000,
                'status_pengiriman' => 'dikirim',
                'tanggal_pesanan' => now()->subHours(8),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_pesanan' => '#ORD-044',
                'nama_pembeli' => 'Fajar Pratama',
                'kota_pembeli' => 'Parepare',
                'no_wa_pembeli' => '081234567805',
                'jenis_produk_dipesan' => 'ganci_custom',
                'edisi_budaya' => 'saqbe',
                'tag_id' => null, // Belum aktif
                'total_harga' => 110000,
                'status_pengiriman' => 'pending',
                'tanggal_pesanan' => now()->subHours(12),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($sampleOrders as $order) {
            DB::table('orders')->updateOrInsert(
                ['id_pesanan' => $order['id_pesanan']],
                $order
            );
        }
    }
}
