<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;

class PerplexityProvider implements AIProviderInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model
    ) {}

    public function generateContent(string $prompt, array $config = []): string
    {
        $response = Http::timeout(45)->withToken($this->apiKey)
            ->post('https://api.perplexity.ai/chat/completions', [
                'model' => $this->model,
                'messages' => [['role' => 'system', 'content' => 'Be precise.'], ['role' => 'user', 'content' => $prompt]],
                'max_tokens' => $config['max_tokens'] ?? 4000
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content', '');
        }

        $error = $response->json('error.message', 'Perplexity API Error');
        throw new \Exception("Perplexity Error ({$response->status()}): {$error}");
    }

    public function testConnection(): array
    {
        $response = Http::withToken($this->apiKey)
            ->post('https://api.perplexity.ai/chat/completions', [
                'model' => $this->model,
                'messages' => [['role' => 'system', 'content' => 'Be precise.'], ['role' => 'user', 'content' => 'Hi']],
                'max_tokens' => 5
            ]);

        if ($response->successful()) {
            return ['success' => true, 'message' => __('admin.ai_test_success', ['provider' => 'Perplexity'])];
        }

        return ['success' => false, 'message' => $response->json('error.message', 'Connection failed')];
    }

    public function getModels(): array
    {
        return [
            'llama-3-sonar-large-32k-online',
            'llama-3-sonar-small-32k-online',
            'mixtral-8x7b-instruct',
        ];
    }
}
