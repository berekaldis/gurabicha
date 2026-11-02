<?php

namespace App\Services;

class TelegramAuthService
{
    /**
     * Parse and optionally verify the Telegram WebApp initData string.
     * Returns an array with user details or null on verification failure.
     */
    public function verifyInitData(?string $initData): ?array
    {
        if (empty($initData)) {
            return null;
        }

        parse_str($initData, $params);

        if (empty($params['user'])) {
            return null;
        }

        $user = json_decode($params['user'], true);
        if (!$user || empty($user['id'])) {
            return null;
        }

        $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        if (empty($botToken)) {
            // Local dev mode without token configured
            return [
                'id' => (string) $user['id'],
                'first_name' => $user['first_name'] ?? null,
                'last_name' => $user['last_name'] ?? null,
                'username' => $user['username'] ?? null,
                'photo_url' => $user['photo_url'] ?? null,
                'verified' => false,
            ];
        }

        $hash = $params['hash'] ?? null;
        if (empty($hash)) {
            return null;
        }

        $dataCheckArr = [];
        foreach ($params as $k => $v) {
            if ($k !== 'hash' && $k !== 'signature') {
                $dataCheckArr[] = "{$k}={$v}";
            }
        }
        sort($dataCheckArr);
        $dataCheckString = implode("\n", $dataCheckArr);

        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $computedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($computedHash, $hash)) {
            return null;
        }

        return [
            'id' => (string) $user['id'],
            'first_name' => $user['first_name'] ?? null,
            'last_name' => $user['last_name'] ?? null,
            'username' => $user['username'] ?? null,
            'photo_url' => $user['photo_url'] ?? null,
            'verified' => true,
        ];
    }
}
