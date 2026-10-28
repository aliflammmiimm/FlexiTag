<?php

namespace Tests\Feature;

use App\Models\ModeData;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('user.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_user_dashboard_with_all_components(): void
    {
        $user = User::factory()->create([
            'name' => 'Andi Daeng',
            'role' => 'user',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Andi Daeng',
            'headline' => 'Pegawai Kopdes',
            'active_mode' => 'kampus',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-8X9Y2Z',
            'activation_code' => 'KD-9988-ZZ',
            'status' => 'aktif',
            'user_id' => $user->id,
            'produced_at' => now(),
            'activated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Halo, Andi Daeng');
        $response->assertSee('Keychain Pintar');
        $response->assertSee('TOTAL TAP');
        $response->assertSee('AKTIVITAS TERAKHIR');
        $response->assertSee('Pilih Mode Aktif');
        $response->assertSee('Mode Kampus');
        $response->assertSee('Mode Kerja');
        $response->assertSee('Mode UMKM');
        $response->assertSee('Mode Barang Hilang');
        $response->assertSee('ID: FT-8X9Y2Z');
        $response->assertSee('Terenkripsi Cloud MaccaTech');
        $response->assertSee('NFC READY');
        $response->assertSee('Edit Isi Profil Aktif');
        $response->assertSee('Pengaturan Privasi &amp; Akses', false);
        $response->assertSee('MENU NAVIGASI');
        $response->assertSee('Pegawai Kopdes');
    }

    public function test_admin_user_with_active_tag_can_view_user_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Super Administrator',
            'role' => 'admin',
        ]);

        Profile::create([
            'user_id' => $admin->id,
            'display_name' => 'Super Administrator',
            'headline' => 'Core Admin',
            'active_mode' => 'kampus',
        ]);

        $this->createActiveTagFor($admin);

        $response = $this->actingAs($admin)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Konsol Pengelola');
        $response->assertSee('FLEXITAG');
        $response->assertSee('Halo, Super Administrator');
    }

    public function test_admin_without_active_tag_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Saparuddin',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('user.dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    private function createActiveTagFor(User $user): Tag
    {
        return Tag::create([
            'tag_code' => 'FT-'.strtoupper(Str::random(6)),
            'activation_code' => 'KD-'.strtoupper(Str::random(6)),
            'status' => 'aktif',
            'user_id' => $user->id,
            'produced_at' => now(),
            'activated_at' => now(),
        ]);
    }

    public function test_user_can_switch_mode_via_json_request(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => $user->name,
            'active_mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)->postJson(route('user.dashboard.switchMode'), [
            'mode' => 'kerja',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'mode' => 'kerja',
            'label' => 'Mode Kerja',
        ]);

        $this->assertEquals('kerja', $user->fresh()->profile->active_mode);
    }

    public function test_user_can_update_profile_from_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Nama Lama',
            'headline' => 'Pekerjaan Lama',
        ]);

        $response = $this->actingAs($user)->post(route('user.dashboard.updateProfile'), [
            'display_name' => 'Andi Daeng Baru',
            'headline' => 'Pemberdaya UMKM Sulsel',
            'no_whatsapp' => '081299887766',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertEquals('Andi Daeng Baru', $user->fresh()->profile->display_name);
        $this->assertEquals('Pemberdaya UMKM Sulsel', $user->fresh()->profile->headline);
        $this->assertEquals('6281299887766', $user->fresh()->no_whatsapp);
    }

    public function test_landing_page_shows_masuk_button(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Masuk');
        $response->assertSee(route('login'));
        $response->assertSee('Aktivasi Tag');
        $response->assertSee('Pesan Sekarang');
    }

    public function test_landing_page_displays_catalog_designs_with_images(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('<img', false);
        $response->assertSee('★ Top #1');
        $response->assertSee('★ Top #4');
        $response->assertDontSee('★ Top #5');
    }

    public function test_user_can_login_and_is_redirected_to_user_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'andi@flexitag.id',
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $this->createActiveTagFor($user);

        $response = $this->post(route('login.submit'), [
            'email' => 'andi@flexitag.id',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_without_active_tag_cannot_access_dashboard_and_is_redirected_to_activation_step_2(): void
    {
        $user = User::factory()->create([
            'email' => 'no_tag@flexitag.id',
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertRedirect(route('user.activation.show', ['step' => 2]));
        $response->assertSessionHas('warning');
    }

    public function test_user_without_active_tag_logging_in_is_redirected_to_activation_step_2(): void
    {
        $user = User::factory()->create([
            'email' => 'belum_aktif@flexitag.id',
            'password' => 'secret123',
            'role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'belum_aktif@flexitag.id',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('user.activation.show', ['step' => 2]));
        $response->assertSessionHas('warning');
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_without_active_tag_visiting_login_is_redirected_to_activation_step_2(): void
    {
        $user = User::factory()->create([
            'email' => 'guest_check@flexitag.id',
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('user.activation.show', ['step' => 2]));
    }

    public function test_user_can_view_umkm_profile_edit_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Kedai Bu Sari',
            'active_mode' => 'umkm',
        ]);

        $response = $this->actingAs($user)->get(route('user.profile.umkm'));

        $response->assertStatus(200);
        $response->assertSee('Edit Profil');
        $response->assertSee('KATALOG KULINER');
        $response->assertSee('Ubah Logo Usaha / Toko');
        $response->assertSee('Sinkronisasi Tag UMKM');
        $response->assertSee('Informasi Pokok');
        $response->assertSee('Nama Usaha / Toko');
        $response->assertSee('Katalog Produk Menu');
        $response->assertSee('Simpan Profil UMKM');
    }

    public function test_user_can_update_umkm_profile_and_catalog(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Nama Awal',
            'active_mode' => 'umkm',
        ]);

        $katalogSample = [
            [
                'id' => 101,
                'nama' => 'Bandeng Pallumara',
                'harga' => 40000,
                'satuan' => 'porsi',
                'label' => 'Menu Baru',
                'badge_icon' => '✨',
                'badge_class' => 'badge-gold',
                'is_ready' => true,
            ],
        ];

        $response = $this->actingAs($user)->post(route('user.profile.umkm.update'), [
            'nama_usaha' => 'Warung Pangkep Sentosa',
            'deskripsi' => 'Kuliner olahan bandeng tanpa duri khas Sulawesi Selatan dengan bumbu rempah pilihan.',
            'alamat_toko' => 'Jl. Poros Makassar - Pangkep No. 88',
            'whatsapp_order' => '081234567899',
            'katalog_json' => json_encode($katalogSample),
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('status');

        $this->assertEquals($user->name, $user->fresh()->profile->display_name);
        $this->assertEquals('6281234567899', $user->fresh()->no_whatsapp);

        $this->assertDatabaseHas('mode_data', [
            'user_id' => $user->id,
            'mode' => 'umkm',
        ]);
    }

    public function test_guest_cannot_access_umkm_edit_page(): void
    {
        $response = $this->get(route('user.profile.umkm'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_tambah_produk_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->createActiveTagFor($user);

        $response = $this->actingAs($user)->get(route('user.profile.umkm.product.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Produk Baru');
        $response->assertSee('Foto Produk Utama');
        $response->assertSee('Unggah Foto Produk');
        $response->assertSee('Nama Produk');
        $response->assertSee('Contoh: Coto Makassar Instan Premium');
        $response->assertSee('Harga (Rp)');
        $response->assertSee('Satuan Takaran');
        $response->assertSee('Rekomendasi:');
        $response->assertSee('Deskripsi Produk (Opsional)');
        $response->assertSee('Label Promosi');
        $response->assertSee('Tanpa Label');
        $response->assertSee('Unggulan');
        $response->assertSee('Paling Laris');
        $response->assertSee('Sinkronisasi Otomatis');
        $response->assertSee('NFC READY');
        $response->assertSee('Simpan Produk');
    }

    public function test_user_can_store_new_product_and_redirect_to_umkm_profile(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->createActiveTagFor($user);

        $response = $this->actingAs($user)->post(route('user.profile.umkm.product.store'), [
            'nama' => 'Coto Makassar Instan Premium',
            'harga' => '35.000',
            'satuan' => '/porsi',
            'deskripsi' => 'Daging sapi pilihan berkuah rempah khas Makassar dengan sambal tauco.',
            'label_promosi' => 'unggulan',
        ]);

        $response->assertRedirect(route('user.profile.umkm'));
        $response->assertSessionHas('status');

        $modeRecord = ModeData::where('user_id', $user->id)->where('mode', 'umkm')->first();
        $this->assertNotNull($modeRecord);
        $products = $modeRecord->data['katalog_produk'] ?? [];
        $this->assertCount(1, $products);
        $this->assertEquals('Coto Makassar Instan Premium', $products[0]['nama']);
        $this->assertEquals(35000, $products[0]['harga']);
        $this->assertEquals('porsi', $products[0]['satuan']);
        $this->assertEquals('Unggulan', $products[0]['label']);
    }

    public function test_user_can_edit_and_update_product(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->createActiveTagFor($user);

        $initialProduct = [
            'id' => 999,
            'nama' => 'Konro Bakar',
            'harga' => 50000,
            'satuan' => 'porsi',
            'deskripsi' => 'Iga sapi bakar',
            'label' => 'Paling Laris',
            'badge_icon' => '🔥',
            'badge_class' => 'badge-red',
        ];

        ModeData::create([
            'user_id' => $user->id,
            'mode' => 'umkm',
            'data' => [
                'katalog_produk' => [$initialProduct],
            ],
        ]);

        $responseView = $this->actingAs($user)->get(route('user.profile.umkm.product.edit', ['id' => 999]));
        $responseView->assertStatus(200);
        $responseView->assertSee('Edit Produk');
        $responseView->assertSee('Konro Bakar');

        $responseUpdate = $this->actingAs($user)->post(route('user.profile.umkm.product.update', ['id' => 999]), [
            'nama' => 'Konro Bakar Karebosi Spesial',
            'harga' => '55.000',
            'satuan' => '/porsi',
            'deskripsi' => 'Iga sapi bakar bumbu kacang spesial',
            'label_promosi' => 'unggulan',
        ]);

        $responseUpdate->assertRedirect(route('user.profile.umkm'));

        $modeRecord = ModeData::where('user_id', $user->id)->where('mode', 'umkm')->first();
        $products = $modeRecord->data['katalog_produk'] ?? [];
        $this->assertEquals('Konro Bakar Karebosi Spesial', $products[0]['nama']);
        $this->assertEquals(55000, $products[0]['harga']);
        $this->assertEquals('Unggulan', $products[0]['label']);
    }

    public function test_user_can_delete_product(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->createActiveTagFor($user);

        $productToDelete = [
            'id' => 888,
            'nama' => 'Es Pisang Ijo',
            'harga' => 15000,
            'satuan' => 'porsi',
        ];

        ModeData::create([
            'user_id' => $user->id,
            'mode' => 'umkm',
            'data' => [
                'katalog_produk' => [$productToDelete],
            ],
        ]);

        $response = $this->actingAs($user)->post(route('user.profile.umkm.product.destroy', ['id' => 888]));
        $response->assertRedirect(route('user.profile.umkm'));

        $modeRecord = ModeData::where('user_id', $user->id)->where('mode', 'umkm')->first();
        $products = $modeRecord->data['katalog_produk'] ?? [];
        $this->assertCount(0, $products);
    }

    public function test_public_profile_displays_umkm_view_when_active_mode_is_umkm(): void
    {
        $user = User::factory()->create([
            'name' => 'Pemilik Kedai',
            'no_whatsapp' => '6281299998888',
        ]);
        $tag = $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Kedai Kopi Bugis',
            'headline' => 'Kopi Arabika Toraja Asli',
            'active_mode' => 'umkm',
        ]);

        ModeData::create([
            'user_id' => $user->id,
            'mode' => 'umkm',
            'data' => [
                'nama_usaha' => 'Kedai Kopi Bugis Makassar',
                'deskripsi' => 'Kopi khas Sulawesi dengan racikan rempah istimewa.',
                'alamat_toko' => 'Jl. Sam Ratulangi No. 45, Makassar',
                'whatsapp_order' => '6281299998888',
                'katalog_produk' => [
                    [
                        'id' => 1,
                        'nama' => 'Kopi Tarik Bugis',
                        'harga' => 20000,
                        'satuan' => 'cangkir',
                        'deskripsi' => 'Kopi seduh tradisional',
                        'label' => 'Unggulan',
                        'badge_icon' => '⭐',
                        'badge_class' => 'badge-gold',
                    ],
                ],
            ],
        ]);

        $response = $this->get(route('public.tag', ['tag_code' => $tag->tag_code]));

        $response->assertStatus(200);
        $response->assertSee('MODE UMKM AKTIF');
        $response->assertSee('Kedai Kopi Bugis Makassar');
        $response->assertSee('Kopi khas Sulawesi dengan racikan rempah istimewa.');
        $response->assertSee('Jl. Sam Ratulangi No. 45, Makassar');
        $response->assertSee('Kopi Tarik Bugis');
        $response->assertSee('Rp 20.000');
        $response->assertSee('Hubungi &amp; Pesan via WhatsApp', false);
        $response->assertSee('Katalog Produk Pilihan');
        $response->assertSee('Informasi Toko &amp; Jam Buka', false);
    }

    public function test_public_profile_displays_standard_view_when_active_mode_is_not_umkm(): void
    {
        $user = User::factory()->create(['name' => 'Mahasiswa Teknik']);
        $tag = $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Andi Mahasiswa',
            'headline' => 'Teknik Informatika Unhas',
            'active_mode' => 'kampus',
        ]);

        $response = $this->get(route('public.tag', ['tag_code' => $tag->tag_code]));

        $response->assertStatus(200);
        $response->assertSee('Mode Kampus');
        $response->assertSee('Andi Mahasiswa');
        $response->assertDontSee('MODE UMKM AKTIF');
    }

    public function test_user_can_view_merch_page_with_consistent_header(): void
    {
        $user = User::factory()->create(['name' => 'Pemilik Merch']);
        $tag = $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Pemilik Merch',
            'headline' => 'Member FlexiTag',
            'active_mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)->get(route('user.merch'));

        $response->assertStatus(200);
        // Memastikan konsistensi topbar header dengan halaman home
        $response->assertSee('user-topbar', false);
        $response->assertSee('FLEXITAG');
        $response->assertSee('Merch');
        $response->assertSee('hamburger-btn', false);
        // Memastikan konten vault dan merch card
        $response->assertSee('Manajemen Tag & Merch', false);
        $response->assertSee('NFC HERITAGE VAULT');
        $response->assertSee('+ Tambah Produk FlexiTag');
        $response->assertSee('Pindai Tag Sekarang');
        $response->assertSee('Kunci Tag Sementara (Kill Switch)');
        $response->assertSee($tag->tag_code);
    }

    public function test_user_can_toggle_kill_switch_to_lock_and_unlock_tag(): void
    {
        $user = User::factory()->create(['name' => 'Owner Killswitch']);
        $tag = $this->createActiveTagFor($user);

        $this->assertEquals('aktif', $tag->status);

        // Kunci tag melalui kill switch
        $response = $this->actingAs($user)->post(route('user.merch.toggleTagStatus', $tag->id));
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $tag->refresh();
        $this->assertEquals('nonaktif', $tag->status);
        $this->assertEquals('owner', $tag->disabled_by);

        // Pengguna masih dapat membuka halaman merch untuk membuka kunci kembali
        $merchResponse = $this->actingAs($user)->get(route('user.merch'));
        $merchResponse->assertStatus(200);
        $merchResponse->assertSee('Buka Kunci Tag');
        $merchResponse->assertSee('Tag terkunci oleh proteksi Kill Switch');

        // Buka kunci tag kembali
        $unlockResponse = $this->actingAs($user)->post(route('user.merch.toggleTagStatus', $tag->id));
        $unlockResponse->assertRedirect();

        $tag->refresh();
        $this->assertEquals('aktif', $tag->status);
        $this->assertNull($tag->disabled_by);
    }

    public function test_public_profile_is_locked_when_kill_switch_is_active(): void
    {
        $user = User::factory()->create();
        $tag = $this->createActiveTagFor($user);

        // Kunci tag dengan kill switch (owner)
        $tag->update(['status' => 'nonaktif', 'disabled_by' => 'owner']);

        $response = $this->get(route('public.tag', ['tag_code' => $tag->tag_code]));

        $response->assertStatus(200);
        $response->assertSee('Proteksi Kill Switch Aktif');
        $response->assertSee('Kunci Sementara oleh Pemilik');
        $response->assertDontSee($user->name);
    }

    public function test_user_dashboard_and_public_profile_when_tag_is_blocked_by_admin(): void
    {
        $user = User::factory()->create(['name' => 'User Blokir']);
        $tag = $this->createActiveTagFor($user);

        // Admin memblokir tag
        $tag->update(['status' => 'nonaktif', 'disabled_by' => 'admin']);

        // Sisi User Dashboard
        $dashResponse = $this->actingAs($user)->get(route('user.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Diblokir Admin');
        $dashResponse->assertSee('sedang dinonaktifkan oleh Administrator');

        // Sisi User Merch
        $merchResponse = $this->actingAs($user)->get(route('user.merch'));
        $merchResponse->assertStatus(200);
        $merchResponse->assertSee('Diblokir Admin');
        $merchResponse->assertSee('Hubungi Admin');

        // Sisi Publik
        $publicResponse = $this->get(route('public.tag', ['tag_code' => $tag->tag_code]));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Perangkat Dinonaktifkan');
        $publicResponse->assertSee('Penangguhan oleh Administrator');
    }

    public function test_user_can_quick_claim_new_tag_from_merch_page(): void
    {
        $user = User::factory()->create(['name' => 'Owner Multi Tag']);
        $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Owner Multi Tag',
            'active_mode' => 'kampus',
        ]);

        $newTag = Tag::create([
            'tag_code' => 'FT-QUICK-01',
            'activation_code' => 'KD-QUICK-99',
            'status' => 'belum_aktif',
            'user_id' => null,
            'produced_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('user.merch.claimTag'), [
            'activation_code' => 'KD-QUICK-99',
        ]);

        $response->assertRedirect(route('user.merch'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('tags', [
            'id' => $newTag->id,
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);
    }

    public function test_user_can_view_edit_mode_kampus_page(): void
    {
        $user = User::factory()->create(['name' => 'Andi Muhammad']);
        $tag = $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Andi Muhammad',
            'headline' => 'Teknik Komputer • Universitas Negeri Makassar',
            'bio' => 'Angkatan 2023 • Makassar, Sulawesi Selatan',
            'active_mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)->get(route('user.profile.kampus'));

        $response->assertStatus(200);
        $response->assertSee('Edit Profil');
        $response->assertSee('JEJARING AKADEMIK');
        $response->assertSee('KAMPUS');
        $response->assertSee('Foto Profil Mahasiswa');
        $response->assertSee('Identitas Mahasiswa');
        $response->assertSee('Teknik Komputer');
        $response->assertSee('Universitas Negeri Makassar');
        $response->assertSee('HIMA Teknik Komputer UNM');
    }

    public function test_user_can_update_mode_kampus_profile_and_sync_public_profile(): void
    {
        $user = User::factory()->create(['name' => 'Andi Muhammad']);
        $tag = $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Andi Muhammad',
            'headline' => 'Mahasiswa Baru',
            'active_mode' => 'kampus',
        ]);

        $links = [
            [
                'id' => 1,
                'type' => 'academic',
                'title' => 'HIMA Teknik Komputer UNM',
                'subtitle' => 'Pengurus Divisi Riset & Teknologi',
                'url' => 'https://himatekkom.unm.ac.id',
                'is_visible' => true,
            ],
            [
                'id' => 2,
                'type' => 'linkedin',
                'title' => 'LinkedIn Mahasiswa',
                'subtitle' => 'Jejaring akademik, lomba, & sertifikasi',
                'url' => 'https://linkedin.com/in/andimuhammad',
                'is_visible' => true,
            ],
        ];

        $postData = [
            'nama_lengkap' => 'Andi Muhammad Baru',
            'program_studi' => 'Teknik Informatika & Komputer',
            'universitas' => 'Universitas Negeri Makassar',
            'angkatan' => '2023',
            'domisili' => 'Makassar, Sulawesi Selatan',
            'telepon_kampus' => '081234567890',
            'email_kampus' => 'andi.muhammad@student.unm.ac.id',
            'links_json' => json_encode($links),
        ];

        $response = $this->actingAs($user)->post(route('user.profile.kampus.update'), $postData);

        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('status', 'Profil Mode Kampus berhasil disimpan & disinkronkan ke Tag!');

        // Verifikasi database mode_data dan profile
        $this->assertDatabaseHas('mode_data', [
            'user_id' => $user->id,
            'mode' => 'kampus',
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'display_name' => 'Andi Muhammad Baru',
            'headline' => 'Teknik Informatika & Komputer • Universitas Negeri Makassar',
            'active_mode' => 'kampus',
        ]);

        // Verifikasi publik profil tag
        $publicResponse = $this->get(route('public.tag', ['tag_code' => $tag->tag_code]));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Andi Muhammad Baru');
        $publicResponse->assertSee('Teknik Informatika & Komputer • Universitas Negeri Makassar');
        $publicResponse->assertSee('Angkatan 2023 • Makassar, Sulawesi Selatan');
        $publicResponse->assertSee('HIMA Teknik Komputer UNM');
        $publicResponse->assertSee('Pengurus Divisi Riset & Teknologi');
    }

    public function test_authenticated_user_can_view_edit_mode_kerja_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $tag = $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Budi Developer',
            'headline' => 'Full-Stack Developer',
            'active_mode' => 'kerja',
        ]);

        ModeData::create([
            'user_id' => $user->id,
            'mode' => 'kampus',
            'data' => [
                'nama_mahasiswa' => 'Budi Developer',
                'program_studi' => 'Teknik Informatika',
                'universitas' => 'Universitas Hasanuddin',
                'domisili' => 'Makassar',
                'telepon_kampus' => '081234567890',
                'email_kampus' => 'budi@student.unhas.ac.id',
            ],
        ]);

        $response = $this->actingAs($user)->get(route('user.profile.kerja'));

        $response->assertStatus(200);
        $response->assertSee('PORTOFOLIO &amp; KARIR', false);
        $response->assertSee('Salin dari Profil Kampus');
        $response->assertSee('Foto Profil Profesional');
        $response->assertSee('Curriculum Vitae (PDF)');
        $response->assertSee('Simpan Perubahan Mode Kerja');
    }

    public function test_authenticated_user_can_update_mode_kerja(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $tag = $this->createActiveTagFor($user);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Andi Muhammad',
            'headline' => 'Junior Developer',
            'active_mode' => 'kerja',
        ]);

        $postData = [
            'nama_lengkap' => 'Andi Muhammad, S.Kom',
            'posisi' => 'Senior IoT & Cloud Engineer',
            'perusahaan' => 'PT Celebes Digital Solusindo',
            'lokasi_kantor' => 'Makassar, Sulawesi Selatan (Hybrid)',
            'bio' => 'Berpengalaman 3+ tahun membangun sistem tertanam dan platform web berbasis Laravel.',
            'telepon_kerja' => '081234567890',
            'email_kerja' => 'andi.pro@example.com',
            'links_json' => json_encode([
                [
                    'id' => 1,
                    'type' => 'portfolio',
                    'title' => 'Portofolio Digital',
                    'subtitle' => 'Koleksi aplikasi IoT',
                    'url' => 'https://andimuhammad.dev',
                    'is_visible' => true,
                ],
            ]),
        ];

        $response = $this->actingAs($user)->post(route('user.profile.kerja.update'), $postData);

        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('status', 'Profil Mode Kerja berhasil disimpan & disinkronkan ke Tag!');

        $this->assertDatabaseHas('mode_data', [
            'user_id' => $user->id,
            'mode' => 'kerja',
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'display_name' => 'Andi Muhammad, S.Kom',
            'headline' => 'Senior IoT & Cloud Engineer • PT Celebes Digital Solusindo',
            'active_mode' => 'kerja',
        ]);

        $pubResponse = $this->get(route('public.tag', ['tag_code' => $tag->tag_code]));
        $pubResponse->assertStatus(200);
        $pubResponse->assertSee('Andi Muhammad, S.Kom');
        $pubResponse->assertSee('Senior IoT &amp; Cloud Engineer', false);
        $pubResponse->assertSee('Makassar, Sulawesi Selatan (Hybrid)');
        $pubResponse->assertSee('Unduh CV (PDF)');
    }
}
