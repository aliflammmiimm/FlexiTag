<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\FinancialEntry;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
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
        $resDesigns->assertSee('<img', false);

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

    public function test_admin_can_update_financial_entry(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $entry = FinancialEntry::create([
            'type' => 'pengeluaran',
            'amount' => 150000,
            'category' => 'Bahan Baku Akrilik',
            'entry_date' => now()->toDateString(),
            'note' => 'Pembelian akrilik bening 3mm',
        ]);

        $updateResponse = $this->actingAs($admin)->put(route('admin.settings.financial.update', $entry->id), [
            'type' => 'pengeluaran',
            'amount' => 175000,
            'category' => 'Bahan Baku Akrilik & Resin',
            'entry_date' => now()->toDateString(),
            'note' => 'Pembelian akrilik bening 3mm + lem resin UV',
        ]);

        $updateResponse->assertRedirect(route('admin.settings', ['tab' => 'financial']));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('financial_entries', [
            'id' => $entry->id,
            'amount' => 175000,
            'category' => 'Bahan Baku Akrilik & Resin',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'update_financial',
        ]);
    }

    public function test_admin_can_search_and_filter_financial_entries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        FinancialEntry::create([
            'type' => 'pemasukan',
            'amount' => 200000,
            'category' => 'Penjualan Komunitas Sepeda Toraja',
            'entry_date' => now()->toDateString(),
            'note' => 'Pesanan batch 4 unit ganci',
        ]);

        FinancialEntry::create([
            'type' => 'pengeluaran',
            'amount' => 95000,
            'category' => 'Pembelian Chip NFC NTAG213 High Quality',
            'entry_date' => now()->toDateString(),
            'note' => 'Supplier chip Jakarta',
        ]);

        // Filter by type: pemasukan
        $filterResponse = $this->actingAs($admin)->get(route('admin.settings', [
            'tab' => 'financial',
            'finance_type' => 'pemasukan',
        ]));

        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Penjualan Komunitas Sepeda Toraja');

        // Search by keyword
        $searchResponse = $this->actingAs($admin)->get(route('admin.settings', [
            'tab' => 'financial',
            'search_finance' => 'NTAG213',
        ]));

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Pembelian Chip NFC NTAG213 High Quality');
    }

    public function test_admin_can_sync_paid_orders_to_financial_entries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $order = Order::create([
            'order_no' => 'ORD-SYNC-'.rand(1000, 9999),
            'channel' => 'online',
            'customer_name' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'email' => 'budi@example.com',
            'delivery_method' => 'ambil',
            'shape' => 'bulat',
            'size' => 'standar',
            'color' => 'Merah Toraja',
            'quantity' => 2,
            'unit_price' => 55000,
            'total' => 110000,
            'status' => Order::STATUS_SELESAI,
        ]);

        // Ensure financial entry not present yet
        $this->assertFalse(FinancialEntry::where('note', 'like', "%{$order->order_no}%")->exists());

        // Call sync
        $syncResponse = $this->actingAs($admin)->post(route('admin.settings.financial.syncOrders'));
        $syncResponse->assertRedirect(route('admin.settings', ['tab' => 'financial']));
        $syncResponse->assertSessionHas('success');

        // Verify financial entry created
        $entry = FinancialEntry::where('note', 'like', "%{$order->order_no}%")->first();
        $this->assertNotNull($entry);
        $this->assertEquals(110000, (float) $entry->amount);
        $this->assertEquals('pemasukan', $entry->type);
        $this->assertStringContainsString('Penjualan Ganci (Online Web)', $entry->category);
    }

    public function test_admin_can_export_financial_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        FinancialEntry::create([
            'type' => 'pemasukan',
            'amount' => 50000,
            'category' => 'Penjualan Ganci Uji CSV',
            'entry_date' => now()->toDateString(),
            'note' => 'Catatan uji CSV',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.settings.financial.exportCsv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_order_status_update_automatically_records_financial_entry(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $order = Order::create([
            'order_no' => 'ORD-AUTO-'.rand(1000, 9999),
            'channel' => 'offline',
            'customer_name' => 'Rahmat Hidayat',
            'whatsapp' => '082199887766',
            'email' => 'rahmat@example.com',
            'delivery_method' => 'ambil',
            'shape' => 'persegi',
            'size' => 'standar',
            'color' => 'Hitam Elegan',
            'quantity' => 1,
            'unit_price' => 60000,
            'total' => 60000,
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $this->assertFalse(FinancialEntry::where('note', 'like', "%{$order->order_no}%")->exists());

        // Update status to pembayaran_dikonfirmasi
        $response = $this->actingAs($admin)->post(route('admin.orders.status', $order->id), [
            'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
            'note' => 'Pembayaran tunai kasir diterima',
        ]);

        $response->assertSessionHas('success');

        // Check that financial entry is automatically recorded
        $entry = FinancialEntry::where('note', 'like', "%{$order->order_no}%")->first();
        $this->assertNotNull($entry);
        $this->assertEquals(60000, (float) $entry->amount);
        $this->assertEquals('pemasukan', $entry->type);
        $this->assertEquals('Penjualan Ganci (Offline Kasir)', $entry->category);
    }

    public function test_admin_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.submit'), [
                'email' => 'wrong.admin@flexitag.id',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'wrong.admin@flexitag.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan masuk admin', session('errors')->first('email'));
    }

    public function test_maintenance_mode_blocks_public_and_allows_admin(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'mode_maintenance'],
            ['value' => '1', 'updated_at' => now()]
        );

        // Guest accessing public page receives 503 Maintenance
        $guestResponse = $this->get(route('home'));
        $guestResponse->assertStatus(503);
        $guestResponse->assertSee('Sistem Sedang Ditingkatkan');

        // Admin login page remains accessible
        $adminLoginResponse = $this->get(route('admin.login'));
        $adminLoginResponse->assertStatus(200);

        // Authenticated admin can access home page even during maintenance
        $admin = User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)->get(route('home'));
        $adminResponse->assertStatus(200);

        // Turn off maintenance mode
        DB::table('settings')->updateOrInsert(
            ['key' => 'mode_maintenance'],
            ['value' => '0', 'updated_at' => now()]
        );

        $normalResponse = $this->get(route('home'));
        $normalResponse->assertStatus(200);
    }
}
