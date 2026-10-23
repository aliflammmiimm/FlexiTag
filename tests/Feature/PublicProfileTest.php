<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_tag_profile_renders_successfully(): void
    {
        $user = User::factory()->create([
            'name' => 'Andi Muhammad',
            'email' => 'andi.test.'.uniqid().'@student.unm.ac.id',
            'no_whatsapp' => '+62 812-3456-7890',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Andi Muhammad',
            'headline' => 'Teknik Komputer • Universitas Negeri Makassar',
            'bio' => 'Angkatan 2023 • Makassar, Sulawesi Selatan',
            'photo_path' => 'images/avatar_andi.jpg',
            'theme' => 'sulawesi_heritage',
            'active_mode' => 'kampus',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FLX-ANDI01',
            'activation_code' => 'KD-ANDI-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'batch_id' => '#B-PA-ALLO',
        ]);

        Link::create([
            'user_id' => $user->id,
            'type' => 'academic',
            'label' => 'HIMA Teknik Komputer UNM|Pengurus Divisi Riset & Teknologi',
            'url' => 'https://himatekkom.unm.ac.id',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $response = $this->get(route('public.tag', $tag->tag_code));

        $response->assertStatus(200);
        $response->assertSee('Andi Muhammad');
        $response->assertSee('Teknik Komputer • Universitas Negeri Makassar');
        $response->assertSee('Simpan Kontak');
        $response->assertSee('Chat WhatsApp');
        $response->assertSee('HIMA Teknik Komputer UNM');
        $response->assertSee('Pengurus Divisi Riset & Teknologi');
        $response->assertSee('Tampilkan QR Tap & Bagikan');
        $response->assertSee('FlexiTag');
    }

    public function test_vcard_download_generates_valid_vcard(): void
    {
        $user = User::factory()->create([
            'name' => 'Andi Muhammad',
            'email' => 'andi.vcard.'.uniqid().'@student.unm.ac.id',
            'no_whatsapp' => '081234567890',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Andi Muhammad',
            'headline' => 'Teknik Komputer • Universitas Negeri Makassar',
            'active_mode' => 'kampus',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FLX-VCARD1',
            'activation_code' => 'KD-VCARD-01',
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);

        $response = $this->get(route('public.tag.vcard', $tag->tag_code));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/vcard; charset=utf-8');
        $this->assertStringContainsString('BEGIN:VCARD', $response->getContent());
        $this->assertStringContainsString('FN:Andi Muhammad', $response->getContent());
        $this->assertStringContainsString('END:VCARD', $response->getContent());
    }

    public function test_unactivated_tag_redirects_to_activation(): void
    {
        $tag = Tag::create([
            'tag_code' => 'FLX-UNACT1',
            'activation_code' => 'KD-UNACT-01',
            'status' => 'belum_aktif',
        ]);

        $response = $this->get(route('public.tag', $tag->tag_code));

        $response->assertRedirect(route('user.activation.show', ['step' => 2, 'tag_code' => $tag->tag_code]));
    }
}
