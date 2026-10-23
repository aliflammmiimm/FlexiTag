<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Guest diarahkan ke halaman login saat membuka Settings.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('user.settings'));
        $response->assertRedirect(route('login'));
    }

    /**
     * 2. User terautentikasi dapat membuka halaman Settings miliknya.
     */
    public function test_authenticated_user_can_view_settings(): void
    {
        $user = User::factory()->create([
            'name' => 'Baharuddin Lopa',
            'email' => 'bahar@flexitag.id',
            'role' => 'user',
            'two_factor_enabled' => false,
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Baharuddin Lopa',
            'headline' => 'Pegawai Toraja',
        ]);

        $response = $this->actingAs($user)->get(route('user.settings'));

        $response->assertStatus(200);
        $response->assertSee('Settings');
        $response->assertSee('Pengaturan Akun');
        $response->assertSee('Baharuddin Lopa');
        $response->assertSee('bahar@flexitag.id');
        $response->assertSee('Keamanan');
        $response->assertSee('Ubah Kata Sandi');
        $response->assertSee('Autentikasi 2 Langkah');
        $response->assertSee('Akses &amp; Privasi Tag', false);
        $response->assertSee('Bantuan &amp; Dukungan', false);
        $response->assertSee('Pusat Bantuan / FAQ');
        $response->assertSee('Hubungi Tim Support');
    }

    /**
     * 3. Data user yang tampil sesuai user yang sedang login (bukan user lain).
     */
    public function test_settings_data_matches_authenticated_user_only(): void
    {
        $userA = User::factory()->create([
            'name' => 'Akun Pengguna A',
            'email' => 'user_a@flexitag.id',
        ]);

        $userB = User::factory()->create([
            'name' => 'Akun Pengguna B',
            'email' => 'user_b@flexitag.id',
        ]);

        $response = $this->actingAs($userA)->get(route('user.settings'));

        $response->assertStatus(200);
        $response->assertSee('Akun Pengguna A');
        $response->assertSee('user_a@flexitag.id');
        $response->assertDontSee('Akun Pengguna B');
        $response->assertDontSee('user_b@flexitag.id');
    }

    /**
     * 4. Hardware yang tampil hanya milik user yang sedang login.
     */
    public function test_hardware_displayed_only_belongs_to_authenticated_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Tag milik User B
        Tag::create([
            'tag_code' => 'FT-TAG-B',
            'activation_code' => 'ACT-B',
            'status' => 'aktif',
            'user_id' => $userB->id,
        ]);

        // Tag milik User A
        Tag::create([
            'tag_code' => 'FT-TAG-A',
            'activation_code' => 'ACT-A',
            'status' => 'aktif',
            'user_id' => $userA->id,
        ]);

        $response = $this->actingAs($userA)->get(route('user.settings'));

        $response->assertStatus(200);
        $response->assertSee('1 Hardware Terhubung');
        $response->assertSee('FT-TAG-A');
        $response->assertDontSee('FT-TAG-B');
    }

    /**
     * 5. User tanpa hardware tidak menyebabkan error 500.
     */
    public function test_user_without_hardware_shows_clean_state(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.settings'));

        $response->assertStatus(200);
        $response->assertSee('0 Hardware Terhubung');
        $response->assertSee('Belum ada hardware terhubung');
    }

    /**
     * 6. Status 2FA tidak diklaim aktif jika database bernilai false.
     */
    public function test_two_factor_status_is_truthful_when_disabled(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => false,
        ]);

        $response = $this->actingAs($user)->get(route('user.settings'));

        $response->assertStatus(200);
        $response->assertSee('Tidak Aktif');
    }

    /**
     * 7. Status 2FA ditampilkan aktif jika database bernilai true.
     */
    public function test_two_factor_status_shows_active_when_enabled(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
        ]);

        $response = $this->actingAs($user)->get(route('user.settings'));

        $response->assertStatus(200);
        $response->assertSee('Aktif');
    }

    /**
     * 8. User dapat mengubah kata sandi dengan verifikasi password saat ini.
     */
    public function test_user_can_update_password_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->post(route('user.settings.password'), [
            'current_password' => 'old-password-123',
            'password' => 'new-secret-password-456',
            'password_confirmation' => 'new-secret-password-456',
        ]);

        $response->assertRedirect(route('user.settings'));
        $response->assertSessionHas('status', 'Kata sandi berhasil diperbarui dengan aman.');
        $this->assertTrue(Hash::check('new-secret-password-456', $user->fresh()->password));
    }

    /**
     * 9. Gagal ubah kata sandi jika password saat ini salah.
     */
    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password-123'),
        ]);

        $response = $this->actingAs($user)->post(route('user.settings.password'), [
            'current_password' => 'wrong-password-999',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('correct-password-123', $user->fresh()->password));
    }

    /**
     * 10. User dapat memperbarui preferensi privasi tag miliknya.
     */
    public function test_user_can_update_tag_privacy_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.settings.privacy'), [
            'show_phone' => '1',
            'show_email' => '1',
        ]);

        $response->assertRedirect(route('user.settings'));
        $response->assertSessionHas('status', 'Pengaturan privasi tag berhasil diperbarui.');

        $this->assertDatabaseHas('privacy_settings', [
            'user_id' => $user->id,
            'show_phone' => 1,
            'show_email' => 1,
            'show_location' => 0,
            'indexable' => 0,
        ]);
    }
}
