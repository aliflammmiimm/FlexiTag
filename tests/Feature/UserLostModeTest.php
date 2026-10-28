<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserLostModeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Guest diarahkan ke login saat membuka halaman mode hilang.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('user.lost-mode.edit'));
        $response->assertRedirect(route('login'));
    }

    /**
     * 2. User dapat membuka edit mode hilang untuk tag miliknya.
     */
    public function test_authenticated_user_can_view_lost_mode_edit_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Pemilik Tag Toraja',
            'no_whatsapp' => '081234567890',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Pemilik Tag Toraja',
            'active_mode' => 'hilang',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-LOST01',
            'activation_code' => 'ACT-LOST01',
            'status' => 'aktif',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.lost-mode.edit'));

        $response->assertStatus(200);
        $response->assertSee('Edit Profil (Mode Hilang)');
        $response->assertSee('PROTEKSI PRIVASI AKTIF');
        $response->assertSee('Privasi Terlindungi');
        $response->assertSee('FT-LOST01');
        $response->assertSee('Formulir Informasi Darurat');
        $response->assertSee('Pesan Penemuan Barang');
        $response->assertSee('Nomor HP Penerima Relay');
        $response->assertSee('Opsi Komunikasi &amp; Keamanan', false);
        $response->assertSee('Simpan Mode Hilang');
    }

    /**
     * 3. User tidak dapat melihat atau mengedit tag user lain.
     */
    public function test_user_cannot_view_or_edit_other_user_tag_info(): void
    {
        $userA = User::factory()->create(['name' => 'User A']);
        $userB = User::factory()->create(['name' => 'User B']);

        // Tag milik User B
        Tag::create([
            'tag_code' => 'FT-TAG-USERB',
            'activation_code' => 'ACT-B',
            'status' => 'aktif',
            'user_id' => $userB->id,
        ]);

        // Tag milik User A
        Tag::create([
            'tag_code' => 'FT-TAG-USERA',
            'activation_code' => 'ACT-A',
            'status' => 'aktif',
            'user_id' => $userA->id,
        ]);

        $response = $this->actingAs($userA)->get(route('user.lost-mode.edit'));

        $response->assertStatus(200);
        $response->assertSee('FT-TAG-USERA');
        $response->assertDontSee('FT-TAG-USERB');
    }

    /**
     * 4. Data mode hilang yang tersimpan ditampilkan kembali di formulir.
     */
    public function test_saved_lost_mode_data_is_displayed_back_in_form(): void
    {
        $user = User::factory()->create();

        DB::table('mode_data')->insert([
            'user_id' => $user->id,
            'mode' => 'hilang',
            'data' => json_encode([
                'message' => 'Tolong kembalikan tas ransel hitam saya!',
                'relay_phone' => '082199887766',
                'reward' => 'Uang tunai Rp 150.000',
                'form_enabled' => true,
                'share_location' => true,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.lost-mode.edit'));

        $response->assertStatus(200);
        $response->assertSee('Tolong kembalikan tas ransel hitam saya!');
        $response->assertSee('082199887766');
        $response->assertSee('Uang tunai Rp 150.000');
    }

    /**
     * 5. Pesan darurat penemuan barang wajib divalidasi server.
     */
    public function test_lost_mode_message_is_required_by_server_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.lost-mode.update'), [
            'message' => '',
            'relay_phone' => '081299887766',
        ]);

        $response->assertSessionHasErrors('message');
    }

    /**
     * 6. Pesan darurat tidak boleh melebihi 250 karakter.
     */
    public function test_lost_mode_message_cannot_exceed_250_characters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.lost-mode.update'), [
            'message' => str_repeat('A', 251),
            'relay_phone' => '081299887766',
        ]);

        $response->assertSessionHasErrors('message');
    }

    /**
     * 7. Nomor relay divalidasi wajib dan memiliki panjang minimal.
     */
    public function test_relay_phone_is_required_and_validated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.lost-mode.update'), [
            'message' => 'Pesan darurat sah',
            'relay_phone' => '123', // Kurang dari 8 digit
        ]);

        $response->assertSessionHasErrors('relay_phone');
    }

    /**
     * 8. Reward opsional dapat dikosongkan.
     */
    public function test_reward_is_optional_and_can_be_empty(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.lost-mode.update'), [
            'message' => 'Barang saya tertinggal di warkop',
            'relay_phone' => '081234567890',
            'reward' => null,
            'form_enabled' => '1',
            'share_location' => '1',
        ]);

        $response->assertRedirect(route('user.lost-mode.edit'));
        $response->assertSessionHas('status', 'Pengaturan Mode Hilang berhasil disimpan.');

        $this->assertDatabaseHas('mode_data', [
            'user_id' => $user->id,
            'mode' => 'hilang',
        ]);
    }

    /**
     * 9. Submit valid menyimpan payload ke tabel mode_data dan mengaktifkan mode hilang di profile.
     */
    public function test_valid_submission_saves_to_mode_data_and_updates_profile(): void
    {
        $user = User::factory()->create();
        Profile::create([
            'user_id' => $user->id,
            'display_name' => $user->name,
            'active_mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)->post(route('user.lost-mode.update'), [
            'message' => 'Kunci motor hilang di sekitar Pantai Losari',
            'relay_phone' => '081122334455',
            'reward' => 'Rp 50.000',
            'form_enabled' => '1',
            'share_location' => '1',
            'activate_mode' => '1',
        ]);

        $response->assertRedirect(route('user.lost-mode.edit'));

        $row = DB::table('mode_data')->where('user_id', $user->id)->where('mode', 'hilang')->first();
        $this->assertNotNull($row);

        $payload = json_decode($row->data, true);
        $this->assertEquals('Kunci motor hilang di sekitar Pantai Losari', $payload['message']);
        $this->assertEquals('081122334455', $payload['relay_phone']);
        $this->assertEquals('Rp 50.000', $payload['reward']);
        $this->assertTrue($payload['form_enabled']);
        $this->assertTrue($payload['share_location']);

        // Profile active_mode berhasil berganti ke 'hilang'
        $this->assertEquals('hilang', $user->fresh()->profile->active_mode);
    }

    /**
     * 10. Validation error mempertahankan input sebelumnya (old input).
     */
    public function test_validation_error_preserves_old_inputs(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.lost-mode.update'), [
            'message' => 'Pesan saya yang valid',
            'relay_phone' => '12', // Error
            'reward' => 'Traktir Makan',
        ]);

        $response->assertSessionHasErrors('relay_phone');
        $response->assertSessionHasInput('message', 'Pesan saya yang valid');
        $response->assertSessionHasInput('reward', 'Traktir Makan');
    }

    /**
     * 11. Copy privasi tidak mengklaim klaim zero-knowledge atau enkripsi fiktif.
     */
    public function test_privacy_copy_does_not_claim_unsupported_zero_knowledge(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.lost-mode.edit'));

        $response->assertStatus(200);
        $response->assertDontSee('Zero-Knowledge');
        $response->assertDontSee('Enkripsi 256-bit');
        $response->assertSee('Privasi Terlindungi');
        $response->assertSee('Nomor kontak pribadi pemilik tidak ditampilkan langsung');
    }

    /**
     * 12. Floating Box Modal pada Dashboard menampilkan pesan penemuan barang dan status unread.
     */
    public function test_owner_inbox_displays_lost_item_messages(): void
    {
        $user = User::factory()->create();
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Owner Testing',
            'active_mode' => 'hilang',
        ]);

        Tag::create([
            'tag_code' => 'FT-TEST01',
            'activation_code' => 'ACT-TEST01',
            'status' => 'aktif',
            'user_id' => $user->id,
        ]);

        $message = Message::create([
            'type' => 'lost_item',
            'user_id' => $user->id,
            'sender_name' => 'Budi Penemu',
            'location' => 'Kantin Teknik Unhas Gowa',
            'message' => 'Gantungan kunci ditemukan di meja kantin lantai 2.',
            'contact' => '082199887766',
            'read_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('modalLostReports');
        $response->assertSee('Laporan Penemuan Barang');
        $response->assertSee('1 Baru');
        $response->assertSee('Budi Penemu');
        $response->assertSee('Kantin Teknik Unhas Gowa');
        $response->assertSee('Gantungan kunci ditemukan di meja kantin lantai 2.');
        $response->assertSee('Hubungi Penemu (082199887766)');
        $response->assertSee('Tandai Dibaca');
    }

    /**
     * 13. Pemilik dapat menandai pesan sebagai dibaca via endpoint AJAX.
     */
    public function test_owner_can_mark_message_as_read_via_ajax(): void
    {
        $user = User::factory()->create();

        $message = Message::create([
            'type' => 'lost_item',
            'user_id' => $user->id,
            'sender_name' => 'Ahmad Penemu',
            'location' => 'Pantai Losari Makassar',
            'message' => 'Barang aman dititip di pos security.',
            'contact' => '081234567890',
            'read_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('user.lost-mode.messages.read', $message->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Pesan telah ditandai sebagai dibaca.',
            ]);

        $this->assertNotNull($message->fresh()->read_at);
    }

    /**
     * 14. Pemilik dapat menandai semua pesan sebagai dibaca sekaligus.
     */
    public function test_owner_can_mark_all_messages_as_read(): void
    {
        $user = User::factory()->create();

        $msg1 = Message::create([
            'type' => 'lost_item',
            'user_id' => $user->id,
            'sender_name' => 'Penemu 1',
            'location' => 'Lokasi 1',
            'message' => 'Pesan 1',
            'contact' => '0811111111',
            'read_at' => null,
            'created_at' => now(),
        ]);

        $msg2 = Message::create([
            'type' => 'lost_item',
            'user_id' => $user->id,
            'sender_name' => 'Penemu 2',
            'location' => 'Lokasi 2',
            'message' => 'Pesan 2',
            'contact' => '0822222222',
            'read_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('user.lost-mode.messages.readAll'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNotNull($msg1->fresh()->read_at);
        $this->assertNotNull($msg2->fresh()->read_at);
    }

    /**
     * 15. User lain tidak dapat menandai pesan milik orang lain sebagai dibaca.
     */
    public function test_other_user_cannot_mark_different_users_message(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $message = Message::create([
            'type' => 'lost_item',
            'user_id' => $user1->id,
            'sender_name' => 'Penemu',
            'location' => 'Lokasi',
            'message' => 'Pesan untuk user 1',
            'read_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user2)->postJson(route('user.lost-mode.messages.read', $message->id));

        $response->assertStatus(403);
        $this->assertNull($message->fresh()->read_at);
    }

    /**
     * 16. Dashboard pemilik menampilkan banner notifikasi jika ada pesan penemuan belum dibaca.
     */
    public function test_dashboard_displays_emergency_alert_banner_when_unread_messages_exist(): void
    {
        $user = User::factory()->create();
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Owner Testing',
            'active_mode' => 'hilang',
        ]);
        Tag::create([
            'tag_code' => 'FT-TEST-DASH',
            'activation_code' => 'ACT-TEST-DASH',
            'status' => 'aktif',
            'user_id' => $user->id,
            'mode' => 'hilang',
        ]);

        Message::create([
            'type' => 'lost_item',
            'user_id' => $user->id,
            'sender_name' => 'Penemu Baik',
            'location' => 'Makassar',
            'message' => 'Kunci ditemukan.',
            'contact' => '081234567890',
            'read_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pemberitahuan Barang Ditemukan!');
        $response->assertSee('1 Baru');
        $response->assertSee('Buka Laporan');
    }
}
