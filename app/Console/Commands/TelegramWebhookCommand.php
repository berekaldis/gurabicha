<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class TelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook 
                            {--set : Set the webhook URL}
                            {--get : Retrieve current webhook status}
                            {--delete : Remove the active webhook}
                            {--url= : Specific webhook URL to set}';

    protected $description = 'Manage Telegram Bot Webhook configuration';

    public function handle(TelegramBotService $botService): int
    {
        if (!$botService->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN is not configured in .env');
            return Command::FAILURE;
        }

        if ($this->option('set')) {
            $url = $this->option('url') ?: url('/api/telegram/webhook');
            \App\Models\Setting::set('telegram_webhook_url', $url);
            $this->info("Setting Telegram Webhook to: {$url}...");
            $res = $botService->setWebhook($url);
            if (!empty($res['ok'])) {
                $this->info('Webhook configured successfully!');
            } else {
                $this->error('Failed to set webhook: ' . json_encode($res));
            }
            return Command::SUCCESS;
        }

        if ($this->option('delete')) {
            \App\Models\Setting::set('telegram_webhook_url', '');
            $this->info('Deleting Telegram Webhook...');
            $res = $botService->deleteWebhook();
            if (!empty($res['ok'])) {
                $this->info('Webhook deleted successfully. Bot is now ready for polling!');
            } else {
                $this->error('Failed to delete webhook: ' . json_encode($res));
            }
            return Command::SUCCESS;
        }

        // Default or --get: Show info
        $this->info('Fetching Telegram Webhook info...');
        $info = $botService->getWebhookInfo();
        $this->line(json_encode($info, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
