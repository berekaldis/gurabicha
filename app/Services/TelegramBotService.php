<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Feedback;
use App\Models\Setting;
use App\Models\SportMatch;
use App\Models\TelegramBroadcast;
use App\Models\TelegramSubscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    protected string $token;
    protected string $apiUrl;
    protected string $webAppUrl;
    protected string $botUsername;

    public function __construct()
    {
        $dbToken = Setting::get('telegram_bot_token');
        $this->token = !empty($dbToken) ? (string) $dbToken : (string) config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', ''));
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
        $dbWebAppUrl = Setting::get('telegram_webapp_url');
        if (!empty($dbWebAppUrl) && str_starts_with((string) $dbWebAppUrl, 'https://')) {
            $this->webAppUrl = (string) $dbWebAppUrl;
        } else {
            // If webapp url is not HTTPS, check if webhook URL is an active HTTPS tunnel/domain
            $dbWebhookUrl = Setting::get('telegram_webhook_url');
            if (!empty($dbWebhookUrl) && str_starts_with((string) $dbWebhookUrl, 'https://')) {
                $parsed = parse_url((string) $dbWebhookUrl);
                $this->webAppUrl = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '') . (!empty($parsed['port']) ? ':' . $parsed['port'] : '') . '/';
            } else {
                $this->webAppUrl = !empty($dbWebAppUrl) ? (string) $dbWebAppUrl : (string) config('services.telegram.webapp_url', env('TELEGRAM_WEBAPP_URL', url('/')));
            }
        }
        $dbUsername = Setting::get('telegram_bot_username');
        $this->botUsername = !empty($dbUsername) ? (string) $dbUsername : (string) config('services.telegram.bot_username', env('TELEGRAM_BOT_USERNAME', 'GuraBichaAddisBot'));
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    public function getBotUsername(): string
    {
        return $this->botUsername;
    }

    public function getWebAppUrl(): string
    {
        return $this->webAppUrl;
    }

    /**
     * Send message via Telegram Bot API with optional inline keyboard.
     */
    public function sendMessage(int|string $chatId, string $text, ?array $replyMarkup = null): array
    {
        if (!$this->isConfigured()) {
            Log::info("TelegramBotService (mock): sending to {$chatId}: {$text}");
            return ['ok' => true, 'mock' => true, 'text' => $text];
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            // Telegram API strictly requires HTTPS URLs for web_app buttons.
            // If the URL is HTTP or relative, automatically downgrade to a standard URL button
            // to avoid Telegram throwing a 400 Bad Request error.
            if (isset($replyMarkup['inline_keyboard']) && is_array($replyMarkup['inline_keyboard'])) {
                foreach ($replyMarkup['inline_keyboard'] as &$row) {
                    if (is_array($row)) {
                        foreach ($row as &$btn) {
                            if (isset($btn['web_app']['url']) && !str_starts_with((string) $btn['web_app']['url'], 'https://')) {
                                $btnUrl = (string) $btn['web_app']['url'];
                                unset($btn['web_app']);
                                $btn['url'] = $btnUrl;
                            }
                        }
                    }
                }
                unset($row, $btn);
            }

            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        $response = Http::withoutVerifying()
            ->post("{$this->apiUrl}/sendMessage", $payload);

        $result = $response->json() ?? ['ok' => false, 'error' => 'Invalid response'];

        if (empty($result['ok'])) {
            Log::error("TelegramBotService::sendMessage failed for chat {$chatId}: " . json_encode($result) . " | Payload: " . json_encode($payload));
        }

        return $result;
    }

    /**
     * Broadcast a message to Telegram subscribers matching the audience filter.
     *
     * @return array{total: int, sent: int, failed: int, recipients: array}
     */
    public function broadcast(string $text, ?array $replyMarkup = null, ?string $audience = 'ALL'): array
    {
        // 1. Gather chat IDs from TelegramSubscriber
        $subscribers = TelegramSubscriber::forAudience($audience)->get();
        $subscriberChatIds = $subscribers->pluck('chat_id')->toArray();

        // 2. Gather chat IDs from registered Customers with telegram_chat_id
        $customerQuery = Customer::whereNotNull('telegram_chat_id')
            ->where('telegram_chat_id', '!=', '');

        if (!empty($audience) && $audience !== 'ALL') {
            if ($audience === 'ACOUSTIC') {
                $customerQuery->where('notify_acoustic', true);
            } else {
                $customerQuery->where(function ($q) use ($audience) {
                    $q->where('favorite_club', $audience)
                      ->orWhereNull('favorite_club');
                });
            }
        }

        $customerChatIds = $customerQuery->pluck('telegram_chat_id')->toArray();

        // Unique target chat IDs
        $allChatIds = array_values(array_unique(array_filter(array_merge($subscriberChatIds, $customerChatIds))));

        $sent = 0;
        $failed = 0;
        $recipientIds = [];

        foreach ($allChatIds as $chatId) {
            try {
                $res = $this->sendMessage($chatId, $text, $replyMarkup);
                if (!empty($res['ok'])) {
                    $sent++;
                    $recipientIds[] = (string) $chatId;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                Log::error("Failed broadcasting to Telegram chat {$chatId}: " . $e->getMessage());
                $failed++;
            }
        }

        return [
            'total' => count($allChatIds),
            'sent' => $sent,
            'failed' => $failed,
            'recipients' => $recipientIds,
        ];
    }

    /**
     * Set Telegram Webhook URL.
     */
    public function setWebhook(string $url): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'TELEGRAM_BOT_TOKEN is not configured'];
        }

        $response = Http::withoutVerifying()
            ->post("{$this->apiUrl}/setWebhook", [
                'url' => $url,
                'drop_pending_updates' => true,
            ]);

        return $response->json() ?? ['ok' => false];
    }

    /**
     * Get current Webhook status from Telegram.
     */
    public function getWebhookInfo(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'TELEGRAM_BOT_TOKEN is not configured'];
        }

        $response = Http::withoutVerifying()
            ->get("{$this->apiUrl}/getWebhookInfo");

        return $response->json() ?? ['ok' => false];
    }

    /**
     * Delete active Webhook (required before using getUpdates polling).
     */
    public function deleteWebhook(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'TELEGRAM_BOT_TOKEN is not configured'];
        }

        $response = Http::withoutVerifying()
            ->post("{$this->apiUrl}/deleteWebhook");

        return $response->json() ?? ['ok' => false];
    }

    /**
     * Long-polling getUpdates for local dev or CLI polling command.
     */
    public function getUpdates(int $offset = 0, int $timeout = 20): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'TELEGRAM_BOT_TOKEN is not configured'];
        }

        $response = Http::withoutVerifying()
            ->timeout($timeout + 5)
            ->get("{$this->apiUrl}/getUpdates", [
                'offset' => $offset,
                'timeout' => $timeout,
            ]);

        return $response->json() ?? ['ok' => false];
    }

    /**
     * Handle incoming Telegram Update (Webhook or Poll).
     */
    public function handleUpdate(array $update): void
    {
        // Handle callback queries (inline button clicks in Telegram chat)
        if (isset($update['callback_query'])) {
            $cq = $update['callback_query'];
            $cqChatId = $cq['message']['chat']['id'] ?? null;
            $data = $cq['data'] ?? '';
            $from = $cq['from'] ?? [];
            if ($cqChatId) {
                if ($data === 'cmd_fixtures') {
                    $this->sendFixturesSummary($cqChatId);
                } elseif ($data === 'cmd_menu') {
                    $this->sendMenuSummary($cqChatId);
                } elseif ($data === 'cmd_venue') {
                    $this->sendVenueInfo($cqChatId);
                } elseif ($data === 'cmd_feedback') {
                    $this->handleFeedbackCommand($cqChatId, '/feedback', $from['first_name'] ?? null, $from['last_name'] ?? null, $from['username'] ?? null);
                } elseif (str_starts_with($data, 'fb_rate_')) {
                    $rating = (int) substr($data, 8);
                    $rating = max(1, min(5, $rating));
                    $senderName = trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? '')) ?: 'Patron';
                    $username = $from['username'] ?? null;

                    Cache::put("tg_fb_rating_{$cqChatId}", $rating, 900);

                    // Persist initial feedback record immediately with the selected rating
                    try {
                        $fb = Feedback::create([
                            'telegram_chat_id' => (string) $cqChatId,
                            'telegram_username' => $username,
                            'name' => $senderName,
                            'category' => 'GENERAL',
                            'rating' => $rating,
                            'message' => "Rated {$rating} star" . ($rating > 1 ? 's' : '') . " via Telegram Bot",
                            'status' => 'NEW',
                            'source' => 'BOT',
                        ]);
                        Cache::put("tg_fb_last_id_{$cqChatId}", $fb->id, 900);
                    } catch (\Throwable $e) {
                        Log::error("Failed to create rating feedback: " . $e->getMessage());
                    }

                    $stars = str_repeat('⭐', $rating);
                    $msg = "{$stars} <b>Rated {$rating}/5 Stars! Thank You!</b>\n\n"
                         . "Would you like to leave a suggestion or comment? <b>Type and send your message below</b>, or tap a category to specify:";

                    $categoryKeyboard = [
                        'inline_keyboard' => [
                            [
                                ['text' => '🍔 Food', 'callback_data' => 'fb_cat_FOOD'],
                                ['text' => '🍸 Bar & Drinks', 'callback_data' => 'fb_cat_DRINKS'],
                                ['text' => '🎸 Acoustic Live', 'callback_data' => 'fb_cat_ACOUSTIC'],
                            ],
                            [
                                ['text' => '⚽ Big Screen', 'callback_data' => 'fb_cat_SPORTS'],
                                ['text' => '🤵 Hospitality', 'callback_data' => 'fb_cat_SERVICE'],
                                ['text' => '💡 General Idea', 'callback_data' => 'fb_cat_SUGGESTION'],
                            ],
                            [
                                [
                                    'text' => '🚀 Open Mini App Feedback Form',
                                    'web_app' => ['url' => rtrim($this->webAppUrl, '/') . '#feedback'],
                                ],
                            ],
                        ],
                    ];

                    $this->sendMessage($cqChatId, $msg, $categoryKeyboard);

                } elseif (str_starts_with($data, 'fb_cat_')) {
                    $category = substr($data, 7);
                    $fbId = Cache::get("tg_fb_last_id_{$cqChatId}");
                    if ($fbId) {
                        Feedback::where('id', $fbId)->update(['category' => $category]);
                    }
                    Cache::put("tg_fb_cat_{$cqChatId}", $category, 900);

                    $catNames = [
                        'FOOD' => '🍔 Food & Kitchen',
                        'DRINKS' => '🍸 Bar & Cocktails',
                        'ACOUSTIC' => '🎸 Saturday Acoustic Night',
                        'SPORTS' => '⚽ Football Match Screening',
                        'SERVICE' => '🤵 Table Service & Hospitality',
                        'SUGGESTION' => '💡 General Idea / Suggestion',
                    ];
                    $label = $catNames[$category] ?? $category;

                    $this->sendMessage($cqChatId, "📝 <b>Category: {$label}</b>\n\nPlease reply with your suggestions or feedback below (or type <code>/suggest [your message]</code>):");
                }
            }
            return;
        }

        $message = $update['message'] ?? null;
        if (!$message) {
            return;
        }

        $chatId = $message['chat']['id'] ?? null;
        $text = trim($message['text'] ?? '');
        $firstName = $message['from']['first_name'] ?? null;
        $lastName = $message['from']['last_name'] ?? null;
        $username = $message['from']['username'] ?? null;

        if (!$chatId) {
            return;
        }

        // Automatically register / update Telegram subscriber
        try {
            TelegramSubscriber::updateOrCreate(
                ['chat_id' => (string) $chatId],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'is_active' => true,
                    'last_interaction_at' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning("Could not register Telegram subscriber {$chatId}: " . $e->getMessage());
        }

        $senderName = $firstName ?? 'Patron';

        // Check if this is a follow-up text comment to an active feedback rating flow
        if (!str_starts_with($text, '/')) {
            $pendingFbId = Cache::pull("tg_fb_last_id_{$chatId}");
            if ($pendingFbId) {
                try {
                    $fb = Feedback::find($pendingFbId);
                    if ($fb) {
                        $fb->update([
                            'message' => $text,
                            'name' => trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: $fb->name,
                            'telegram_username' => $username ?: $fb->telegram_username,
                        ]);

                        // Notify Admin if telegram_admin_chat_id is set
                        $adminChatId = Setting::get('telegram_admin_chat_id');
                        if ($adminChatId && $adminChatId != $chatId) {
                            $userHandle = $username ? "@{$username}" : $senderName;
                            $stars = str_repeat('⭐', (int) ($fb->rating ?? 5));
                            $adminMsg = "💬 <b>New Telegram Bot Feedback!</b>\n\n"
                                . "<b>From:</b> {$userHandle} (ID: {$chatId})\n"
                                . "<b>Rating:</b> {$stars} ({$fb->rating}/5)\n"
                                . "<b>Category:</b> {$fb->category}\n\n"
                                . "<b>Message:</b>\n" . htmlspecialchars($text) . "\n\n"
                                . "📍 <i>Gura Bicha Mexico</i>";
                            $this->sendMessage($adminChatId, $adminMsg);
                        }

                        $stars = str_repeat('⭐', (int) ($fb->rating ?? 5));
                        $thankMsg = "🙏 <b>Thank You, {$senderName}!</b>\n\n"
                                  . "Your {$stars} rating and feedback have been shared directly with Gura Bicha management.\n\n"
                                  . "<i>We appreciate you helping us make Addis Ababa's premier sports & acoustic lounge even better!</i> ✨";

                        $this->sendMessage($chatId, $thankMsg, [
                            'inline_keyboard' => [
                                [
                                    ['text' => '🚀 Launch Mini App', 'web_app' => ['url' => $this->webAppUrl]],
                                ],
                            ],
                        ]);
                        return;
                    }
                } catch (\Throwable $e) {
                    Log::error("Failed to append message to feedback {$pendingFbId}: " . $e->getMessage());
                }
            }
        }

        $command = strtolower(explode(' ', $text)[0]);

        switch ($command) {
            case '/start':
                $this->sendStartWelcome($chatId, $senderName);
                break;

            case '/fixtures':
            case '/matches':
                $this->sendFixturesSummary($chatId);
                break;

            case '/menu':
                $this->sendMenuSummary($chatId);
                break;

            case '/reserve':
            case '/book':
                $this->sendReservationInfo($chatId);
                break;

            case '/live':
                $this->sendLiveScores($chatId);
                break;

            case '/venue':
            case '/location':
            case '/map':
                $this->sendVenueInfo($chatId);
                break;

            case '/feedback':
            case '/suggest':
            case '/suggestion':
                $this->handleFeedbackCommand($chatId, $text, $firstName, $lastName, $username);
                break;

            case '/help':
            default:
                $this->sendHelp($chatId);
                break;
        }
    }

    /**
     * Handle incoming feedback or suggestion command via Telegram Bot.
     */
    public function handleFeedbackCommand(int|string $chatId, string $text, ?string $firstName, ?string $lastName, ?string $username): void
    {
        $parts = preg_split('/\s+/', $text, 2);
        $body = isset($parts[1]) ? trim($parts[1]) : '';
        $senderName = trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: 'Patron';

        if (empty($body)) {
            $msg = "⭐️ <b>Gura Bicha Feedback & Suggestions</b>\n\n"
                 . "How would you rate your experience at Gura Bicha Sports Bar & Acoustic Lounge?\n\n"
                 . "<b>Tap a star rating below to rate us:</b>";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '⭐ 1', 'callback_data' => 'fb_rate_1'],
                        ['text' => '⭐⭐ 2', 'callback_data' => 'fb_rate_2'],
                        ['text' => '⭐⭐⭐ 3', 'callback_data' => 'fb_rate_3'],
                        ['text' => '⭐⭐⭐⭐ 4', 'callback_data' => 'fb_rate_4'],
                        ['text' => '⭐⭐⭐⭐⭐ 5', 'callback_data' => 'fb_rate_5'],
                    ],
                    [
                        ['text' => '🍔 Food', 'callback_data' => 'fb_cat_FOOD'],
                        ['text' => '🍸 Bar', 'callback_data' => 'fb_cat_DRINKS'],
                        ['text' => '🎸 Acoustic', 'callback_data' => 'fb_cat_ACOUSTIC'],
                    ],
                    [
                        ['text' => '⚽ Big Screen', 'callback_data' => 'fb_cat_SPORTS'],
                        ['text' => '🤵 Service', 'callback_data' => 'fb_cat_SERVICE'],
                        ['text' => '💡 Suggestion', 'callback_data' => 'fb_cat_SUGGESTION'],
                    ],
                    [
                        [
                            'text' => '🚀 Open Mini App Feedback Form',
                            'web_app' => ['url' => rtrim($this->webAppUrl, '/') . '#feedback'],
                        ],
                    ],
                ],
            ];

            $this->sendMessage($chatId, $msg, $keyboard);
            return;
        }

        $cachedRating = Cache::pull("tg_fb_rating_{$chatId}");
        $rating = $cachedRating ? (int) $cachedRating : 5;
        $category = Cache::pull("tg_fb_cat_{$chatId}") ?: (str_starts_with(strtolower($text), '/suggest') ? 'SUGGESTION' : 'GENERAL');

        try {
            $fb = Feedback::create([
                'telegram_chat_id' => (string) $chatId,
                'telegram_username' => $username,
                'name' => $senderName,
                'category' => $category,
                'rating' => $rating,
                'message' => $body,
                'status' => 'NEW',
                'source' => 'BOT',
            ]);

            // Notify Admin if telegram_admin_chat_id is set
            $adminChatId = Setting::get('telegram_admin_chat_id');
            if ($adminChatId && $adminChatId != $chatId) {
                $userHandle = $username ? "@{$username}" : $senderName;
                $stars = str_repeat('⭐', $rating);
                $adminMsg = "💬 <b>New Telegram Bot Feedback!</b>\n\n"
                    . "<b>From:</b> {$userHandle} (ID: {$chatId})\n"
                    . "<b>Rating:</b> {$stars} ({$rating}/5)\n"
                    . "<b>Category:</b> {$fb->category}\n\n"
                    . "<b>Message:</b>\n" . htmlspecialchars($body) . "\n\n"
                    . "📍 <i>Gura Bicha Mexico</i>";
                $this->sendMessage($adminChatId, $adminMsg);
            }

            $stars = str_repeat('⭐', $rating);
            $thankMsg = "🙏 <b>Thank You, {$senderName}!</b>\n\n"
                      . "Your {$stars} rating and feedback have been shared directly with Gura Bicha management.\n\n"
                      . "<i>We appreciate you helping us make Addis Ababa's premier sports & acoustic lounge even better!</i> ✨";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        [
                            'text' => '🚀 Launch Mini App',
                            'web_app' => ['url' => $this->webAppUrl],
                        ],
                    ],
                ],
            ];

            $this->sendMessage($chatId, $thankMsg, $keyboard);

        } catch (\Throwable $e) {
            Log::error("Failed to save feedback from Telegram {$chatId}: " . $e->getMessage());
            $this->sendMessage($chatId, "⚠️ Sorry, we could not save your feedback right now. Please try again or use the Mini App.");
        }
    }

    protected function sendStartWelcome(int|string $chatId, string $name): void
    {
        $text = "⚽🎸 <b>Welcome to Gura Bicha!</b>\n\n"
              . "Selam <b>{$name}</b>! 👋 Your premier Addis Ababa destination for <b>Big Screen Premier League matchdays</b> and soulful <b>Live Acoustic nights</b> at Mexico, Gura Bicha.\n\n"
              . "Tap below to get started:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '🚀 Launch Mini App',
                        'web_app' => ['url' => $this->webAppUrl],
                    ],
                ],
                [
                    [
                        'text' => '⚽ Live Fixtures',
                        'callback_data' => 'cmd_fixtures',
                    ],
                    [
                        'text' => '🍽️ Food & Bar Menu',
                        'callback_data' => 'cmd_menu',
                    ],
                ],
                [
                    [
                        'text' => '🎸 Book Acoustic Table',
                        'web_app' => ['url' => rtrim($this->webAppUrl, '/') . '#reserve'],
                    ],
                    [
                        'text' => '📍 Location & Hours',
                        'callback_data' => 'cmd_venue',
                    ],
                ],
                [
                    [
                        'text' => '💬 Feedback & Suggestion',
                        'callback_data' => 'cmd_feedback',
                    ],
                ],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    protected function sendFixturesSummary(int|string $chatId): void
    {
        $now = Carbon::now('UTC');
        $live = SportMatch::where('status', 'LIVE')->orWhere('live', true)->get();
        $upcoming = SportMatch::whereBetween('kickoff', [$now->copy()->subHours(2), $now->copy()->addDays(7)])
            ->where('status', '!=', 'LIVE')
            ->orderBy('kickoff', 'asc')
            ->take(4)
            ->get();

        $lines = ["⚽ <b>Match Fixtures & Screenings</b>\n"];

        if ($live->isNotEmpty()) {
            $lines[] = "🔴 <b>LIVE NOW:</b>";
            foreach ($live as $m) {
                $lines[] = "• {$m->homeTeam} <b>{$m->homeScore} - {$m->awayScore}</b> {$m->awayTeam} ({$m->competition})";
            }
            $lines[] = "";
        }

        if ($upcoming->isNotEmpty()) {
            $lines[] = "⏳ <b>Upcoming Screenings:</b>";
            foreach ($upcoming as $m) {
                $time = $m->kickoff ? $m->kickoff->timezone('Africa/Addis_Ababa')->format('D g:i A') : 'TBD';
                $lines[] = "• <b>{$m->homeTeam} vs {$m->awayTeam}</b> — {$time} EAT";
            }
        } else {
            $lines[] = "No upcoming matches scheduled today.";
        }

        $lines[] = "\n🍺 <i>Cold drafts & authentic cuisine ready for matchday!</i>";

        $text = implode("\n", $lines);

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '⚽ Open Live Match Center',
                        'web_app' => ['url' => rtrim($this->webAppUrl, '/') . '#fixtures'],
                    ],
                ],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    protected function sendMenuSummary(int|string $chatId): void
    {
        $text = "🍽️ <b>Gura Bicha Sport Bar Menu</b>\n"
              . "<i>Good Food · Great Drinks · Best Vibes</i>\n\n"
              . "🍔 <b>Food:</b>\n"
              . "• <b>Buffet</b> — 1,550 ETB\n"
              . "• <b>Steak Sandwich</b> — 1,200 ETB\n"
              . "• <b>Chicken Sandwich</b> — 1,200 ETB\n"
              . "• <b>Chicken Wings</b> — 1,200 ETB\n"
              . "• <b>Cheese Burger</b> — 1,200 ETB\n"
              . "• <b>Lasagna</b> — 1,400 ETB\n\n"
              . "🍕 <b>Pizza:</b>\n"
              . "• <b>Margherita Pizza</b> — 1,500 ETB\n"
              . "• <b>Tuna Pizza</b> — 1,500 ETB\n\n"
              . "🍺 <b>Beer & Drinks:</b>\n"
              . "• <b>Heineken</b> — 280 ETB\n"
              . "• <b>Arada</b> — 280 ETB\n"
              . "• <b>Heineken Draft</b> — 190 ETB\n"
              . "• <b>Local Beer</b> — 220 ETB\n"
              . "• <b>Mojito</b> — 600 ETB\n\n"
              . "Tap below to view full menu with photos & order:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '📖 Browse Full Menu',
                        'web_app' => ['url' => rtrim($this->webAppUrl, '/') . '#menu'],
                    ],
                ],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    protected function sendReservationInfo(int|string $chatId): void
    {
        $phone = Setting::get('venue_phone', '+251905888724');

        $text = "🎸 <b>Live Acoustic Table Booking</b>\n\n"
              . "✨ <b>Vibe:</b> Live acoustic band & candlelit table seating\n"
              . "📍 <b>Location:</b> Mexico, Chamber of Commerce Bldg\n"
              . "⏰ <b>Sessions:</b> 7:00 PM – 11:30 PM\n"
              . "📞 <b>Phone:</b> {$phone}\n\n"
              . "Book your front-row table below:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '🎟️ Book Acoustic Table',
                        'web_app' => ['url' => rtrim($this->webAppUrl, '/') . '#reserve'],
                    ],
                ],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    protected function sendLiveScores(int|string $chatId): void
    {
        $live = SportMatch::where('status', 'LIVE')->orWhere('live', true)->get();

        if ($live->isNotEmpty()) {
            $lines = ["🔴 <b>LIVE ON 4K ARENA SCREENS:</b>\n"];
            foreach ($live as $m) {
                $lines[] = "⚽ <b>{$m->homeTeam} {$m->homeScore} - {$m->awayScore} {$m->awayTeam}</b> ({$m->competition})";
            }
            $text = implode("\n", $lines);
        } else {
            $next = SportMatch::where('kickoff', '>=', now())
                ->orderBy('kickoff', 'asc')
                ->first();

            $nextText = $next
                ? "Next kickoff: <b>{$next->homeTeam} vs {$next->awayTeam}</b> (" . $next->kickoff->timezone('Africa/Addis_Ababa')->format('D g:i A') . " EAT)"
                : "No live matches right now.";

            $text = "⚽ <b>Gura Bicha 4K Stadium Screenings</b>\n\n"
                  . "{$nextText}\n\n"
                  . "<i>Walk in anytime for cold drafts & big screens!</i>";
        }

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '⚽ Open Live Match Center',
                        'web_app' => ['url' => rtrim($this->webAppUrl, '/') . '#fixtures'],
                    ],
                ],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    protected function sendVenueInfo(int|string $chatId): void
    {
        $mapUrl = Setting::get('google_maps_url', 'https://maps.app.goo.gl/CK9ZByHcwZeUaHDK6') ?: 'https://maps.app.goo.gl/CK9ZByHcwZeUaHDK6';
        $phone = Setting::get('venue_phone', '+251905888724');

        $text = "📍 <b>Gura Bicha Sports Bar & Lounge</b>\n\n"
              . "🏢 <b>Location:</b> Mexico, Gura Bicha, Addis Ababa\n"
              . "⏰ <b>Hours:</b> Daily 12:00 PM – 2:00 AM\n"
              . "🎸 <b>Live Acoustic:</b> 7:00 PM – 11:30 PM\n"
              . "📞 <b>Phone:</b> {$phone}";

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '🗺️ Open in Google Maps',
                        'url' => $mapUrl,
                    ],
                ],
                [
                    [
                        'text' => '🚀 Launch Mini App',
                        'web_app' => ['url' => $this->webAppUrl],
                    ],
                ],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    protected function sendHelp(int|string $chatId): void
    {
        $text = "🤖 <b>Gura Bicha Bot Commands</b>\n\n"
              . "• /fixtures — Current matches & kickoffs\n"
              . "• /menu — Food & drink prices\n"
              . "• /reserve — Acoustic table booking\n"
              . "• /live — Real-time big screen scores\n"
              . "• /feedback [msg] — Send suggestions & feedback to management\n"
              . "• /suggest [idea] — Suggest drinks, food, songs or matches\n"
              . "• /venue — Location, map & hours\n\n"
              . "📍 <i>Mexico, Gura Bicha</i>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '🚀 Launch Mini App',
                        'web_app' => ['url' => $this->webAppUrl],
                    ],
                ],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }
}
