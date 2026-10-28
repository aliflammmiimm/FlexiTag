<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Tap;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_from_analytics(): void
    {
        $response = $this->get(route('admin.analytics'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_view_analytics_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Analitik Interaksi NFC');
        $response->assertSee('Total Tap Keseluruhan');
        $response->assertSee('Frekuensi Scan NFC Harian');
        $response->assertSee('Unduh Laporan (CSV)');
    }

    public function test_admin_analytics_is_synchronized_with_real_database_taps(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $tag = Tag::create([
            'tag_code' => 'FLX-AN-01',
            'activation_code' => 'KD-ANLT-01',
            'user_id' => $user->id,
            'status' => 'aktif',
            'mode' => 'umkm',
            'activated_at' => now()->subDays(2),
        ]);

        Tap::create([
            'tag_id' => $tag->id,
            'tapped_at' => now()->subDay(),
            'source' => 'nfc',
            'region' => 'Kota Makassar',
            'device_type' => 'Android Smartphone',
        ]);

        Tap::create([
            'tag_id' => $tag->id,
            'tapped_at' => now()->subHours(2),
            'source' => 'qr',
            'region' => 'Tana Toraja',
            'device_type' => 'iOS (iPhone)',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics', ['range' => '7_days']));

        $response->assertStatus(200);
        $response->assertViewHas(['metrics', 'trendData', 'modeBreakdown', 'regions', 'rangeLabel']);

        $metrics = $response->viewData('metrics');
        $this->assertNotEmpty($metrics['total_tap']);
        $this->assertNotEmpty($metrics['avg_daily_tap']);

        $modeBreakdown = $response->viewData('modeBreakdown');
        $this->assertIsArray($modeBreakdown);
        $this->assertCount(4, $modeBreakdown);

        $regions = $response->viewData('regions');
        $this->assertIsArray($regions);
        $this->assertNotEmpty($regions);
    }

    public function test_admin_analytics_supports_different_ranges(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 7 days
        $res7 = $this->actingAs($admin)->get(route('admin.analytics', ['range' => '7_days']));
        $res7->assertStatus(200);
        $res7->assertSee('7 Hari Terakhir');

        // 90 days
        $res90 = $this->actingAs($admin)->get(route('admin.analytics', ['range' => '90_days']));
        $res90->assertStatus(200);
        $res90->assertSee('90 Hari Terakhir');

        // Year
        $resYear = $this->actingAs($admin)->get(route('admin.analytics', ['range' => 'year']));
        $resYear->assertStatus(200);
        $resYear->assertSee('Tahun Ini');

        // All
        $resAll = $this->actingAs($admin)->get(route('admin.analytics', ['range' => 'all']));
        $resAll->assertStatus(200);
        $resAll->assertSee('Semua Waktu');
    }

    public function test_authenticated_admin_can_export_tap_telemetry_csv(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics.exportCsv', ['range' => '30_days']));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'export_telemetry_csv',
            'user_id' => $admin->id,
        ]);
    }
}
