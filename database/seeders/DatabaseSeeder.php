<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Administrators & Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@flexitag.id'],
            [
                'name' => 'Saparudding',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'jabatan_admin' => 'Ops Lead Makassar',
                'no_whatsapp' => '081234567890',
                'is_active' => true,
                'two_factor_enabled' => false,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'saparudding@flexitag.id'],
            [
                'name' => 'Saparudding',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'jabatan_admin' => 'Ops Lead Makassar',
                'no_whatsapp' => '081234567891',
                'is_active' => true,
                'two_factor_enabled' => false,
                'email_verified_at' => now(),
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'budi.santoso@example.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'user',
                'jabatan_admin' => null,
                'no_whatsapp' => '081234567801',
                'is_active' => true,
                'two_factor_enabled' => false,
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Profile (1:1 with users)
        DB::table('profiles')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'display_name' => 'Budi Santoso, S.Kom',
                'headline' => 'Software Engineer & Tech Enthusiast',
                'bio' => 'Mahasiswa Pascasarjana Sistem Informasi & Founder Kedai Kopi Celebes. Suka mengeksplorasi IoT dan Web Development.',
                'photo_path' => 'uploads/profiles/budi.jpg',
                'theme' => 'sulawesi_heritage',
                'active_mode' => 'kampus',
                'username' => 'budisantoso',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Seed Privacy Settings
        DB::table('privacy_settings')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'show_phone' => true,
                'show_email' => true,
                'show_location' => false,
                'indexable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Seed mode_data for all 4 modes (kampus, kerja, umkm, hilang)
        $modeDataSamples = [
            [
                'user_id' => $user->id,
                'mode' => 'kampus',
                'data' => json_encode([
                    'universitas' => 'Universitas Hasanuddin',
                    'fakultas' => 'Teknik',
                    'program_studi' => 'Teknik Informatika',
                    'nim' => 'D121211050',
                    'angkatan' => 2021,
                    'status_akademik' => 'Aktif',
                    'organisasi' => [
                        ['nama' => 'Himpunan Mahasiswa Informatika', 'jabatan' => 'Ketua Divisi Litbang', 'periode' => '2023-2024'],
                        ['nama' => 'GDG on Campus Unhas', 'jabatan' => 'Core Team Member', 'periode' => '2024'],
                    ],
                    'portofolio_riset' => 'https://scholar.google.com/citations?user=sample',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'mode' => 'kerja',
                'data' => json_encode([
                    'perusahaan' => 'PT Celebes Digital Solusindo',
                    'posisi' => 'Full-Stack Developer',
                    'industri' => 'Teknologi Informasi',
                    'lokasi_kantor' => 'Makassar, Sulawesi Selatan',
                    'cv_file_path' => 'uploads/cv/budi_santoso_cv.pdf',
                    'linkedin_url' => 'https://linkedin.com/in/budisantoso',
                    'github_url' => 'https://github.com/budisantoso',
                    'keahlian' => ['Laravel', 'Vue.js', 'MySQL', 'TailwindCSS', 'RESTful API', 'Docker'],
                    'open_to_work' => true,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'mode' => 'umkm',
                'data' => json_encode([
                    'nama_usaha' => 'Kedai Kopi Celebes',
                    'tagline' => 'Aroma Khas Kopi Toraja Asli di Jantung Kota',
                    'kategori' => 'Food & Beverages',
                    'alamat_toko' => 'Jl. Boulevard No. 45, Panakkukang, Makassar',
                    'whatsapp_order' => '6281234567890',
                    'shopee_url' => 'https://shopee.co.id/kedaicelebes',
                    'katalog_produk' => [
                        [
                            'id' => 1,
                            'nama' => 'Kopi Arabika Toraja Sapan (250g)',
                            'harga' => 65000,
                            'deskripsi' => 'Single origin Toraja Sapan dengan aroma floral dan fruity note yang seimbang.',
                            'foto_path' => 'uploads/products/kopi_toraja.jpg',
                            'is_ready' => true,
                        ],
                        [
                            'id' => 2,
                            'nama' => 'Kopi Robusta Enrekang Duri (250g)',
                            'harga' => 45000,
                            'deskripsi' => 'Robusta khas pegunungan Enrekang dengan body tebal dan sentuhan dark chocolate.',
                            'foto_path' => 'uploads/products/kopi_enrekang.jpg',
                            'is_ready' => true,
                        ],
                        [
                            'id' => 3,
                            'nama' => 'Cold Brew Celebes Signature (250ml)',
                            'harga' => 25000,
                            'deskripsi' => 'Cold brew 16 jam perendaman, siap minum dingin menyegarkan.',
                            'foto_path' => 'uploads/products/cold_brew.jpg',
                            'is_ready' => true,
                        ],
                    ],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'mode' => 'hilang',
                'data' => json_encode([
                    'nama_barang' => 'Gantungan Kunci & Tas Ransel Kulit Budi',
                    'pesan_darurat' => 'Halo! Jika Anda menemukan gantungan kunci/tas ini, mohon kesediaannya menghubungi nomor darurat saya. Ada dokumen skripsi berharga di dalamnya. Terima kasih banyak atas kebaikan Anda.',
                    'kontak_darurat_1' => '081234567890 (Budi)',
                    'kontak_darurat_2' => '082198765432 (Istri/Keluarga)',
                    'bounty_reward' => 'Uang tunai Rp 150.000 sebagai tanda terima kasih',
                    'instruksi_pengembalian' => 'Dapat dititipkan di Pos Security Kampus FT Unhas Gowa atau hubungi via WhatsApp.',
                    'show_finder_form' => true,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($modeDataSamples as $item) {
            DB::table('mode_data')->updateOrInsert(
                ['user_id' => $item['user_id'], 'mode' => $item['mode']],
                $item
            );
        }

        // 5. Seed Tags
        $sampleTags = [
            [
                'tag_code' => 'FLX-8X9Y2Z',
                'activation_code' => 'ACT-882-98234',
                'user_id' => $user->id,
                'status' => 'aktif',
                'disabled_by' => null,
                'batch_id' => '#B-882',
                'produced_at' => now()->subMonths(1),
                'activated_at' => now()->subDays(15),
                'created_at' => now()->subMonths(1),
                'updated_at' => now()->subDays(15),
            ],
            [
                'tag_code' => 'FLX-9A2B4C',
                'activation_code' => 'ACT-881-12345',
                'user_id' => $user->id,
                'status' => 'aktif',
                'disabled_by' => null,
                'batch_id' => '#B-881',
                'produced_at' => now()->subMonths(1),
                'activated_at' => now()->subDays(10),
                'created_at' => now()->subMonths(1),
                'updated_at' => now()->subDays(10),
            ],
            [
                'tag_code' => 'FLX-KM-001',
                'activation_code' => 'ACT-880-99887',
                'user_id' => null,
                'status' => 'belum_aktif',
                'disabled_by' => null,
                'batch_id' => '#B-880',
                'produced_at' => now()->subWeeks(2),
                'activated_at' => null,
                'created_at' => now()->subWeeks(2),
                'updated_at' => now()->subWeeks(2),
            ],
        ];

        $tagId = null;
        foreach ($sampleTags as $tag) {
            $id = DB::table('tags')->updateOrInsert(
                ['tag_code' => $tag['tag_code']],
                $tag
            );
            if (! $tagId) {
                $tagId = DB::table('tags')->where('tag_code', $tag['tag_code'])->value('id');
            }
        }

        // 6. Seed Links
        $linkId = DB::table('links')->updateOrInsert(
            ['user_id' => $user->id, 'url' => 'https://budisantoso.dev'],
            [
                'type' => 'website',
                'label' => 'Website Portofolio',
                'sort_order' => 1,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $linkRecordId = DB::table('links')->where('user_id', $user->id)->value('id');

        if ($linkRecordId) {
            DB::table('link_clicks')->insertOrIgnore([
                'link_id' => $linkRecordId,
                'clicked_at' => now()->subHours(3),
            ]);
        }

        // 7. Seed Taps
        if ($tagId) {
            DB::table('taps')->insertOrIgnore([
                'tag_id' => $tagId,
                'tapped_at' => now()->subHours(2),
                'source' => 'nfc',
                'device_type' => 'Android Smartphone',
                'region' => 'Makassar, Indonesia',
            ]);
        }

        // 8. Seed Designs (Katalog Ganci)
        $designs = [
            [
                'name' => 'Gantungan Kunci Edisi Motif Toraja Pa\'ssulan',
                'category' => 'motif_lokal',
                'image_path' => 'designs/ganci_toraja.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gantungan Kunci Edisi Motif Bugis Saoraja',
                'category' => 'motif_lokal',
                'image_path' => 'designs/ganci_bugis.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gantungan Kunci Bulat Akrilik Minimalis',
                'category' => 'pribadi',
                'image_path' => 'designs/ganci_bulat.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gantungan Kunci Persegi Branding UMKM',
                'category' => 'umkm',
                'image_path' => 'designs/ganci_umkm.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($designs as $design) {
            DB::table('designs')->updateOrInsert(
                ['name' => $design['name']],
                $design
            );
        }

        // 9. Seed Orders & Status Logs
        $sampleOrders = [
            [
                'order_no' => 'ORD-2026-0001',
                'customer_name' => 'Andi Muhammad Rifki',
                'whatsapp' => '081345678912',
                'email' => 'andi.rifki@gmail.com',
                'delivery_method' => 'ambil',
                'address' => 'Workshop FlexiTag Makassar',
                'shape' => 'Ganci Bulat',
                'size' => 'Diameter 4.5 cm',
                'color' => 'Hitam Doff',
                'notes' => 'Custom grafir logo komunitas di bagian belakang chip NFC',
                'quantity' => 2,
                'unit_price' => 35000.00,
                'total' => 70000.00,
                'status' => 'dirakit_dan_diuji',
                'design_file_path' => 'orders/designs/logo_komunitas.ai',
                'payment_proof_path' => 'orders/payments/struk_transfer_0001.jpg',
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
            [
                'order_no' => 'ORD-2026-0002',
                'customer_name' => 'Budi Santoso',
                'whatsapp' => '081234567801',
                'email' => 'budi.santoso@example.com',
                'delivery_method' => 'kirim',
                'address' => 'Jl. Perintis Kemerdekaan KM 10, Tamalanrea, Makassar',
                'shape' => 'Ganci Persegi',
                'size' => '5 x 3 cm',
                'color' => 'Cokelat Kayu',
                'notes' => 'Edisi Pa\'teddong Toraja Heritage',
                'quantity' => 1,
                'unit_price' => 45000.00,
                'total' => 45000.00,
                'status' => 'siap_diambil_dikirim',
                'design_file_path' => null,
                'payment_proof_path' => 'orders/payments/struk_0002.jpg',
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ],
            [
                'order_no' => 'ORD-2026-0003',
                'customer_name' => 'Nurul Aisyah',
                'whatsapp' => '081234567802',
                'email' => 'nurul.aisyah@gmail.com',
                'delivery_method' => 'kirim',
                'address' => 'Jl. Sultan Hasanuddin No. 12, Gowa',
                'shape' => 'Ganci Bulat',
                'size' => 'Diameter 4.5 cm',
                'color' => 'Merah Marun',
                'notes' => 'Motif Saqbe Bugis Silk Edition',
                'quantity' => 1,
                'unit_price' => 40000.00,
                'total' => 40000.00,
                'status' => 'menunggu_pembayaran',
                'design_file_path' => null,
                'payment_proof_path' => null,
                'created_at' => now()->subHours(6),
                'updated_at' => now(),
            ],
        ];

        foreach ($sampleOrders as $ord) {
            DB::table('orders')->updateOrInsert(
                ['order_no' => $ord['order_no']],
                $ord
            );
        }

        $orderId = DB::table('orders')->where('order_no', 'ORD-2026-0001')->value('id');
        if ($orderId) {
            DB::table('order_status_logs')->insertOrIgnore([
                [
                    'order_id' => $orderId,
                    'status' => 'menunggu_pembayaran',
                    'changed_at' => now()->subDays(2),
                    'note' => 'Pesanan baru dibuat oleh pelanggan melalui form checkout.',
                    'admin_id' => $admin->id,
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ],
                [
                    'order_id' => $orderId,
                    'status' => 'pembayaran_dikonfirmasi',
                    'changed_at' => now()->subDays(2)->addHours(2),
                    'note' => 'Bukti transfer via BCA telah diverifikasi.',
                    'admin_id' => $admin->id,
                    'created_at' => now()->subDays(2)->addHours(2),
                    'updated_at' => now()->subDays(2)->addHours(2),
                ],
                [
                    'order_id' => $orderId,
                    'status' => 'desain_disetujui',
                    'changed_at' => now()->subDays(1),
                    'note' => 'Mockup grafir logo disetujui pemesan.',
                    'admin_id' => $admin->id,
                    'created_at' => now()->subDays(1),
                    'updated_at' => now()->subDays(1),
                ],
                [
                    'order_id' => $orderId,
                    'status' => 'dirakit_dan_diuji',
                    'changed_at' => now()->subHours(4),
                    'note' => 'Chip NFC NTAG215 berhasil diprogram dan dites respon URL.',
                    'admin_id' => $admin->id,
                    'created_at' => now()->subHours(4),
                    'updated_at' => now()->subHours(4),
                ],
            ]);
        }

        // 10. Seed Settings (SRS Key-Value format & Admin Console configurations)
        $defaultSettings = [
            'app_name' => 'FlexiTag Smart Merchandise',
            'contact_whatsapp' => '628114400012',
            'contact_email' => 'support@flexitag.id',
            'production_lead_time_days' => '3',
            'currency_symbol' => 'Rp',
            'base_ganci_price' => '35000',
            'toraja_edition_surcharge' => '5000',
            'bugis_edition_surcharge' => '5000',
            'maintenance_mode' => 'false',
            // Admin console setting keys
            'no_wa_cs_pusat' => '+62 812-3456-7890',
            'relay_wa_otomatis_aktif' => '1',
            'notifikasi_email_pesanan' => '1',
            'mode_maintenance' => '0',
        ];

        foreach ($defaultSettings as $key => $val) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $val, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 11. Seed Financial Entries
        DB::table('financial_entries')->insertOrIgnore([
            [
                'type' => 'pemasukan',
                'amount' => 70000.00,
                'category' => 'Penjualan Ganci Custom',
                'entry_date' => now()->subDays(2)->toDateString(),
                'note' => 'Pembayaran pesanan ORD-2026-0001 (Andi Rifki)',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'type' => 'pengeluaran',
                'amount' => 250000.00,
                'category' => 'Bahan Baku & Chip NFC',
                'entry_date' => now()->subDays(10)->toDateString(),
                'note' => 'Pembelian 50 pcs chip NFC NTAG213 dan ring gantungan kunci',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
        ]);

        // 12. Seed Messages (FR-ADM-09: Abuse Reports, Contact Messages, Lost Items)
        DB::table('messages')->insertOrIgnore([
            [
                'type' => 'abuse_report',
                'user_id' => $user->id,
                'sender_name' => 'Warga Kampus Peduli',
                'location' => 'Tamalanrea, Makassar',
                'message' => 'Laporan indikasi konten profil tag publik memuat tautan mencurigakan pada tautan sosial media.',
                'contact' => '081299887766',
                'read_at' => null, // Unread to demonstrate FR-ADM-09 red dot
                'created_at' => now()->subMinutes(45),
            ],
            [
                'type' => 'contact',
                'user_id' => null,
                'sender_name' => 'Fadli Rahman (Ketua Panitia Wisuda)',
                'location' => 'Makassar',
                'message' => 'Halo Admin FlexiTag, kami berminat memesan 150 pcs ganci custom akrilik motif Toraja untuk cinderamata wisuda angkatan kami. Apakah ada penawaran khusus organisasi?',
                'contact' => 'fadli.rahman@gmail.com',
                'read_at' => null, // Unread
                'created_at' => now()->subHours(2),
            ],
            [
                'type' => 'contact',
                'user_id' => null,
                'sender_name' => 'Ibu Marlina (Owner Sutera Bugis)',
                'location' => 'Sengkang, Wajo',
                'message' => 'Selamat siang, saya ingin konsultasi penempelan chip NFC pada kain tenun sutera dan gantungan kunci kemasan oleh-oleh khas.',
                'contact' => '082187654321',
                'read_at' => now()->subDay(),
                'created_at' => now()->subDays(1),
            ],
            [
                'type' => 'lost_item',
                'user_id' => $user->id,
                'sender_name' => 'Rahmat Hidayat (Satpam Gedung LK Unhas)',
                'location' => 'Lobby Gedung LK Fakultas Teknik Gowa',
                'message' => 'Selamat sore Pak Budi, gantungan kunci FlexiTag Anda tertinggal di atas meja lobi satpam. Silakan diambil saat jam dinas.',
                'contact' => '085255123456',
                'read_at' => now()->subMinutes(30),
                'created_at' => now()->subHours(4),
            ],
        ]);

        // 13. Seed Audit Logs
        DB::table('audit_logs')->insertOrIgnore([
            'user_id' => $admin->id,
            'action' => 'ORDER_STATUS_UPDATE',
            'target' => 'ORD-2026-0001',
            'created_at' => now()->subHours(4),
        ]);
    }
}
