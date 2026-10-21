<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\FinancialEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_from_settings(): void
    {
        $response = $this->get(route('admin.settings'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_all_settings_tabs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Tab: General
        $resGeneral = $this->actingAs($admin)->get(route('admin.settings', ['tab' => 'general']));
        $resGeneral->assertStatus(200);
        $resGeneral->assertSee('Profil Pengelola');

        // Tab: Users
        $resUsers = $this->actingAs($admin)->get(route('admin.settings', ['tab' => 'users']));
        $resUsers->assertStatus(200);
        $resUsers->assertSee('Daftar Akun Pengguna');

        // Tab: Designs
        $resDesigns = $this->actingAs($admin)->get(route('admin.settings', ['tab' => 'designs']));
        $resDesigns->assertStatus(200);
        $resDesigns->assertSee('Galeri Template Motif Akrilik');

        // Tab: Financial
        $resFinance = $this->actingAs($admin)->get(route('admin.settings', ['tab' => 'financial']));
        $resFinance->assertStatus(200);
        $resFinance->assertSee('Total Pemasukan Kas');
    }

    public function test_admin_can_update_general_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'name' => 'Super Administrator MaccaTech',
            'jabatan_admin' => 'Head of Core Ops',
            'email' => 'head.ops@flexitag.id',
            'no_wa_cs_pusat' => '+6281234567899',
            'relay_wa_otomatis_aktif' => '1',
            'notifikasi_email_pesanan' => '1',
            'mode_maintenance' => '0',
        ]);

        $response->assertRedirect(route('admin.settings', ['tab' => 'general']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Super Administrator MaccaTech',
            'email' => 'head.ops@flexitag.id',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'no_wa_cs_pusat',
            'value' => '+6281234567899',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'update_system_settings',
        ]);
    }

    public function test_admin_can_create_and_toggle_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $createResponse = $this->actingAs($admin)->post(route('admin.settings.users.store'), [
            'name' => 'Staff Perakitan Baru',
            'email' => 'staff.baru@flexitag.id',
            'password' => 'secret123',
            'role' => 'staff',
            'jabatan_admin' => 'Divisi Hardware',
            'no_whatsapp' => '081299998888',
        ]);

        $createResponse->assertRedirect(route('admin.settings', ['tab' => 'users']));
        $createResponse->assertSessionHas('success');

        $createdUser = User::where('email', 'staff.baru@flexitag.id')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->is_active);

        // Toggle status
        $toggleResponse = $this->actingAs($admin)->post(route('admin.settings.users.toggleStatus', $createdUser->id));
        $toggleResponse->assertRedirect(route('admin.settings', ['tab' => 'users']));

        $this->assertFalse($createdUser->fresh()->is_active);

        // Audit log verified
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create_user',
        ]);
    }

    public function test_admin_can_create_and_delete_motif_design(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $storeResponse = $this->actingAs($admin)->post(route('admin.settings.designs.store'), [
            'name' => 'Gantungan Kunci Edisi Balla Lompoa',
            'category' => 'motif_lokal',
        ]);

        $storeResponse->assertRedirect(route('admin.settings', ['tab' => 'designs']));
        $storeResponse->assertSessionHas('success');

        $design = Design::where('name', 'Gantungan Kunci Edisi Balla Lompoa')->first();
        $this->assertNotNull($design);

        // Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.settings.designs.destroy', $design->id));
        $deleteResponse->assertRedirect(route('admin.settings', ['tab' => 'designs']));

        $this->assertDatabaseMissing('designs', [
            'id' => $design->id,
        ]);
    }

    public function test_admin_can_record_and_delete_financial_entry(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $storeResponse = $this->actingAs($admin)->post(route('admin.settings.financial.store'), [
            'type' => 'pemasukan',
            'amount' => 175000,
            'category' => 'Penjualan Ganci Komunitas Unhas',
            'entry_date' => now()->toDateString(),
            'note' => 'Pembayaran 5 pcs ganci Toraja Pa\'ssulan',
        ]);

        $storeResponse->assertRedirect(route('admin.settings', ['tab' => 'financial']));
        $storeResponse->assertSessionHas('success');

        $entry = FinancialEntry::where('category', 'Penjualan Ganci Komunitas Unhas')->first();
        $this->assertNotNull($entry);
        $this->assertEquals(175000, (float) $entry->amount);

        // Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.settings.financial.destroy', $entry->id));
        $deleteResponse->assertRedirect(route('admin.settings', ['tab' => 'financial']));

        $this->assertDatabaseMissing('financial_entries', [
            'id' => $entry->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'record_financial',
        ]);
    }
}
