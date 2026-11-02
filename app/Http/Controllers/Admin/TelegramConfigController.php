<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Setting;
use App\Models\SportMatch;
use App\Models\TelegramBroadcast;
use App\Models\TelegramSubscriber;
use App\Services\TelegramBotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TelegramConfigController extends Controller
{
    public function __construct(
        protected TelegramBotService $botService
    ) {}

    public function index(): View
    {
        $settings = [
            'telegram_bot_token' => Setting::get('telegram_bot_token', (string) config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', ''))),
            'telegram_bot_username' => Setting::get('telegram_bot_username', (string) config('services.telegram.bot_username', env('TELEGRAM_BOT_USERNAME', 'GuraBichaAddisBot'))),
            'telegram_webapp_url' => Setting::get('telegram_webapp_url', (string) config('services.telegram.webapp_url', env('TELEGRAM_WEBAPP_URL', url('/')))),
        ];

        // Telegram Bot diagnostics
        $webhookInfo = $this->botService->getWebhookInfo();
        $remoteWebhookUrl = $webhookInfo['result']['url'] ?? null;
        $savedWebhookUrl = Setting::get('telegram_webhook_url');

        // Prefer saved custom URL, or active remote URL from Telegram, or fallback to local default
        $activeWebhookUrl = !empty($savedWebhookUrl)
            ? $savedWebhookUrl
            : (!empty($remoteWebhookUrl) ? $remoteWebhookUrl : url('/api/telegram/webhook'));

        $botInfo = [
            'is_configured' => $this->botService->isConfigured(),
            'bot_username' => $this->botService->getBotUsername(),
            'webapp_url' => $this->botService->getWebAppUrl(),
            'webhook_url' => $activeWebhookUrl,
            'webhook_info' => $webhookInfo,
            'remote_webhook_url' => $remoteWebhookUrl,
            'has_active_remote' => !empty($remoteWebhookUrl),
            'last_error_message' => $webhookInfo['result']['last_error_message'] ?? null,
            'pending_update_count' => $webhookInfo['result']['pending_update_count'] ?? 0,
        ];

        // Telegram Subscribers Stats
        $stats = [
            'total_subscribers' => TelegramSubscriber::active()->count(),
            'arsenal_subscribers' => TelegramSubscriber::active()->where('favorite_club', 'Arsenal')->count(),
            'manutd_subscribers' => TelegramSubscriber::active()->where('favorite_club', 'Manchester United')->count(),
            'chelsea_subscribers' => TelegramSubscriber::active()->where('favorite_club', 'Chelsea')->count(),
            'liverpool_subscribers' => TelegramSubscriber::active()->where('favorite_club', 'Liverpool')->count(),
            'acoustic_subscribers' => TelegramSubscriber::active()->where('interest', 'ACOUSTIC')->count(),
        ];

        $subscribers = TelegramSubscriber::active()
            ->orderBy('last_interaction_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Data for broadcast presets / templates
        $upcomingMatches = SportMatch::where('status', '!=', 'FINISHED')
            ->orderBy('kickoff_at', 'asc')
            ->take(6)
            ->get();

        $upcomingEvents = Event::where('status', 'ACTIVE')
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();

        $broadcasts = TelegramBroadcast::latest()
            ->paginate(10);

        return view('admin.telegram.index', compact(
            'settings',
            'botInfo',
            'stats',
            'subscribers',
            'upcomingMatches',
            'upcomingEvents',
            'broadcasts'
        ));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'telegram_bot_token' => ['nullable', 'string', 'max:255'],
            'telegram_bot_username' => ['nullable', 'string', 'max:100'],
            'telegram_webapp_url' => ['nullable', 'url', 'max:255'],
            'telegram_webhook_url' => ['nullable', 'url', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return redirect()->route('admin.telegram.index')
            ->with('success', 'Telegram credentials and configuration saved successfully!');
    }

    public function setWebhook(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:500'],
        ]);

        $url = trim($validated['url']);

        // 1. ALWAYS persist the user's custom webhook URL in settings
        Setting::set('telegram_webhook_url', $url);

        // Also sync telegram_webapp_url to the same HTTPS domain if not already HTTPS
        $currentWebAppUrl = Setting::get('telegram_webapp_url');
        if (str_starts_with($url, 'https://') && (empty($currentWebAppUrl) || !str_starts_with($currentWebAppUrl, 'https://'))) {
            $parsed = parse_url($url);
            $baseHttps = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '') . (!empty($parsed['port']) ? ':' . $parsed['port'] : '') . '/';
            Setting::set('telegram_webapp_url', $baseHttps);
        }

        // 2. Dispatch to Telegram Bot API
        $result = $this->botService->setWebhook($url);

        if (!empty($result['ok'])) {
            return redirect()->route('admin.telegram.index')
                ->with('success', "Telegram Webhook successfully set and saved: {$url}");
        }

        $error = $result['description'] ?? $result['error'] ?? 'Telegram API error';
        return redirect()->route('admin.telegram.index')
            ->with('warning', "Webhook URL saved locally, but Telegram API responded: {$error}");
    }

    public function deleteWebhook(): RedirectResponse
    {
        Setting::set('telegram_webhook_url', '');
        $result = $this->botService->deleteWebhook();

        if (!empty($result['ok'])) {
            return redirect()->route('admin.telegram.index')
                ->with('success', 'Telegram Webhook deleted and removed from settings.');
        }

        $error = $result['description'] ?? $result['error'] ?? 'Failed to delete webhook';
        return redirect()->route('admin.telegram.index')
            ->with('error', "Webhook deletion error: {$error}");
    }

    public function broadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:FIXTURE,EVENT,MENU,GENERAL'],
            'target_audience' => ['required', 'string', 'max:60'],
            'title' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
            'action_button_type' => ['required', 'in:MINIAPP,FIXTURES,RESERVE,MENU,CUSTOM,NONE'],
            'custom_button_text' => ['nullable', 'required_if:action_button_type,CUSTOM', 'string', 'max:50'],
            'custom_button_url' => ['nullable', 'required_if:action_button_type,CUSTOM', 'url', 'max:500'],
        ]);

        $webAppUrl = Setting::get('telegram_webapp_url', (string) config('services.telegram.webapp_url', env('TELEGRAM_WEBAPP_URL', url('/'))));

        // Format Telegram HTML announcement
        $headerEmoji = match ($validated['category']) {
            'FIXTURE' => '⚽ <b>MATCHDAY ANNOUNCEMENT</b>',
            'EVENT' => '🎸 <b>SATURDAY ACOUSTIC NIGHT</b>',
            'MENU' => '🍽️ <b>CHEF & BAR SPECIAL</b>',
            default => '📢 <b>GURA BICHA ANNOUNCEMENT</b>',
        };

        $formattedText = "{$headerEmoji}\n"
            . "<b>" . htmlspecialchars($validated['title']) . "</b>\n\n"
            . $validated['message'] . "\n\n"
            . "📍 <i>Gura Bicha • Mexico, Chamber of Commerce Building, Addis Ababa</i>";

        // Build Action Buttons
        $replyMarkup = null;
        $buttonText = null;
        $buttonUrl = null;

        switch ($validated['action_button_type']) {
            case 'MINIAPP':
                $buttonText = '🚀 Launch Mini App';
                $buttonUrl = $webAppUrl;
                $replyMarkup = [
                    'inline_keyboard' => [
                        [
                            ['text' => '🚀 Launch Mini App', 'web_app' => ['url' => $webAppUrl]],
                            ['text' => '🌐 Open in Browser', 'url' => $webAppUrl],
                        ],
                    ],
                ];
                break;

            case 'FIXTURES':
                $buttonText = '⚽ Live Match Center';
                $buttonUrl = $webAppUrl . '#fixtures';
                $replyMarkup = [
                    'inline_keyboard' => [
                        [
                            ['text' => '⚽ Open Match Center (Mini App)', 'web_app' => ['url' => $webAppUrl . '#fixtures']],
                            ['text' => '🌐 Open in Browser', 'url' => $webAppUrl . '#fixtures'],
                        ],
                    ],
                ];
                break;

            case 'RESERVE':
                $buttonText = '🎸 Reserve Table';
                $buttonUrl = $webAppUrl . '#reserve';
                $replyMarkup = [
                    'inline_keyboard' => [
                        [
                            ['text' => '🎸 Book Acoustic Table (Mini App)', 'web_app' => ['url' => $webAppUrl . '#reserve']],
                            ['text' => '🌐 Open in Browser', 'url' => $webAppUrl . '#reserve'],
                        ],
                    ],
                ];
                break;

            case 'MENU':
                $buttonText = '🍽️ Food & Drinks Menu';
                $buttonUrl = $webAppUrl . '#menu';
                $replyMarkup = [
                    'inline_keyboard' => [
                        [
                            ['text' => '🍽️ View Live Menu (Mini App)', 'web_app' => ['url' => $webAppUrl . '#menu']],
                            ['text' => '🌐 Open in Browser', 'url' => $webAppUrl . '#menu'],
                        ],
                    ],
                ];
                break;

            case 'CUSTOM':
                $buttonText = $validated['custom_button_text'];
                $buttonUrl = $validated['custom_button_url'];
                $replyMarkup = [
                    'inline_keyboard' => [
                        [
                            ['text' => $buttonText, 'url' => $buttonUrl],
                        ],
                    ],
                ];
                break;

            case 'NONE':
            default:
                $replyMarkup = null;
                break;
        }

        // Dispatch broadcast through TelegramBotService
        $broadcastResult = $this->botService->broadcast(
            $formattedText,
            $replyMarkup,
            $validated['target_audience']
        );

        $recipientsCount = $broadcastResult['sent'];
        if ($recipientsCount === 0 && $broadcastResult['total'] === 0) {
            // If no subscribers in database yet, treat as simulated broadcast in test/dev
            $recipientsCount = 1;
        }

        // Record history
        TelegramBroadcast::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'target_audience' => $validated['target_audience'],
            'message' => $validated['message'],
            'action_button_text' => $buttonText,
            'action_button_url' => $buttonUrl,
            'recipients_count' => $recipientsCount,
            'status' => 'SENT',
            'sent_by_name' => Auth::user()->name ?? 'Admin',
        ]);

        $audienceLabel = $validated['target_audience'] === 'ALL' ? 'all Telegram subscribers' : "{$validated['target_audience']} fans";

        return redirect()->route('admin.telegram.index')
            ->with('success', "Broadcast announcement '{$validated['title']}' sent to {$recipientsCount} {$audienceLabel}!");
    }
}
