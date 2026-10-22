<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    public function test_admin_user_can_view_user_dashboard_without_redirect_to_admin_dashboard(): void
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

        $response = $this->actingAs($admin)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Konsol Pengelola');
        $response->assertSee('FLEXITAG');
        $response->assertSee('Halo, Super Administrator');
    }

    public function test_user_can_switch_mode_via_json_request(): void
    {
        $user = User::factory()->create(['role' => 'user']);
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

    public function test_user_can_login_and_is_redirected_to_user_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'andi@flexitag.id',
            'password' => 'secret123',
            'is_active' => true,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'andi@flexitag.id',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
