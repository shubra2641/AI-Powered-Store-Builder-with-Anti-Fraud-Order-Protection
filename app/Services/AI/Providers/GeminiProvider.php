<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AIProviderInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model
    ) {}

    public function generateContent(string $prompt, array $config = []): string
    {
        // Handle varying Gemini URL endpoints based on model version if needed
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
        
        $response = Http::timeout(45)->post("{$url}?key={$this->apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);
 
        if ($response->successful()) {
            return $response->json('candidates.0.content.parts.0.text', '');
        }
 
        $error = $response->json('error.message', 'Gemini API Error');
        throw new \Exception("Gemini Error ({$response->status()}): {$error}");
    }

    public function testConnection(): array
    {
        try {
            $testPrompt = 'Say "API key is working" if you can read this.';
            // Determine URL based on model to ensure compatibility (logic copied from original AIService)
            $url = match($this->model) {
                'gemini-2.0-flash' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
                'gemini-2.0-flash-exp' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent',
                'gemini-2.5-flash' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
                default => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent'
            };

            // Override model in URL if it was specific in the match, otherwise use construct model?
            // Actually, the original code had hardcoded defaults for testing. 
            // Let's rely on the constructor model but fall back to a known working endpoint pattern if the model is weird?
            // For simplicity and correctness, let's use the standard endpoint with the injected model unless it's a known special case.
            // But to match original logic exactly:
            if (!in_array($this->model, ['gemini-2.0-flash', 'gemini-2.0-flash-exp', 'gemini-2.5-flash'])) {
                 // The original code fell back to gemini-1.5-flash for testing... 
                 // It's safer to try to use the selected model.
                 $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
            } else {
                 $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
            }

            $response = Http::timeout(30)->post($url . '?key=' . $this->apiKey, [
                'contents' => [
                    ['parts' => [['text' => $testPrompt]]]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 100
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Success';
                return [
                    'success' => true,
                    'message' => 'API key is working correctly',
                    'response' => $content
                ];
            }

            if ($response->status() === 429) {
                return [
                    'success' => true,
                    'message' => 'API key is valid but quota exceeded. The key works correctly.',
                    'quota_exceeded' => true,
                    'status' => 429,
                    'details' => $response->json('error.message')
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('error.message') ?? 'Unknown error',
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
            'gemini-2.0-flash',
            'gemini-2.0-flash-exp',
            'gemini-1.5-flash',
            'gemini-1.5-pro',
        ];
    }
}
