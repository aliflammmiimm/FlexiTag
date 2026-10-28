<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Tag;
use App\Models\Tap;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_dashboard_with_dynamic_mode_distribution(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        // Create tags with different modes
        Tag::create([
            'tag_code' => 'FLX-MD-KM',
            'activation_code' => 'KD-TEST-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kampus',
        ]);

        Tag::create([
            'tag_code' => 'FLX-MD-KJ',
            'activation_code' => 'KD-TEST-02',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kerja',
        ]);

        Tag::create([
            'tag_code' => 'FLX-MD-UM',
            'activation_code' => 'KD-TEST-03',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'umkm',
        ]);

        Tag::create([
            'tag_code' => 'FLX-MD-HL',
            'activation_code' => 'KD-TEST-04',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'hilang',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Distribusi Mode Pelanggan');
        $response->assertSee('Mode Kampus');
        $response->assertSee('Mode Kerja');
        $response->assertSee('Mode UMKM');
        $response->assertSee('Mode Barang Hilang');
        $response->assertViewHas('modeDistribution');
        $response->assertViewHas('totalActiveTagsCount');

        $distribution = $response->viewData('modeDistribution');
        $this->assertIsArray($distribution);
        $this->assertCount(4, $distribution);
    }

    public function test_admin_dashboard_calculates_dynamic_weekly_trends_and_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        // Create an order in the current week
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);

        $order = Order::create([
            'order_no' => 'ORD-TEST-999',
            'customer_name' => 'Daeng Uki Test',
            'whatsapp' => '081299990000',
            'email' => 'daeng.uki@example.com',
            'delivery_method' => 'kirim',
            'address' => 'Makassar',
            'shape' => 'Ganci Bulat',
            'size' => 'Diameter 4.5 cm',
            'color' => 'Hitam Doff',
            'quantity' => 3,
            'unit_price' => 35000,
            'total' => 105000,
            'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
        ]);
        $order->created_at = $startOfWeek->copy()->addDay();
        $order->save();

        $tag = Tag::create([
            'tag_code' => 'FLX-TR-01',
            'activation_code' => 'KD-TRND-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kerja',
            'activated_at' => $startOfWeek->copy()->addDay(),
        ]);

        Tap::create([
            'tag_id' => $tag->id,
            'tapped_at' => $startOfWeek->copy()->addDay(),
            'source' => 'nfc',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas(['weeklyTrends', 'periodLabel', 'metrics', 'avgTapPerTag']);

        $weeklyTrends = $response->viewData('weeklyTrends');
        $this->assertCount(7, $weeklyTrends);
        $this->assertEquals('Sen', $weeklyTrends[0]['day']);
        $this->assertEquals('Min', $weeklyTrends[6]['day']);

        // Check Tuesday (index 1) has positive counts
        $tuesday = $weeklyTrends[1];
        $this->assertGreaterThanOrEqual(1, $tuesday['penjualan']);
        $this->assertGreaterThanOrEqual(1, $tuesday['aktivasi']);
        $this->assertGreaterThan(0, $tuesday['penjualan_height']);
        $this->assertGreaterThan(0, $tuesday['aktivasi_height']);

        // Check metrics
        $metrics = $response->viewData('metrics');
        $this->assertStringContainsString('Rp', $metrics['total_pendapatan']);
        $this->assertNotEmpty($metrics['new_orders']);
        $this->assertGreaterThanOrEqual(1, $metrics['perlu_dikirim']);

        // Check rendered HTML contains dynamic indicators
        $response->assertSee('Tren Penjualan &amp; Aktivasi Mingguan', false);
        $response->assertSee('Rerata');
        $response->assertSee('tap / perangkat aktif');
    }

    public function test_admin_dashboard_supports_period_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Test Bulan Ini
        $resMonth = $this->actingAs($admin)->get(route('admin.dashboard', ['period' => 'bulan_ini']));
        $resMonth->assertStatus(200);
        $resMonth->assertSee('Bulan Ini');

        // Test Tahun Ini
        $resYear = $this->actingAs($admin)->get(route('admin.dashboard', ['period' => 'tahun_ini']));
        $resYear->assertStatus(200);
        $resYear->assertSee('Tahun Ini');

        // Test Semua Waktu
        $resAll = $this->actingAs($admin)->get(route('admin.dashboard', ['period' => 'semua']));
        $resAll->assertStatus(200);
        $resAll->assertSee('Semua Waktu');
    }

    public function test_admin_can_filter_tags_inventory_by_mode(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Pak Joko UMKM']);

        Tag::create([
            'tag_code' => 'FLX-FL-UM',
            'activation_code' => 'KD-FLTR-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'umkm',
        ]);

        Tag::create([
            'tag_code' => 'FLX-FL-KM',
            'activation_code' => 'KD-FLTR-02',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'kampus',
        ]);

        // Filter by mode umkm
        $response = $this->actingAs($admin)->get(route('admin.tags', ['mode' => 'umkm']));

        $response->assertStatus(200);
        $response->assertSee('FLX-FL-UM');
        $response->assertDontSee('FLX-FL-KM');
    }
}
