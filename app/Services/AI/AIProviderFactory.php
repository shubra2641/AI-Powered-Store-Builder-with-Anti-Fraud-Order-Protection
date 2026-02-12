<?php

namespace App\Services\AI;

use App\Models\DS_AIKey;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\GroqProvider;
use App\Services\AI\Providers\ClaudeProvider;
use App\Services\AI\Providers\PerplexityProvider;

class AIProviderFactory
{
    /**
     * Create an AI provider instance.
     * 
     * @param DS_AIKey $key
     * @return AIProviderInterface
     * @throws \Exception
     */
    public static function make(DS_AIKey $key): AIProviderInterface
    {
        return match ($key->provider) {
            'gemini' => new GeminiProvider($key->api_key, $key->model),
            'chatgpt' => new OpenAIProvider($key->api_key, $key->model),
            'groq' => new GroqProvider($key->api_key, $key->model),
            'claude' => new ClaudeProvider($key->api_key, $key->model),
            'perplexity' => new PerplexityProvider($key->api_key, $key->model),
            default => throw new \Exception("Unsupported AI Provider: {$key->provider}"),
        };
    }

    /**
     * Get all supported models aggregate.
     * 
     * @return array
     */
    public static function getAllSupportedModels(): array
    {
        // Ideally we could call static methods on providers, but for now we aggregate here or delegate.
        // Since getModels is an instance method in the interface (to allow API fetching if needed), 
        // we'll keep the hardcoded list here or in AIService for creation dropdowns, 
        // to avoid instantiating providers without keys.
        return [
            'gemini' => [
                'gemini-2.0-flash',
                'gemini-2.0-flash-exp',
                'gemini-1.5-flash',
                'gemini-1.5-pro',
            ],
            'chatgpt' => [
                'gpt-4o',
                'gpt-4-turbo',
                'gpt-4',
                'gpt-3.5-turbo',
            ],
            'groq' => [
                'llama-3.1-8b-instant',
                'llama-3.3-70b-versatile',
            ],
            'claude' => [
                'claude-3-5-sonnet-20240620',
                'claude-3-opus-20240229',
            ],
            'perplexity' => [
                'llama-3-sonar-large-32k-online',
                'llama-3-sonar-small-32k-online',
                'mixtral-8x7b-instruct',
            ],
        ];
    }
}
