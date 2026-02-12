<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;

class OpenAIProvider implements AIProviderInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model
    ) {}

    public function generateContent(string $prompt, array $config = []): string
    {
        $response = Http::timeout(45)->withToken($this->apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);
 
        if ($response->successful()) {
            return $response->json('choices.0.message.content', '');
        }
 
        $error = $response->json('error.message', 'OpenAI API Error');
        throw new \Exception("OpenAI Error ({$response->status()}): {$error}");
    }

    public function testConnection(): array
    {
        try {
            $testPrompt = 'Say "API key is working" if you can read this.';
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [['role' => 'user', 'content' => $testPrompt]],
                'max_tokens' => 50
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                return [
                    'success' => true,
                    'message' => 'API key is working correctly',
                    'response' => $content
                ];
            }

            $errorMessage = $response->json('error.message') ?? 'Unknown error';
            
            if ($response->status() === 429 || stripos($errorMessage, 'quota') !== false) {
                return [
                    'success' => true,
                    'message' => 'API key is valid but quota exceeded. The key works correctly.',
                    'quota_exceeded' => true,
                    'status' => $response->status(),
                    'details' => $errorMessage
                ];
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getModels(): array
    {
        return [
            'gpt-4o',
            'gpt-4-turbo',
            'gpt-4',
            'gpt-3.5-turbo',
        ];
    }
}
