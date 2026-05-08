<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AnthropicClient
{
    /**
     * Calls Anthropic Messages API and returns the concatenated output text.
     *
     * @throws \RuntimeException
     */
    public function messages(array $messages, array $options = []): string
    {
        $apiKey = config('services.anthropic.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('ANTHROPIC_API_KEY is not configured.');
        }

        $model = $options['model'] ?? config('services.anthropic.model');
        $maxTokens = (int) ($options['max_tokens'] ?? 1200);
        $temperature = $options['temperature'] ?? 0.3;
        $system = $options['system'] ?? null;

        $payload = [
            'model' => $model,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
            'messages' => $messages,
        ];

        if (is_string($system) && $system !== '') {
            $payload['system'] = $system;
        }

        try {
            $res = Http::baseUrl(rtrim(config('services.anthropic.base_url'), '/'))
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => config('services.anthropic.version'),
                    'content-type' => 'application/json',
                    'accept' => 'application/json',
                ])
                ->timeout(60)
                ->post('/v1/messages', $payload);
        } catch (ConnectionException $e) {
            throw new \RuntimeException('Failed to connect to Anthropic API.');
        }

        if (!$res->successful()) {
            $msg = $res->json('error.message') ?? $res->body();
            throw new \RuntimeException('Anthropic API error: '.Str::limit((string) $msg, 500));
        }

        $content = $res->json('content', []);
        if (!is_array($content)) {
            throw new \RuntimeException('Unexpected Anthropic response format.');
        }

        $texts = [];
        foreach ($content as $part) {
            if (is_array($part) && ($part['type'] ?? null) === 'text' && isset($part['text'])) {
                $texts[] = $part['text'];
            }
        }

        $out = trim(implode("\n", $texts));
        if ($out === '') {
            throw new \RuntimeException('Anthropic API returned empty content.');
        }

        return $out;
    }
}

