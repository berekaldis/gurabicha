<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    public function test_can_submit_feedback_via_api(): void
    {
        $response = $this->postJson('/api/feedback', [
            'name' => 'Abebe Patron',
            'phone' => '+251911223344',
            'category' => 'ACOUSTIC',
            'rating' => 5,
            'message' => 'The acoustic session on Saturday was incredible! Loved the vocals.',
            'source' => 'MINIAPP',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('feedbacks', [
            'name' => 'Abebe Patron',
            'category' => 'ACOUSTIC',
            'rating' => 5,
            'status' => 'NEW',
            'source' => 'MINIAPP',
        ]);
    }

    public function test_telegram_bot_handles_feedback_command(): void
    {
        /** @var TelegramBotService $service */
        $service = app(TelegramBotService::class);

        // User submits feedback command
        $update = [
            'message' => [
                'chat' => ['id' => 99887766],
                'from' => [
                    'first_name' => 'Dawit',
                    'last_name' => 'K',
                    'username' => 'dawit_k',
                ],
                'text' => '/feedback Please add more craft beers to the menu!',
            ],
        ];

        $service->handleUpdate($update);

        $this->assertDatabaseHas('feedbacks', [
            'telegram_chat_id' => '99887766',
            'telegram_username' => 'dawit_k',
            'message' => 'Please add more craft beers to the menu!',
            'source' => 'BOT',
        ]);
    }

    public function test_admin_can_view_and_update_feedback(): void
    {
        $user = User::factory()->create([
            'role' => 'ADMIN',
        ]);

        $feedback = Feedback::create([
            'name' => 'Sara Fan',
            'category' => 'FOOD',
            'rating' => 4,
            'message' => 'Wings were great, maybe a bit spicier next time.',
            'status' => 'NEW',
            'source' => 'MINIAPP',
        ]);

        $response = $this->actingAs($user)->get(route('admin.feedbacks.index'));
        $response->assertStatus(200);
        $response->assertSee('Wings were great');

        // Update status
        $patchResponse = $this->actingAs($user)->patch(route('admin.feedbacks.status', $feedback), [
            'status' => 'RESOLVED',
            'admin_notes' => 'Discussed with head chef to calibrate wing spice levels.',
        ]);

        $patchResponse->assertRedirect();
        $this->assertDatabaseHas('feedbacks', [
            'id' => $feedback->id,
            'status' => 'RESOLVED',
            'admin_notes' => 'Discussed with head chef to calibrate wing spice levels.',
        ]);
    }
}
