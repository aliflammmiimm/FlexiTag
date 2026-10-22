<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Tag;
use App\Models\Tap;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserAnalyticsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Guest diarahkan ke halaman login jika membuka /analytics.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('user.analytics'));
        $response->assertRedirect(route('login'));
    }

    /**
     * 2. User terautentikasi dapat melihat analytics miliknya.
     */
    public function test_authenticated_user_can_view_own_analytics(): void
    {
        $user = User::factory()->create([
            'name' => 'Fadhil Makassar',
            'role' => 'user',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'display_name' => 'Fadhil Makassar',
            'active_mode' => 'kampus',
        ]);

        $tag = Tag::create([
            'tag_code' => 'FT-TEST01',
            'activation_code' => 'ACT-001',
            'status' => 'aktif',
            'user_id' => $user->id,
            'activated_at' => now(),
        ]);

        Tap::create([
            'tag_id' => $tag->id,
            'tapped_at' => now(),
            'source' => 'nfc',
            'region' => 'Kota Makassar',
        ]);

        $response = $this->actingAs($user)->get(route('user.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Analytics');
        $response->assertSee('Statistik Interaksi');
        $response->assertSee('TOTAL TAP');
        $response->assertSee('MINGGU INI');
        $response->assertSee('Aktivitas Tap Harian');
        $response->assertSee('Distribusi Mode NFC');
        $response->assertSee('HARDWARE TERAKTIF');
        $response->assertSee('FT-TEST01');
    }

    /**
     * 3. User tidak dapat melihat data analytics user lain (Data Isolation).
     */
    public function test_user_cannot_see_other_user_taps_or_hardware(): void
    {
        $userA = User::factory()->create(['name' => 'User A']);
        $userB = User::factory()->create(['name' => 'User B']);

        // Tag milik User B
        $tagB = Tag::create([
            'tag_code' => 'FT-USER-B',
            'activation_code' => 'ACT-USER-B',
            'status' => 'aktif',
            'user_id' => $userB->id,
            'activated_at' => now(),
        ]);

        // Buat 10 tap untuk User B
        for ($i = 0; $i < 10; $i++) {
            Tap::create([
                'tag_id' => $tagB->id,
                'tapped_at' => now(),
                'source' => 'nfc',
                'region' => 'Kab. Gowa',
            ]);
        }

        // Tag milik User A (hanya 1 tap)
        $tagA = Tag::create([
            'tag_code' => 'FT-USER-A',
            'activation_code' => 'ACT-USER-A',
            'status' => 'aktif',
            'user_id' => $userA->id,
            'activated_at' => now(),
        ]);

        Tap::create([
            'tag_id' => $tagA->id,
            'tapped_at' => now(),
            'source' => 'nfc',
            'region' => 'Kota Makassar',
        ]);

        $response = $this->actingAs($userA)->get(route('user.analytics'));

        $response->assertStatus(200);
        // User A hanya melihat 1 tap miliknya, bukan 10 tap milik User B
        $response->assertSee('FT-USER-A');
        $response->assertDontSee('FT-USER-B');
    }

    /**
     * 4. Parameter rentang waktu 7d, 30d, dan month valid.
     */
    public function test_valid_periods_are_accepted(): void
    {
        $user = User::factory()->create();

        foreach (['7d', '30d', 'month'] as $period) {
            $response = $this->actingAs($user)->get(route('user.analytics', ['period' => $period]));
            $response->assertStatus(200);
        }
    }

    /**
     * 5. Parameter invalid kembali ke default 7d.
     */
    public function test_invalid_period_falls_back_to_7d(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.analytics', ['period' => 'invalid_random_string']));
        $response->assertStatus(200);
        // Default 7 Hari Terakhir terseleksi
        $response->assertSee('7 Hari Terakhir');
    }

    /**
     * 6. Chart aktivitas selalu memiliki tepat 7 hari termasuk hari tanpa tap.
     */
    public function test_daily_activity_always_has_seven_days_with_zeros(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'FT-ZERO',
            'activation_code' => 'ACT-ZERO',
            'status' => 'aktif',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.analytics', ['period' => '7d']));

        $response->assertStatus(200);
        $dailyActivity = $response->viewData('dailyActivity');

        $this->assertCount(7, $dailyActivity);
        foreach ($dailyActivity as $day) {
            $this->assertArrayHasKey('day_label', $day);
            $this->assertArrayHasKey('count', $day);
            $this->assertIsInt($day['count']);
        }
    }

    /**
     * 7. User tanpa tap tidak menghasilkan error (empty state bersih).
     */
    public function test_user_without_taps_does_not_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.analytics'));

        $response->assertStatus(200);
        $response->assertSee('0');
        $response->assertSee('Belum ada pembanding');
        $response->assertSee('Belum ada aktivitas tap pada periode ini.');
    }

    /**
     * 8. Total tap sesuai dengan data aktual di database.
     */
    public function test_total_taps_matches_database(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'FT-COUNT01',
            'activation_code' => 'ACT-COUNT',
            'status' => 'aktif',
            'user_id' => $user->id,
        ]);

        Tap::create(['tag_id' => $tag->id, 'tapped_at' => now()]);
        Tap::create(['tag_id' => $tag->id, 'tapped_at' => now()->subDay()]);
        Tap::create(['tag_id' => $tag->id, 'tapped_at' => now()->subDays(2)]);

        $response = $this->actingAs($user)->get(route('user.analytics', ['period' => '7d']));

        $response->assertStatus(200);
        $this->assertEquals(3, $response->viewData('totalTaps'));
    }

    /**
     * 9. Hardware teraktif hanya berasal dari tag milik user.
     */
    public function test_active_hardware_only_belongs_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'tag_code' => 'FT-OWNER-TAG',
            'activation_code' => 'ACT-OWNER',
            'status' => 'aktif',
            'user_id' => $user->id,
        ]);

        Tap::create([
            'tag_id' => $tag->id,
            'tapped_at' => now(),
            'region' => 'Kota Makassar',
        ]);

        $response = $this->actingAs($user)->get(route('user.analytics'));

        $response->assertStatus(200);
        $activeHardware = $response->viewData('activeHardware');

        $this->assertNotNull($activeHardware);
        $this->assertEquals('FT-OWNER-TAG', $activeHardware['tag_code']);
        $this->assertEquals('Kota Makassar', $activeHardware['location']);
    }

    /**
     * 10. Route dapat diakses dan view yang benar di-render.
     */
    public function test_route_resolves_and_renders_correct_view(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/analytics');

        $response->assertStatus(200);
        $response->assertViewIs('user.analytics');
    }
}
