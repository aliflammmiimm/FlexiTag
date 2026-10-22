<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TagActivationWizardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_landing_page_links_to_activation_wizard(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee(route('user.activation.show'));
    }

    public function test_guest_is_presented_with_step_one_account(): void
    {
        $response = $this->get(route('user.activation.show'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
        $response->assertSee('Masuk Akun');
        $response->assertSee('Status Tag: Belum Diaktivasi');
    }

    public function test_guest_cannot_access_step_two_directly(): void
    {
        $response = $this->get(route('user.activation.show', ['step' => 2]));

        $response->assertRedirect(route('user.activation.show', ['step' => 1]));
    }

    public function test_guest_can_register_inside_wizard_and_advance_to_step_two(): void
    {
        $response = $this->post(route('user.activation.auth'), [
            'auth_action' => 'register',
            'name' => 'Andi Muhammad Fadil',
            'email' => 'andi.fadil@example.com',
            'password' => 'password123',
            'agree_terms' => '1',
        ]);

        $response->assertRedirect(route('user.activation.show', ['step' => 2]));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'andi.fadil@example.com',
            'name' => 'Andi Muhammad Fadil',
        ]);
    }

    public function test_user_can_activate_valid_code_and_advance_to_step_three(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'a8k2x9',
            'activation_code' => 'KD-8821-XQ',
            'status' => 'belum_aktif',
            'user_id' => null,
            'produced_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('user.activation.store'), [
            'activation_code' => 'KD-8821-XQ',
        ]);

        $response->assertRedirect(route('user.activation.show', ['step' => 3]));
        $response->assertSessionHas('activation_toast');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);
    }

    public function test_user_can_also_activate_using_tag_code_directly(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'FLX-KM-099',
            'activation_code' => 'ACT-SECRET99',
            'status' => 'belum_aktif',
            'user_id' => null,
            'produced_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('user.activation.store'), [
            'activation_code' => 'FLX-KM-099',
        ]);

        $response->assertRedirect(route('user.activation.show', ['step' => 3]));

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);
    }

    public function test_user_can_save_initial_profile_and_advance_to_step_four(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'a8k2x9',
            'activation_code' => 'KD-8821-XQ',
            'status' => 'aktif',
            'user_id' => $user->id,
            'produced_at' => now(),
            'activated_at' => now(),
        ]);

        session(['activation_tag_id' => $tag->id]);

        $response = $this->actingAs($user)->post(route('user.activation.profile'), [
            'display_name' => 'Andi Muhammad',
            'headline' => 'Toraja Utara / Makassar',
            'active_mode' => 'kampus',
            'no_whatsapp' => '082199887711',
        ]);

        $response->assertRedirect(route('user.activation.show', ['step' => 4]));

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'display_name' => 'Andi Muhammad',
            'headline' => 'Toraja Utara / Makassar',
            'active_mode' => 'kampus',
        ]);

        $this->assertDatabaseHas('links', [
            'user_id' => $user->id,
            'type' => 'whatsapp',
        ]);
    }

    public function test_step_four_shows_celebration_and_public_link(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'a8k2x9',
            'activation_code' => 'KD-8821-XQ',
            'status' => 'aktif',
            'user_id' => $user->id,
            'produced_at' => now(),
            'activated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.activation.show', ['step' => 4]));

        $response->assertStatus(200);
        $response->assertSee('Selamat! FlexiTag Anda Sudah Aktif');
        $response->assertSee('flexitag.id/u/a8k2x9');
        $response->assertSee('QR Cadangan');
    }

    public function test_public_tag_profile_route_renders_and_logs_tap(): void
    {
        $user = User::factory()->create(['no_whatsapp' => '6282199887711']);
        $user->profile()->create([
            'display_name' => 'Andi Muhammad',
            'headline' => 'Toraja Utara / Makassar',
            'active_mode' => 'kampus',
        ]);

        $tag = Tag::create([
            'tag_code' => 'a8k2x9',
            'activation_code' => 'KD-8821-XQ',
            'status' => 'aktif',
            'user_id' => $user->id,
            'produced_at' => now(),
            'activated_at' => now(),
        ]);

        $response = $this->get(route('public.tag', ['tag_code' => 'a8k2x9']));

        $response->assertStatus(200);
        $response->assertSee('Andi Muhammad');
        $response->assertSee('Mode Kampus');

        $this->assertDatabaseHas('taps', [
            'tag_id' => $tag->id,
        ]);
    }
}
