<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;

class GroqProvider implements AIProviderInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model
    ) {}

    public function generateContent(string $prompt, array $config = []): string
    {
        $maxTokens = $config['max_tokens'] ?? 4000;
        
        $response = Http::timeout(45)->withToken($this->apiKey)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'max_tokens' => $maxTokens
            ]);
 
        if ($response->successful()) {
            return $response->json('choices.0.message.content', '');
        }
 
        $error = $response->json('error.message', 'Unknown Groq Error');
        throw new \Exception("Groq API Error ({$response->status()}): {$error}");
    }

    public function testConnection(): array
    {
        try {
            $testPrompt = 'Say "API key is working" if you can read this.';
            // Reuse logic from generateContent but wrap it for test response structure
            try {
                $content = $this->generateContent($testPrompt, ['max_tokens' => 100]);
                return [
                    'success' => true,
                    'message' => 'Groq API key is working correctly',
                    'response' => $content
                ];
            } catch (\Exception $e) {
                 return [
                    'success' => false,
                    'message' => 'Groq API key test failed: ' . $e->getMessage()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Groq API key test failed: ' . $e->getMessage()
            ];
        }
    }

    public function getModels(): array
    {
        return [
            'llama-3.1-8b-instant',
            'llama-3.3-70b-versatile',
        ];
    }
}
