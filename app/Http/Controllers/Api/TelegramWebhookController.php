<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected TelegramBotService $botService
    ) {}

    /**
     * Handle incoming updates from Telegram Webhook.
     */
    public function webhook(Request $request): JsonResponse
    {
        $update = $request->all();

        if (!empty($update)) {
            $this->botService->handleUpdate($update);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Check Telegram Bot & Mini App status and configuration.
     */
    public function info(): JsonResponse
    {
        return response()->json([
            'status' => 'online',
            'configured' => $this->botService->isConfigured(),
            'bot_username' => $this->botService->getBotUsername(),
            'webapp_url' => $this->botService->getWebAppUrl(),
            'webhook_url' => url('/api/telegram/webhook'),
            'webhook_info' => $this->botService->getWebhookInfo(),
        ]);
    }

    /**
     * Set Webhook programmatically.
     */
    public function setWebhook(Request $request): JsonResponse
    {
        $url = $request->input('url', url('/api/telegram/webhook'));
        $result = $this->botService->setWebhook($url);

        return response()->json($result);
    }
}
