<?php

namespace Tests\Feature;

use App\Mail\PasswordResetCodeNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserPasswordResetTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Pengguna dapat membuka formulir lupa kata sandi.
     */
    public function test_user_can_view_forgot_password_form(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi');
        $response->assertSee('Kirim Kode Verifikasi');
    }

    /**
     * 2. Pengguna dapat meminta kode verifikasi 6 digit dan email terkirim.
     */
    public function test_user_can_request_password_reset_code(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'andi.test.reset@flexitag.id',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'andi.test.reset@flexitag.id',
        ]);

        $response->assertRedirect(route('password.reset', ['email' => 'andi.test.reset@flexitag.id']));
        $response->assertSessionHas('status');

        $this->assertTrue(
            DB::table('password_reset_tokens')
                ->where('email', 'andi.test.reset@flexitag.id')
                ->exists()
        );

        Mail::assertSent(PasswordResetCodeNotification::class, function ($mail) {
            return $mail->hasTo('andi.test.reset@flexitag.id') && strlen($mail->code) === 6;
        });
    }

    /**
     * 3. Pengguna dapat membuka formulir atur ulang kata sandi dan form input token tersedia.
     */
    public function test_user_can_view_reset_password_form_with_token_and_email(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => '654321',
            'email' => 'andi@flexitag.id',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Atur Ulang Kata Sandi');
        $response->assertSee('andi@flexitag.id');
        $response->assertSee('654321');
        $response->assertSee('Kode Token 6 Digit Angka');
        $response->assertSee('Verifikasi &amp; Simpan Kata Sandi', false);
    }

    /**
     * 4. Pengguna dapat memperbarui kata sandi dengan kode token 6 digit yang valid.
     */
    public function test_user_can_reset_password_with_valid_6_digit_token(): void
    {
        $user = User::factory()->create([
            'email' => 'andi.berhasil.reset@flexitag.id',
            'password' => 'oldpassword123',
        ]);

        $sixDigitCode = '482915';

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($sixDigitCode),
                'created_at' => now(),
            ]
        );

        $response = $this->post(route('password.update'), [
            'token' => $sixDigitCode,
            'email' => $user->email,
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        // Pastikan token terhapus setelah digunakan
        $this->assertFalse(
            DB::table('password_reset_tokens')->where('email', $user->email)->exists()
        );

        // Pengguna dapat login dengan kata sandi baru
        $loginResponse = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'newpassword456',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * 5. Reset kata sandi gagal jika kode token salah.
     */
    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'andi.salah.token@flexitag.id',
            'password' => 'oldpassword123',
        ]);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make('123456'),
                'created_at' => now(),
            ]
        );

        $response = $this->post(route('password.update'), [
            'token' => '654321',
            'email' => $user->email,
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertSessionHasErrors('token');
    }

    /**
     * 6. Reset kata sandi gagal jika kode token bukan 6 digit.
     */
    public function test_password_reset_fails_with_non_six_digit_token(): void
    {
        $user = User::factory()->create([
            'email' => 'andi.short.token@flexitag.id',
            'password' => 'oldpassword123',
        ]);

        $response = $this->post(route('password.update'), [
            'token' => '1234',
            'email' => $user->email,
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertSessionHasErrors('token');
    }

    /**
     * 7. Reset kata sandi gagal jika token sudah kedaluwarsa (>60 menit).
     */
    public function test_password_reset_fails_when_token_expired(): void
    {
        $user = User::factory()->create([
            'email' => 'andi.expired.token@flexitag.id',
            'password' => 'oldpassword123',
        ]);

        $sixDigitCode = '123456';

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($sixDigitCode),
                'created_at' => now()->subMinutes(61), // Lewat 60 menit
            ]
        );

        $response = $this->post(route('password.update'), [
            'token' => $sixDigitCode,
            'email' => $user->email,
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertSessionHasErrors('token');
    }
}
