<?php

namespace Tests\Feature;

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
