<?php

namespace Tests\Feature;

use App\Models\ModeData;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TagModeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_update_tag_mode_via_ajax(): void
    {
        $user = User::factory()->create([
            'email' => 'mode.user.'.uniqid().'@test.com',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-MD-01',
            'activation_code' => 'KD-MD-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('user.merch.updateTagMode', $tag->id), [
                'mode' => 'kerja',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tag_id' => $tag->id,
                'mode' => 'kerja',
                'mode_label' => 'Mode Kerja',
            ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'mode' => 'kerja',
        ]);
    }

    public function test_user_cannot_update_another_users_tag_mode(): void
    {
        $user1 = User::factory()->create(['email' => 'user1.'.uniqid().'@test.com']);
        Tag::create([
            'tag_code' => 'FT-MD-01B',
            'activation_code' => 'KD-MD-01B',
            'user_id' => $user1->id,
            'status' => 'aktif',
            'mode' => 'kampus',
        ]);

        $user2 = User::factory()->create(['email' => 'user2.'.uniqid().'@test.com']);
        $tag2 = Tag::create([
            'tag_code' => 'FT-MD-02',
            'activation_code' => 'KD-MD-02',
            'user_id' => $user2->id,
            'status' => 'aktif',
            'mode' => 'kampus',
        ]);

        $response = $this->actingAs($user1)
            ->postJson(route('user.merch.updateTagMode', $tag2->id), [
                'mode' => 'kerja',
            ]);

        $response->assertStatus(404);

        $this->assertDatabaseHas('tags', [
            'id' => $tag2->id,
            'mode' => 'kampus',
        ]);
    }

    public function test_invalid_mode_fails_validation(): void
    {
        $user = User::factory()->create(['email' => 'user.val.'.uniqid().'@test.com']);

        $tag = Tag::create([
            'tag_code' => 'FT-MD-03',
            'activation_code' => 'KD-MD-03',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kampus',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('user.merch.updateTagMode', $tag->id), [
                'mode' => 'invalid_mode_xyz',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mode']);
    }

    public function test_multiple_tags_have_independent_modes_under_same_user(): void
    {
        $user = User::factory()->create(['email' => 'multi.tag.'.uniqid().'@test.com']);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Baharuddin Lopa',
            'headline' => 'Mahasiswa & Entrepreneur',
            'active_mode' => 'kampus',
        ]);

        $tag1 = Tag::create([
            'tag_code' => 'FT-KMP-01',
            'activation_code' => 'KD-KMP-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kerja',
        ]);

        $tag2 = Tag::create([
            'tag_code' => 'FT-UMKM-01',
            'activation_code' => 'KD-UMKM-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'umkm',
        ]);

        $response = $this->actingAs($user)->get(route('user.merch'));

        $response->assertStatus(200);
        $response->assertSee('FT-KMP-01');
        $response->assertSee('FT-UMKM-01');
        $response->assertSee('Mode Kerja');
        $response->assertSee('Mode UMKM');
    }

    public function test_public_profile_resolves_mode_of_specific_tag(): void
    {
        $user = User::factory()->create([
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat.multi.'.uniqid().'@test.com',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Rahmat Hidayat',
            'headline' => 'Founder & Developer',
            'active_mode' => 'kampus',
        ]);

        ModeData::create([
            'user_id' => $user->id,
            'mode' => 'umkm',
            'data' => [
                'nama_usaha' => 'Kopi Toraja Asli',
                'deskripsi' => 'Kopi arabika khas Toraja roasted fresh',
            ],
        ]);

        $tagKerja = Tag::create([
            'tag_code' => 'FT-TAG-KRJ',
            'activation_code' => 'KD-TAG-KRJ',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kerja',
        ]);

        $tagUmkm = Tag::create([
            'tag_code' => 'FT-TAG-UMK',
            'activation_code' => 'KD-TAG-UMK',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'umkm',
        ]);

        // Tag Kerja scan loads standard public profile in mode 'kerja'
        $responseKerja = $this->get(route('public.tag', $tagKerja->tag_code));
        $responseKerja->assertStatus(200);
        $responseKerja->assertViewIs('user.public_profile');
        $responseKerja->assertViewHas('activeMode', 'kerja');

        // Tag UMKM scan loads UMKM public profile view
        $responseUmkm = $this->get(route('public.tag', $tagUmkm->tag_code));
        $responseUmkm->assertStatus(200);
        $responseUmkm->assertViewIs('user.public_profile_umkm');
        $responseUmkm->assertSee('Kopi Toraja Asli');

        // Query parameter override ?mode=kampus still works as requested
        $responseOverride = $this->get(route('public.tag', [$tagUmkm->tag_code, 'mode' => 'kampus']));
        $responseOverride->assertStatus(200);
        $responseOverride->assertViewIs('user.public_profile');
        $responseOverride->assertViewHas('activeMode', 'kampus');
    }

    public function test_admin_tags_table_displays_synced_independent_tag_modes(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin.'.uniqid().'@test.com',
        ]);

        $user = User::factory()->create([
            'email' => 'client.'.uniqid().'@test.com',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Klien Bisnis',
            'active_mode' => 'kampus',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-ADM-UMK',
            'activation_code' => 'KD-ADM-UMK',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'umkm',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.tags'));

        $response->assertStatus(200);
        $response->assertSee('FT-ADM-UMK');
        $response->assertSee('Mode UMKM');
    }

    public function test_admin_can_update_tag_mode(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin.upd.'.uniqid().'@test.com',
        ]);

        $user = User::factory()->create([
            'email' => 'client.upd.'.uniqid().'@test.com',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-ADM-UPD',
            'activation_code' => 'KD-ADM-UPD',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kampus',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.tags.updateMode', $tag->id), [
            'mode' => 'kerja',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'mode' => 'kerja',
        ]);
    }

    public function test_switching_mode_on_home_dashboard_synchronizes_primary_tag_and_public_view(): void
    {
        $user = User::factory()->create(['email' => 'homesync.'.uniqid().'@test.com']);
        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Home Sync User',
            'headline' => 'Active Profile Sync',
            'active_mode' => 'kerja',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-SYNC-01',
            'activation_code' => 'KD-SYNC-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kerja',
            'activated_at' => now(),
        ]);

        // 1. Initially public view shows 'kerja'
        $responseInitial = $this->get(route('public.tag', $tag->tag_code));
        $responseInitial->assertStatus(200);
        $responseInitial->assertViewHas('activeMode', 'kerja');

        // 2. User switches to 'hilang' from dashboard home
        $switchResponse = $this->actingAs($user)->post(route('user.dashboard.switchMode'), [
            'mode' => 'hilang',
        ]);
        $switchResponse->assertRedirect(route('user.dashboard'));

        // Tag mode in database is synchronized to 'hilang'
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'mode' => 'hilang',
        ]);

        // 3. Public view now immediately displays 'hilang'
        $responseHilang = $this->get(route('public.tag', $tag->tag_code));
        $responseHilang->assertStatus(200);
        $responseHilang->assertViewHas('activeMode', 'hilang');
        $responseHilang->assertSee('MODE BARANG HILANG');
    }
}
