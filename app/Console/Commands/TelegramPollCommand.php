<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll {--timeout=20 : Polling timeout in seconds}';

    protected $description = 'Poll Telegram Bot updates via long-polling (ideal for local testing)';

    public function handle(TelegramBotService $botService): int
    {
        if (!$botService->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN is not configured in .env');
            return Command::FAILURE;
        }

        $timeout = (int) $this->option('timeout');
        $this->info("Starting Telegram Bot long-polling (timeout: {$timeout}s)...");
        $this->info("Mini App URL: " . $botService->getWebAppUrl());
        $this->info("Press Ctrl+C to stop.");

        $offset = 0;

        while (true) {
            $res = $botService->getUpdates($offset, $timeout);
            if (!empty($res['ok']) && !empty($res['result'])) {
                foreach ($res['result'] as $update) {
                    $offset = $update['update_id'] + 1;
                    $msg = $update['message'] ?? [];
                    $from = $msg['from']['first_name'] ?? 'User';
                    $text = $msg['text'] ?? '[non-text]';
                    $this->line("[Incoming] {$from}: {$text}");
                    $botService->handleUpdate($update);
                }
            }
            usleep(500000); // 0.5s pause
        }

        return Command::SUCCESS;
    }
}
