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

    /**
     * FR-LST-01 & NFR-SEC-06: Mode Barang Hilang tidak mengekspos nomor telepon pemilik di HTML.
     */
    public function test_lost_mode_does_not_leak_owner_phone_number_in_html(): void
    {
        $user = User::factory()->create([
            'name' => 'Pemilik Rahasia',
            'email' => 'rahasia.'.uniqid().'@gmail.com',
            'no_whatsapp' => '081299887766',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Pemilik Rahasia',
            'active_mode' => 'hilang',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FLX-PRIV01',
            'activation_code' => 'KD-PRIV-01',
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);

        $response = $this->get(route('public.tag', $tag->tag_code));

        $response->assertStatus(200);
        $response->assertSee('MODE BARANG HILANG AKTIF');
        $response->assertSee('Hubungi Pemilik via Relay Aman');
        $response->assertSee('modalLostMessage');
        $response->assertSee('Laporkan Penyalahgunaan Profil');

        // NFR-SEC-06: Nomor telepon pribadi pemilik tidak boleh ada di kode sumber HTML publik
        $response->assertDontSee('https://wa.me/6281299887766');
        $response->assertDontSee('https://wa.me/081299887766');
        $response->assertDontSee('081299887766');
    }

    /**
     * FR-LST-01 & FR-LST-02: Penemu dapat mengirim pesan aman ke pemilik tag via relay.
     */
    public function test_finder_can_submit_lost_item_message_via_secure_relay(): void
    {
        $user = User::factory()->create([
            'name' => 'Pemilik Dompet',
            'email' => 'pemilik.'.uniqid().'@gmail.com',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Pemilik Dompet',
            'active_mode' => 'hilang',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FLX-RELAY1',
            'activation_code' => 'KD-RELAY-01',
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);

        $response = $this->post(route('public.tag.lost_message', $tag->tag_code), [
            'sender_name' => 'Rahmat Satpam',
            'location' => 'Lobi Gedung Rektorat UNM Gunungsari',
            'message' => 'Gantungan kunci dan dompet Anda kami amankan di meja resepsionis lantai 1.',
            'contact' => '085211223344',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('lost_message_success');

        $this->assertDatabaseHas('messages', [
            'type' => 'lost_item',
            'user_id' => $user->id,
            'sender_name' => 'Rahmat Satpam',
            'location' => 'Lobi Gedung Rektorat UNM Gunungsari',
            'message' => 'Gantungan kunci dan dompet Anda kami amankan di meja resepsionis lantai 1.',
            'contact' => '085211223344',
            'read_at' => null,
        ]);
    }

    /**
     * FR-LST-01: Validasi form pesan penemu mewajibkan lokasi dan isi pesan.
     */
    public function test_finder_lost_message_validation_requires_location_and_message(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'FLX-VAL01',
            'activation_code' => 'KD-VAL-01',
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);

        $response = $this->post(route('public.tag.lost_message', $tag->tag_code), [
            'sender_name' => 'Anonim',
            'location' => '',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['location', 'message']);
    }

    /**
     * FR-PUB-12: Pengunjung dapat mengirim laporan abuse pada profil publik.
     */
    public function test_visitor_can_submit_abuse_report_on_public_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'FLX-ABUSE1',
            'activation_code' => 'KD-ABUSE-01',
            'user_id' => $user->id,
            'status' => 'aktif',
        ]);

        $response = $this->post(route('public.tag.abuse_report', $tag->tag_code), [
            'sender_name' => 'Warga Peduli',
            'message' => 'Tautan di profil ini mengarah ke situs perjudian ilegal.',
            'contact' => 'pelapor@gmail.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('abuse_report_success');

        $this->assertDatabaseHas('messages', [
            'type' => 'abuse_report',
            'user_id' => $user->id,
            'sender_name' => 'Warga Peduli',
            'contact' => 'pelapor@gmail.com',
        ]);
    }

    /**
     * FR-ADM-09: Pengunjung dapat mengirim pesan kontak dari formulir landing page.
     */
    public function test_landing_page_contact_form_saves_to_messages_table(): void
    {
        $response = $this->post(route('contact.submit'), [
            'sender_name' => 'Fadli Rahman',
            'contact' => '081234567890',
            'location' => 'Makassar',
            'message' => 'Kami berminat memesan 150 pcs ganci custom akrilik motif Toraja.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('contact_success');

        $this->assertDatabaseHas('messages', [
            'type' => 'contact',
            'user_id' => null,
            'sender_name' => 'Fadli Rahman',
            'contact' => '081234567890',
            'location' => 'Makassar',
            'message' => 'Kami berminat memesan 150 pcs ganci custom akrilik motif Toraja.',
            'read_at' => null,
        ]);
    }
}
