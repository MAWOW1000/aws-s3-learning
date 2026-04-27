<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Throwable;

class DiscordService
{
    private string $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = config('services.discord.webhook_url', '');
    }

    public function sendErrorNotification(string $description, Throwable $throwable): void
    {
        if (empty($this->webhookUrl)) {
            return;
        }

        $cacheKey = 'discord_notify:' . md5($description . $throwable->getMessage());

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->addMinutes(5));

        try {
            $exceptionClass = get_class($throwable);
            $fullTrace = $throwable->getTraceAsString();

            Http::post($this->webhookUrl, [
                'username' => 'S3 Error Bot',
                'embeds' => [[
                    'title' => '🚨 ' . $description,
                    'color' => 16711680,
                    'fields' => [
                        [
                            'name' => 'Exception',
                            'value' => substr($exceptionClass, 0, 1024),
                            'inline' => false,
                        ],
                        [
                            'name' => 'Message',
                            'value' => substr($throwable->getMessage(), 0, 1024),
                            'inline' => false,
                        ],
                        [
                            'name' => 'Location',
                            'value' => substr($throwable->getFile() . ':' . $throwable->getLine(), 0, 1024),
                            'inline' => false,
                        ],
                        [
                            'name' => 'Stack Trace',
                            'value' => substr("```" . $fullTrace . "```", 0, 1024),
                            'inline' => false,
                        ],
                        [
                            'name' => 'Time',
                            'value' => now()->toIso8601String(),
                            'inline' => false,
                        ],
                    ],
                ]],
            ]);
        } catch (Throwable $e) {
            \Log::warning('Discord notification failed: ' . $e->getMessage());
        }
    }
}
