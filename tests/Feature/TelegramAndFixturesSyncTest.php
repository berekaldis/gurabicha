<?php

namespace Tests\Feature;

use App\Models\SportMatch;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramAndFixturesSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        config([
            'services.telegram.bot_token' => '123456789:ABCDefghIJKlmnOPQrstUVwxyz',
            'services.telegram.bot_username' => 'GuraBichaAddisBot',
            'services.telegram.webapp_url' => 'http://localhost/Gurabicha/public',
        ]);
    }

    public function test_telegram_info_endpoint(): void
    {
        $response = $this->getJson('/api/telegram/info');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'configured',
                'bot_username',
                'webhook_url',
            ]);
    }

    public function test_telegram_webhook_start_command(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 999],
            ], 200),
        ]);

        $payload = [
            'update_id' => 10001,
            'message' => [
                'message_id' => 1,
                'from' => [
                    'id' => 123456,
                    'first_name' => 'Abebe',
                    'username' => 'abebe_k',
                ],
                'chat' => [
                    'id' => 123456,
                    'type' => 'private',
                ],
                'text' => '/start',
            ],
        ];

        $response = $this->postJson('/api/telegram/webhook', $payload);

        $response->assertStatus(200)
            ->assertJson(['ok' => true]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && stripos($request->body(), 'Gura Bicha') !== false
                && str_contains($request->body(), 'web_app');
        });
    }

    public function test_telegram_webhook_fixtures_command(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 999],
            ], 200),
        ]);

        $payload = [
            'update_id' => 10002,
            'message' => [
                'message_id' => 2,
                'from' => [
                    'id' => 123456,
                    'first_name' => 'Abebe',
                ],
                'chat' => [
                    'id' => 123456,
                    'type' => 'private',
                ],
                'text' => '/fixtures',
            ],
        ];

        $response = $this->postJson('/api/telegram/webhook', $payload);

        $response->assertStatus(200)
            ->assertJson(['ok' => true]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && (str_contains($request->body(), 'Fixtures') || str_contains($request->body(), 'Schedule'));
        });
    }

    public function test_telegram_webhook_menu_and_reserve_commands(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 999],
            ], 200),
        ]);

        // Menu command
        $responseMenu = $this->postJson('/api/telegram/webhook', [
            'update_id' => 10003,
            'message' => [
                'message_id' => 3,
                'from' => ['id' => 123456, 'first_name' => 'Abebe'],
                'chat' => ['id' => 123456, 'type' => 'private'],
                'text' => '/menu',
            ],
        ]);
        $responseMenu->assertStatus(200)->assertJson(['ok' => true]);

        // Reserve command
        $responseReserve = $this->postJson('/api/telegram/webhook', [
            'update_id' => 10004,
            'message' => [
                'message_id' => 4,
                'from' => ['id' => 123456, 'first_name' => 'Abebe'],
                'chat' => ['id' => 123456, 'type' => 'private'],
                'text' => '/reserve',
            ],
        ]);
        $responseReserve->assertStatus(200)->assertJson(['ok' => true]);
    }

    public function test_telegram_webhook_help_fallback(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 999],
            ], 200),
        ]);

        $payload = [
            'update_id' => 10005,
            'message' => [
                'message_id' => 5,
                'from' => ['id' => 123456, 'first_name' => 'Abebe'],
                'chat' => ['id' => 123456, 'type' => 'private'],
                'text' => 'What time do you open?',
            ],
        ];

        $response = $this->postJson('/api/telegram/webhook', $payload);

        $response->assertStatus(200)->assertJson(['ok' => true]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && (str_contains($request->body(), 'Gura Bicha') || str_contains($request->body(), 'Mexico'));
        });
    }

    public function test_fixtures_sync_api_endpoint(): void
    {
        Http::fake([
            'https://site.api.espn.com/*' => Http::response([
                'events' => [
                    [
                        'id' => 'espn-test-101',
                        'name' => 'Arsenal at Chelsea',
                        'shortName' => 'ARS @ CHE',
                        'date' => '2026-08-15T16:30:00Z',
                        'competitions' => [
                            [
                                'id' => 'comp-101',
                                'venue' => ['fullName' => 'Stamford Bridge'],
                                'status' => [
                                    'type' => [
                                        'name' => 'STATUS_SCHEDULED',
                                        'state' => 'pre',
                                        'detail' => 'Sat, Aug 15 5:30 PM EAT',
                                    ],
                                ],
                                'competitors' => [
                                    [
                                        'homeAway' => 'home',
                                        'score' => '0',
                                        'team' => [
                                            'displayName' => 'Chelsea',
                                            'logo' => 'https://example.com/chelsea.png',
                                        ],
                                    ],
                                    [
                                        'homeAway' => 'away',
                                        'score' => '0',
                                        'team' => [
                                            'displayName' => 'Arsenal',
                                            'logo' => 'https://example.com/arsenal.png',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/fixtures/sync');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('matches', [
            'homeTeam' => 'Chelsea',
            'awayTeam' => 'Arsenal',
        ]);
    }

    public function test_admin_can_sync_live_fixtures_from_internet(): void
    {
        Http::fake([
            'https://site.api.espn.com/*' => Http::response([
                'events' => [
                    [
                        'id' => 'espn-test-admin-1',
                        'name' => 'Liverpool at Manchester United',
                        'date' => '2026-08-22T15:00:00Z',
                        'competitions' => [
                            [
                                'venue' => ['fullName' => 'Old Trafford'],
                                'status' => [
                                    'type' => [
                                        'name' => 'STATUS_SCHEDULED',
                                        'state' => 'pre',
                                        'detail' => 'Sat, Aug 22 6:00 PM EAT',
                                    ],
                                ],
                                'competitors' => [
                                    [
                                        'homeAway' => 'home',
                                        'score' => '0',
                                        'team' => ['displayName' => 'Manchester United'],
                                    ],
                                    [
                                        'homeAway' => 'away',
                                        'score' => '0',
                                        'team' => ['displayName' => 'Liverpool'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)
            ->post('/admin/matches/sync-internet');

        $response->assertRedirect('/admin/matches');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('matches', [
            'homeTeam' => 'Man United',
            'awayTeam' => 'Liverpool',
        ]);
    }

    public function test_customer_fan_profile_stores_telegram_note(): void
    {
        $response = $this->postJson('/api/customer/profile', [
            'name' => 'Dawit Mengistu',
            'phone' => '+251911998877',
            'email' => 'dawit@example.com',
            'favorite_club' => 'Arsenal',
            'notify_matchday' => true,
            'notify_scores' => true,
            'notify_acoustic' => true,
            'notes' => 'Telegram: @dawit_m (ID: 987654321)',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Fan Club profile saved successfully!',
            ]);

        $this->assertDatabaseHas('customers', [
            'phone' => '+251911998877',
            'favorite_club' => 'Arsenal',
            'notes' => 'Telegram: @dawit_m (ID: 987654321)',
        ]);
    }
}
