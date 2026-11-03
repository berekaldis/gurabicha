<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\TelegramSubscriber;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerProfileController extends Controller
{
    public function __construct(
        protected TelegramBotService $botService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $phone = $request->query('phone');
        $chatId = $request->query('telegram_chat_id') ?? $request->query('chat_id');
        $username = $request->query('telegram_username');

        $query = Customer::query();

        if ($chatId) {
            $query->where('telegram_chat_id', (string) $chatId);
        } elseif ($phone) {
            $query->where('phone', trim($phone));
        } elseif ($username) {
            $query->where('telegram_username', ltrim(trim($username), '@'));
        } else {
            return response()->json(['error' => 'Phone or Telegram ID is required.'], 400);
        }

        $customer = $query->first();

        return response()->json(['customer' => $customer]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'telegram_chat_id' => ['nullable', 'string', 'max:50'],
            'telegram_username' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'favorite_club' => ['nullable', 'string', 'max:100'],
            'notify_matchday' => ['nullable', 'boolean'],
            'notify_scores' => ['nullable', 'boolean'],
            'notify_acoustic' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $chatId = !empty($validated['telegram_chat_id']) ? (string) $validated['telegram_chat_id'] : null;
        $username = !empty($validated['telegram_username']) ? ltrim(trim($validated['telegram_username']), '@') : null;
        $phone = !empty($validated['phone']) ? trim($validated['phone']) : null;

        // Locate existing customer by telegram_chat_id, phone, or username
        $customer = null;
        if ($chatId) {
            $customer = Customer::where('telegram_chat_id', $chatId)->first();
        }
        if (!$customer && $phone) {
            $customer = Customer::where('phone', $phone)->first();
        }
        if (!$customer && $username) {
            $customer = Customer::where('telegram_username', $username)->first();
        }

        if (!$customer) {
            $customer = new Customer();
        }

        $customer->name = $validated['name'];
        if ($phone) {
            $customer->phone = $phone;
        }
        if ($chatId) {
            $customer->telegram_chat_id = $chatId;
        }
        if ($username) {
            $customer->telegram_username = $username;
        }
        $customer->email = $validated['email'] ?? $customer->email;
        $customer->favorite_club = $validated['favorite_club'] ?? $customer->favorite_club;
        $customer->notify_matchday = $request->boolean('notify_matchday', true);
        $customer->notify_scores = $request->boolean('notify_scores', true);
        $customer->notify_acoustic = $request->boolean('notify_acoustic', true);
        $customer->notes = $validated['notes'] ?? $customer->notes;
        $customer->save();

        // Also sync with TelegramSubscriber
        if ($chatId) {
            try {
                TelegramSubscriber::updateOrCreate(
                    ['chat_id' => $chatId],
                    [
                        'first_name' => $customer->name,
                        'username' => $username,
                        'is_active' => true,
                        'last_interaction_at' => now(),
                    ]
                );

                // Dispatch direct confirmation message on Telegram
                $club = $customer->favorite_club ?: 'your favorite club';
                $text = "🎉 <b>Welcome to Gura Bicha Fan Club!</b>\n\n"
                      . "Selam <b>" . htmlspecialchars($customer->name) . "</b>! 👋 You are now officially registered as a <b>" . htmlspecialchars($club) . "</b> supporter. ⚽\n\n"
                      . "We will send your club's matchday kickoffs, live scores, and acoustic night invites right here on Telegram! 🍻🎸\n\n"
                      . "📍 <i>Gura Bicha Sports Bar & Lounge — Mexico, Chamber of Commerce Bldg</i>";

                $this->botService->sendMessage($chatId, $text);
            } catch (\Throwable $e) {
                Log::warning("Could not send Telegram Fan Club welcome to {$chatId}: " . $e->getMessage());
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Fan Club profile saved successfully!',
            'customer' => $customer,
        ]);
    }
}
