<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\TelegramBroadcast;
use App\Models\TelegramSubscriber;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramConfigAndBroadcastTest extends TestCase
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

    public function test_unauthenticated_user_cannot_access_telegram_config(): void
    {
        $response = $this->get('/admin/telegram');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_telegram_config_page(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)->get('/admin/telegram');
        $response->assertStatus(200);
        $response->assertSee('Telegram Config');
        $response->assertSee('Telegram Broadcast Announcements');
        $response->assertSee('Telegram Bot Credentials');
        $response->assertSee('1-Click Announcement Presets');
    }

    public function test_admin_can_update_telegram_credentials(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();
        $originalToken = Setting::get('telegram_bot_token');
        $originalUsername = Setting::get('telegram_bot_username');
        $originalWebApp = Setting::get('telegram_webapp_url');

        $response = $this->actingAs($admin)->post('/admin/telegram/settings', [
            'telegram_bot_token' => '987654321:ZYXWvutSRQPonmlKJIhgfEDCba',
            'telegram_bot_username' => 'GuraBichaLiveBot',
            'telegram_webapp_url' => 'https://gurabicha.com/app',
        ]);

        $response->assertRedirect('/admin/telegram');
        $response->assertSessionHas('success');

        $this->assertEquals('GuraBichaLiveBot', Setting::get('telegram_bot_username'));
        $this->assertEquals('https://gurabicha.com/app', Setting::get('telegram_webapp_url'));

        // Restore settings so tests do not wipe real user settings
        Setting::set('telegram_bot_token', $originalToken);
        Setting::set('telegram_bot_username', $originalUsername);
        Setting::set('telegram_webapp_url', $originalWebApp);
    }

    public function test_admin_can_manage_webhook(): void
    {
        Http::fake([
            'https://api.telegram.org/*/setWebhook*' => Http::response(['ok' => true, 'description' => 'Webhook was set'], 200),
            'https://api.telegram.org/*/deleteWebhook*' => Http::response(['ok' => true, 'description' => 'Webhook was deleted'], 200),
        ]);

        $admin = User::where('email', 'admin@gurabicha.com')->first();
        $originalWebhook = Setting::get('telegram_webhook_url');
        $originalWebApp = Setting::get('telegram_webapp_url');

        // 1. Set webhook
        $setRes = $this->actingAs($admin)->post('/admin/telegram/webhook/set', [
            'url' => 'https://custom-tunnel.loca.lt/api/telegram/webhook',
        ]);
        $setRes->assertRedirect('/admin/telegram');
        $setRes->assertSessionHas('success');

        $this->assertEquals('https://custom-tunnel.loca.lt/api/telegram/webhook', Setting::get('telegram_webhook_url'));

        // Verify the Telegram config page shows the updated webhook URL
        $pageRes = $this->actingAs($admin)->get('/admin/telegram');
        $pageRes->assertSee('https://custom-tunnel.loca.lt/api/telegram/webhook');

        // 2. Delete webhook
        $delRes = $this->actingAs($admin)->post('/admin/telegram/webhook/delete');
        $delRes->assertRedirect('/admin/telegram');
        $delRes->assertSessionHas('success');

        $this->assertEmpty(Setting::get('telegram_webhook_url'));

        // Restore original webhook so tests do not wipe real settings
        Setting::set('telegram_webhook_url', $originalWebhook);
        Setting::set('telegram_webapp_url', $originalWebApp);
    }

    public function test_admin_can_send_broadcast_announcement_to_all_subscribers(): void
    {
        Http::fake([
            'https://api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 9999],
            ], 200),
        ]);

        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)->post('/admin/telegram/broadcast', [
            'category' => 'FIXTURE',
            'target_audience' => 'ALL',
            'title' => '⚽ London Derby Screening: Arsenal vs Chelsea',
            'message' => 'Huge Premier League clash tonight on 4K projection screens with stadium audio! Enjoy cold draft beers and kitfo platters.',
            'action_button_type' => 'FIXTURES',
        ]);

        $response->assertRedirect('/admin/telegram');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('telegram_broadcasts', [
            'title' => '⚽ London Derby Screening: Arsenal vs Chelsea',
            'category' => 'FIXTURE',
            'target_audience' => 'ALL',
            'status' => 'SENT',
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request->body(), 'London Derby');
        });
    }

    public function test_admin_can_send_targeted_broadcast_to_specific_club_fans(): void
    {
        Http::fake([
            'https://api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 9998],
            ], 200),
        ]);

        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)->post('/admin/telegram/broadcast', [
            'category' => 'EVENT',
            'target_audience' => 'Arsenal',
            'title' => 'Gunners Matchday Watch Party',
            'message' => 'Calling all Arsenal fans in Addis Ababa! Join us for a special pre-match acoustic groove session and matchday atmosphere.',
            'action_button_type' => 'MINIAPP',
        ]);

        $response->assertRedirect('/admin/telegram');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('telegram_broadcasts', [
            'title' => 'Gunners Matchday Watch Party',
            'target_audience' => 'Arsenal',
        ]);
    }

    public function test_incoming_telegram_webhook_registers_subscriber(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 123],
            ], 200),
        ]);

        $payload = [
            'update_id' => 50001,
            'message' => [
                'message_id' => 1,
                'from' => [
                    'id' => 888777,
                    'first_name' => 'Kidan',
                    'last_name' => 'Woldemariam',
                    'username' => 'kidan_w',
                ],
                'chat' => [
                    'id' => 888777,
                    'type' => 'private',
                ],
                'text' => '/start',
            ],
        ];

        $response = $this->postJson('/api/telegram/webhook', $payload);
        $response->assertStatus(200)->assertJson(['ok' => true]);

        $this->assertDatabaseHas('telegram_subscribers', [
            'chat_id' => '888777',
            'first_name' => 'Kidan',
            'username' => 'kidan_w',
            'is_active' => true,
        ]);
    }

    public function test_venue_settings_page_still_works_without_telegram_card(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSee('Venue Settings');
        $response->assertSee('Open Telegram Config');
    }

    public function test_admin_can_update_admin_chat_id(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();
        $response = $this->actingAs($admin)->post('/admin/telegram/settings', [
            'telegram_admin_chat_id' => '123456789',
        ]);
        $response->assertRedirect('/admin/telegram');
        $response->assertSessionHas('success');
        $this->assertEquals('123456789', Setting::get('telegram_admin_chat_id'));
    }

    public function test_admin_can_send_test_notification_to_admin_chat(): void
    {
        Http::fake([
            'https://api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 77777],
            ], 200),
        ]);

        Setting::set('telegram_admin_chat_id', '99887766');
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)->post('/admin/telegram/test-notification');
        $response->assertRedirect('/admin/telegram');
        $response->assertSessionHas('success');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request->body(), '99887766')
                && str_contains($request->body(), 'Admin Notification System Test');
        });
    }

    public function test_admin_test_notification_fails_gracefully_when_chat_id_is_empty(): void
    {
        Setting::set('telegram_admin_chat_id', '');
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)->post('/admin/telegram/test-notification');
        $response->assertRedirect('/admin/telegram');
        $response->assertSessionHas('error');
    }
}
