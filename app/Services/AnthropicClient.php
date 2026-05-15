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
            // Handle SSL verification
            $verifySsl = $this->getVerifySslConfig();
            
            $res = Http::baseUrl(rtrim(config('services.anthropic.base_url'), '/'))
                ->withOptions([
                    'verify' => $verifySsl,
                ])
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => config('services.anthropic.version'),
                    'content-type' => 'application/json',
                    'accept' => 'application/json',
                ])
                ->timeout(180)
                ->post('/v1/messages', $payload);
        } catch (ConnectionException $e) {
            throw new \RuntimeException(
                'Failed to connect to Anthropic API: ' . $e->getMessage()
            );
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

    /**
     * Determine the proper SSL verification setting for HTTPS requests.
     * 
     * Handles Windows SSL certificate issues by:
     * 1. Using the configured CA bundle if explicitly set
     * 2. Using PHP's configured CA bundle
     * 3. Disabling verification in development mode if necessary
     * 
     * @return bool|string Path to CA bundle or boolean for verification
     */
    private function getVerifySslConfig()
    {
        $configured = config('services.http_verify_cert');
        
        // If explicitly configured, use that setting
        if ($configured === false || $configured === 'false') {
            return false;
        }
        
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }
        
        // Try to get CA bundle from php.ini
        $caBundle = ini_get('openssl.cafile');
        if (!empty($caBundle) && file_exists($caBundle)) {
            return $caBundle;
        }
        
        $caPath = ini_get('openssl.capath');
        if (!empty($caPath) && is_dir($caPath)) {
            return $caPath;
        }
        
        // In development mode, allow disabling SSL verification as a fallback
        if (app()->environment('local', 'development')) {
            return false;
        }
        
        // In production, verify SSL by default
        return true;
    }
}

