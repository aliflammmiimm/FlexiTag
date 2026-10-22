<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_landing_page_returns_successful_response(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $response->assertSee('Satu Tap Kartu');
        $response->assertSee('Mode Kampus');
        $response->assertSee('Mode Kerja');
        $response->assertSee('Mode UMKM');
        $response->assertSee('Mode Barang Hilang');
    }

    public function test_unregistered_tag_returns_not_found(): void
    {
        $response = $this->get(route('public.tag', 'NONEXISTENT'));
        $response->assertStatus(404);
        $response->assertSee('Tag Belum Terdaftar');
    }

    public function test_unactivated_tag_returns_friendly_unactivated_page(): void
    {
        $tag = Tag::create([
            'tag_code' => 'TEST_UNACT',
            'activation_code' => 'ACT_TEST_123',
            'status' => 'belum_aktif',
        ]);

        $response = $this->get(route('public.tag', $tag->tag_code));
        $response->assertStatus(200);
        $response->assertSee('Kartu Belum Diaktivasi');
    }

    public function test_disabled_tag_returns_forbidden_state(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'TEST_BLOCKED',
            'activation_code' => 'ACT_BLOCKED_123',
            'user_id' => $user->id,
            'status' => 'nonaktif',
            'disabled_by' => 'admin',
        ]);

        $response = $this->get(route('public.tag', $tag->tag_code));
        $response->assertStatus(403);
        $response->assertSee('Akses Publik Dinonaktifkan');
    }

    public function test_active_tag_renders_profile_with_privacy_filtering(): void
    {
        $user = User::factory()->create([
            'name' => 'Fikri Macca',
            'email' => 'fikri@example.com',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Fikri Macca',
            'headline' => 'Mahasiswa Teknik Informatika',
            'active_mode' => 'kampus',
            'username' => 'fikrimacca',
        ]);

        DB::table('mode_data')->insert([
            'user_id' => $user->id,
            'mode' => 'kampus',
            'data' => json_encode([
                'kampus' => 'Universitas Hasanuddin',
                'fakultas' => 'Fakultas Teknik',
                'jurusan' => 'Informatika',
                'nim' => 'D121211001',
                'phone' => '081234567890', // Sensitif: harus disembunyikan jika show_phone false
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('privacy_settings')->insert([
            'user_id' => $user->id,
            'show_phone' => false,
            'show_email' => false,
            'show_location' => false,
            'indexable' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT_FIKRI_01',
            'activation_code' => 'ACT_FIKRI_01',
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);

        // Akses via Tag Code
        $resTag = $this->get(route('public.tag', $tag->tag_code));
        $resTag->assertStatus(200);
        $resTag->assertSee('Fikri Macca');
        $resTag->assertSee('Universitas Hasanuddin');
        $resTag->assertDontSee('081234567890'); // Terfilter oleh privacy

        // Akses via Username @
        $resUser = $this->get(route('public.username', 'fikrimacca'));
        $resUser->assertStatus(200);
        $resUser->assertSee('Fikri Macca');
    }

    public function test_lost_mode_message_submission_and_safe_statement(): void
    {
        $user = User::factory()->create();

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Pemilik Dompet',
            'active_mode' => 'hilang',
        ]);

        $tag = Tag::create([
            'tag_code' => 'LOST_TAG_99',
            'activation_code' => 'ACT_LOST_99',
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);

        // Halaman profil mode hilang wajib memuat teks resmi aman
        $response = $this->get(route('public.tag', $tag->tag_code));
        $response->assertStatus(200);
        $response->assertSee('Pesan diteruskan secara aman tanpa mengekspos kontak pemilik kepada publik.');

        // Pengiriman form pesan penemu
        $postRes = $this->post(route('public.lost-message', $tag->tag_code), [
            'sender_name' => 'Budi Penemu',
            'location' => 'Kantin Teknik',
            'contact' => '081122334455',
            'message' => 'Kartu ditemukan tertinggal di atas meja.',
        ]);

        $postRes->assertRedirect(route('public.tag', $tag->tag_code));
        $this->assertDatabaseHas('messages', [
            'type' => 'lost_item',
            'user_id' => $user->id,
            'sender_name' => 'Budi Penemu',
            'location' => 'Kantin Teknik',
        ]);
    }
}
