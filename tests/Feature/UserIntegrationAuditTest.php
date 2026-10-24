<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Tag;
use App\Models\Tap;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserIntegrationAuditTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test 1: User login dapat membuka Analytics.
     */
    public function test_1_authenticated_user_can_access_analytics(): void
    {
        $user = User::factory()->create(['name' => 'Audit User 1']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Audit User 1',
            'active_mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)->get(route('user.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Analytics');
        $response->assertSee('Statistik Interaksi');
    }

    /**
     * Test 2: User login dapat membuka Settings.
     */
    public function test_2_authenticated_user_can_access_settings(): void
    {
        $user = User::factory()->create(['name' => 'Audit User 2']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Audit User 2',
            'active_mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)->get(route('user.settings'));

        $response->assertStatus(200);
        $response->assertSee('Settings');
        $response->assertSee('Pengaturan Akun');
    }

    /**
     * Test 3: User login dapat membuka Mode Hilang.
     */
    public function test_3_authenticated_user_can_access_lost_mode(): void
    {
        $user = User::factory()->create(['name' => 'Audit User 3']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Audit User 3',
            'active_mode' => 'hilang',
        ]);

        $response = $this->actingAs($user)->get(route('user.lost-mode.edit'));

        $response->assertStatus(200);
        $response->assertSee('Edit Profil (Mode Hilang)');
        $response->assertSee('Formulir Informasi Darurat');
    }

    /**
     * Test 4: Guest diarahkan ke halaman login untuk ketiga halaman.
     */
    public function test_4_guest_is_redirected_to_login_for_all_three_pages(): void
    {
        $this->get(route('user.analytics'))->assertRedirect(route('login'));
        $this->get(route('user.settings'))->assertRedirect(route('login'));
        $this->get(route('user.lost-mode.edit'))->assertRedirect(route('login'));
    }

    /**
     * Test 5: User tidak dapat mengakses atau melihat data user lain.
     */
    public function test_5_user_cannot_view_other_users_data(): void
    {
        $userA = User::factory()->create(['name' => 'User Alice', 'email' => 'alice@audit.com']);
        $userB = User::factory()->create(['name' => 'User Bob', 'email' => 'bob@audit.com']);

        $tagA = Tag::create([
            'tag_code' => 'FT-ALICE01',
            'activation_code' => 'ACT-ALICE01',
            'status' => 'aktif',
            'user_id' => $userA->id,
        ]);

        $tagB = Tag::create([
            'tag_code' => 'FT-BOB999',
            'activation_code' => 'ACT-BOB999',
            'status' => 'aktif',
            'user_id' => $userB->id,
        ]);

        Tap::create([
            'tag_id' => $tagB->id,
            'tapped_at' => now(),
            'source' => 'nfc',
            'device_type' => 'mobile',
            'region' => 'Toraja',
        ]);

        // Alice membuka analytics - tidak boleh melihat tag Bob
        $responseAnalytics = $this->actingAs($userA)->get(route('user.analytics'));
        $responseAnalytics->assertDontSee('FT-BOB999');

        // Alice membuka settings - tidak boleh melihat email atau tag Bob
        $responseSettings = $this->actingAs($userA)->get(route('user.settings'));
        $responseSettings->assertDontSee('bob@audit.com');
        $responseSettings->assertDontSee('FT-BOB999');

        // Alice membuka mode hilang - tag yang ditampilkan harus milik Alice
        $responseLostMode = $this->actingAs($userA)->get(route('user.lost-mode.edit'));
        $responseLostMode->assertSee('FT-ALICE01');
        $responseLostMode->assertDontSee('FT-BOB999');
    }

    /**
     * Test 6: Menu dashboard mengarah ke route yang benar.
     */
    public function test_6_dashboard_menu_contains_correct_routes(): void
    {
        $user = User::factory()->create(['name' => 'Audit Nav User']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Audit Nav User',
            'active_mode' => 'kampus',
        ]);
        Tag::create([
            'tag_code' => 'FT-AUDIT6',
            'activation_code' => 'ACT-AUDIT6',
            'status' => 'aktif',
            'user_id' => $user->id,
            'activated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('user.analytics'));
        $response->assertSee(route('user.settings'));
        $response->assertSee(route('user.lost-mode.edit'));
    }

    /**
     * Test 7: Data Mode Hilang yang disimpan dapat dibaca kembali.
     */
    public function test_7_saved_lost_mode_data_can_be_retrieved(): void
    {
        $user = User::factory()->create(['name' => 'Audit Lost User']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Audit Lost User',
            'active_mode' => 'kampus',
        ]);

        $updateResponse = $this->actingAs($user)->post(route('user.lost-mode.update'), [
            'message' => 'Tas ransel hitam tertinggal di lab komputer',
            'relay_phone' => '081234455667',
            'reward' => 'Uang tunai Rp 150.000',
            'form_enabled' => '1',
            'share_location' => '1',
            'activate_mode' => '1',
        ]);

        $updateResponse->assertRedirect(route('user.lost-mode.edit'));

        // Baca kembali
        $editResponse = $this->actingAs($user)->get(route('user.lost-mode.edit'));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Tas ransel hitam tertinggal di lab komputer');
        $editResponse->assertSee('081234455667');
        $editResponse->assertSee('Uang tunai Rp 150.000');

        // Pastikan active_mode profil menjadi 'hilang'
        $this->assertEquals('hilang', $user->fresh()->profile->active_mode);
    }

    /**
     * Test 8: Profil publik menampilkan mode hilang yang aktif.
     */
    public function test_8_public_profile_displays_active_lost_mode(): void
    {
        $user = User::factory()->create(['name' => 'Owner Lost Tag']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Owner Lost Tag',
            'active_mode' => 'hilang',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-PUB001',
            'activation_code' => 'ACT-PUB001',
            'status' => 'aktif',
            'user_id' => $user->id,
        ]);

        DB::table('mode_data')->insert([
            'user_id' => $user->id,
            'mode' => 'hilang',
            'data' => json_encode([
                'message' => 'Tolong kembalikan ke pos satpam terdekat.',
                'relay_phone' => '089988776655',
                'reward' => 'Rp 50.000',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('public.tag', ['tag_code' => 'FT-PUB001']));

        $response->assertStatus(200);
        $response->assertSee('Mode Penemu 🔒');
        $response->assertSee('PESAN DARURAT PEMILIK');
        $response->assertSee('Tolong kembalikan ke pos satpam terdekat.');
        $response->assertSee('Rp 50.000');
        $response->assertSee('Hubungi Pemilik via Relay Aman');
    }

    /**
     * Test 9: Analytics tidak error jika belum ada data tap.
     */
    public function test_9_analytics_does_not_error_with_zero_taps(): void
    {
        $user = User::factory()->create(['name' => 'Zero Tap User']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Zero Tap User',
            'active_mode' => 'kampus',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-ZEROTAP',
            'activation_code' => 'ACT-ZEROTAP',
            'status' => 'aktif',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.analytics'));

        $response->assertStatus(200);
        $response->assertSee('0');
        $response->assertSee('Belum ada aktivitas tap pada periode ini.');
        $response->assertSee('FT-ZEROTAP');
        $response->assertSee('kali interaksi');
    }

    /**
     * Test 10: Settings tidak error jika user belum memiliki hardware.
     */
    public function test_10_settings_does_not_error_without_hardware(): void
    {
        $user = User::factory()->create(['name' => 'No Hardware User']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'No Hardware User',
            'active_mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)->get(route('user.settings'));

        $response->assertStatus(200);
        $response->assertSee('0 Hardware Terhubung');
        $response->assertSee('Belum ada hardware terhubung');
    }
}
