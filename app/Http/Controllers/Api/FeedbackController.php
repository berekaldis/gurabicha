<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Feedback;
use App\Models\Setting;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Store new customer feedback or suggestion from the MiniApp or external client.
     */
    public function store(Request $request, TelegramBotService $botService): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|min:3|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'category' => 'nullable|string|max:50',
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'telegram_chat_id' => 'nullable|string|max:50',
            'telegram_username' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:20',
        ]);

        try {
            // Find or associate customer if possible
            $customerId = null;
            if (!empty($validated['telegram_chat_id'])) {
                $cust = Customer::where('telegram_chat_id', (string) $validated['telegram_chat_id'])->first();
                if ($cust) {
                    $customerId = $cust->id;
                }
            } elseif (!empty($validated['telegram_username'])) {
                $cust = Customer::where('telegram_username', (string) $validated['telegram_username'])->first();
                if ($cust) {
                    $customerId = $cust->id;
                }
            } elseif (!empty($validated['phone'])) {
                $cust = Customer::where('phone', (string) $validated['phone'])->first();
                if ($cust) {
                    $customerId = $cust->id;
                }
            }

            $feedback = Feedback::create([
                'customer_id' => $customerId,
                'telegram_chat_id' => $validated['telegram_chat_id'] ?? null,
                'telegram_username' => $validated['telegram_username'] ?? null,
                'name' => $validated['name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'category' => strtoupper($validated['category'] ?? 'GENERAL'),
                'rating' => $validated['rating'] ?? null,
                'message' => $validated['message'],
                'status' => 'NEW',
                'source' => strtoupper($validated['source'] ?? 'MINIAPP'),
            ]);

            // Notify Admin via Telegram if configured
            $adminChatId = Setting::get('telegram_admin_chat_id');
            if ($adminChatId && $botService->isConfigured()) {
                $stars = $feedback->rating ? str_repeat('⭐', $feedback->rating) : 'Not rated';
                $from = $feedback->name ?: ($feedback->telegram_username ? '@' . $feedback->telegram_username : 'Guest Patron');
                $phone = $feedback->phone ? " ({$feedback->phone})" : '';
                
                $alertText = "💬 <b>New Feedback / Suggestion!</b>\n\n"
                    . "<b>From:</b> {$from}{$phone}\n"
                    . "<b>Rating:</b> {$stars}\n"
                    . "<b>Category:</b> " . htmlspecialchars($feedback->category) . "\n"
                    . "<b>Source:</b> " . htmlspecialchars($feedback->source) . "\n\n"
                    . "<b>Message:</b>\n" . htmlspecialchars($feedback->message) . "\n\n"
                    . "📍 <i>Gura Bicha VIP Experience Floor</i>";

                try {
                    $botService->sendMessage($adminChatId, $alertText);
                } catch (\Throwable $te) {
                    Log::warning("Could not dispatch Telegram feedback alert to admin: " . $te->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your valuable feedback! We appreciate you helping us make Gura Bicha even better.',
                'feedback' => $feedback,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Feedback submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to save feedback at the moment. Please try again.',
            ], 500);
        }
    }
}
