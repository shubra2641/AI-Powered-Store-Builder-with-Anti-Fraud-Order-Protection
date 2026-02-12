<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;

class ClaudeProvider implements AIProviderInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model
    ) {}

    public function generateContent(string $prompt, array $config = []): string
    {
        $response = Http::timeout(45)->withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => $config['max_tokens'] ?? 4000,
            'messages' => [['role' => 'user', 'content' => $prompt]]
        ]);

        if ($response->successful()) {
            // Claude response structure might differ, checking...
            // It returns content block.
            return $response->json('content.0.text', '');
        }

        $error = $response->json('error.message', 'Claude API Error');
        throw new \Exception("Claude Error ({$response->status()}): {$error}");
    }

    public function testConnection(): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => 5,
            'messages' => [['role' => 'user', 'content' => 'Hi']]
        ]);

        if ($response->successful()) {
            return ['success' => true, 'message' => __('admin.ai_test_success', ['provider' => 'Claude'])];
        }

        return ['success' => false, 'message' => $response->json('error.message', 'Connection failed')];
    }

    public function getModels(): array
    {
        return [
            'claude-3-5-sonnet-20240620',
            'claude-3-opus-20240229',
        ];
    }
}
