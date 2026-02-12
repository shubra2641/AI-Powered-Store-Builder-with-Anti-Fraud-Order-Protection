<?php

namespace App\Services;

use App\Models\DS_AIKey;
use App\Services\AI\AIProviderFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Helpers\AIPromptHelper;

class AIService
{
    /**
     * Get all AI keys.
     * 
     * @return Collection
     */
    public function getAllKeys(): Collection
    {
        return DS_AIKey::all();
    }

    /**
     * Create a new AI key.
     * 
     * @param array $data
     * @return DS_AIKey
     */
    public function createKey(array $data): DS_AIKey
    {
        return DS_AIKey::create($data);
    }

    /**
     * Update an AI key.
     * 
     * @param DS_AIKey $aiKey
     * @param array $data
     * @return DS_AIKey
     */
    public function updateKey(DS_AIKey $aiKey, array $data): DS_AIKey
    {
        $aiKey->update($data);
        return $aiKey;
    }

    /**
     * Delete an AI key.
     * 
     * @param DS_AIKey $aiKey
     * @return bool
     */
    public function deleteKey(DS_AIKey $aiKey): bool
    {
        return $aiKey->delete();
    }

    /**
     * Toggle active status for an AI key.
     *
     * @param DS_AIKey $aiKey
     * @return void
     */
    public function activateKey(DS_AIKey $aiKey): void
    {
        $aiKey->update(['is_active' => !$aiKey->is_active]);
    }

    /**
     * Get supported models for a specific provider.
     * 
     * @param string|null $provider
     * @return array
     */
    public function getSupportedModels(?string $provider = null): array
    {
        $allModels = AIProviderFactory::getAllSupportedModels();

        if ($provider) {
            return $allModels[$provider] ?? [];
        }

        return $allModels;
    }

    /**
     * Test AI key connection.
     * 
     * @param DS_AIKey $aiKey
     * @return array
     */
    public function testConnection(DS_AIKey $aiKey): array
    {
        try {
            $provider = AIProviderFactory::make($aiKey);
            return $provider->testConnection();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate content with streaming support (Typewriter Effect)
     * Implements Server-Sent Events (SSE) compatible output.
     * 
     * @param string $prompt
     * @param callable $onStream Callback for each chunk
     * @return void
     */
    public function generateStreamingResponse(string $prompt, callable $onStream): void
    {
        $activeKey = DS_AIKey::where('is_active', true)->first();
        
        if (!$activeKey) {
            throw new \Exception(__('admin.no_active_ai_key'));
        }

        // Note: Real streaming would require Provider support.
        // For now, keeping the mock implementation as per previous state, 
        // to be enhanced later with provider-specific streaming.
        $text = "This is a streaming response from " . ucfirst($activeKey->provider) . ". ";
        $text .= "DropSaaS features professional AI integration with real-time output capabilities.";
        
        $words = explode(' ', $text);
        foreach ($words as $word) {
            $chunk = $word . ' ';
            $onStream($chunk);

            usleep(50000); 
        }
    }

    /**
     * Generate a structured JSON for a landing page based on a prompt.
     * Uses rotation and failover logic.
     * 
     * @param string $prompt
     * @return array
     */
    public function generateLandingPageStructure(string $prompt): array
    {
        $activeKeys = DS_AIKey::where('is_active', true)->get();
        
        $activeKeys = $activeKeys->shuffle();
        
        Log::info("AI Generation Attempted. Active keys found (shuffled): " . $activeKeys->count());
        
        $systemPrompt = AIPromptHelper::getLandingPageSystemPrompt($prompt);
        $errors = [];

        foreach ($activeKeys as $index => $activeKey) {
            try {
                if ($index > 0) {
                    usleep(500000); 
                }

                $maxTokens = $activeKey->max_tokens ?: 4000;
                
                // Specific token adjustments
                if ($activeKey->provider === 'groq') {
                    $maxTokens = 4000; 
                } elseif ($activeKey->provider === 'gemini') {
                    $maxTokens = 20000; 
                }

                $provider = AIProviderFactory::make($activeKey);
                
                $response = $provider->generateContent($systemPrompt, [
                    'max_tokens' => $maxTokens
                ]);

                if (!$response) {
                    throw new \Exception("Empty response from " . $activeKey->provider);
                }

                $cleanJson = $this->cleanAndRepairJson($response);

                $data = json_decode($cleanJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error("JSON Decode Error (" . $activeKey->provider . "): " . json_last_error_msg());
                    Log::debug("Cleaned JSON (len: " . strlen($cleanJson) . "): " . substr($cleanJson, 0, 1000) . "...");
                    throw new \Exception("Invalid JSON syntax: " . json_last_error_msg());
                }

                if (!$data || !isset($data['sections'])) {
                    throw new \Exception("Missing 'sections' key in AI response.");
                }

                if ($activeKey->last_fail_at) {
                    $activeKey->update(['last_fail_at' => null]);
                }

                return $data;

            } catch (\Exception $e) {
                $activeKey->update(['last_fail_at' => now()]);
                $errors[] = "[{$activeKey->provider}: {$activeKey->model}] " . $e->getMessage();
                continue;
            }
        }

        throw new \Exception("AI Generation Failed. Details: " . implode(' | ', $errors));
    }

    /**
     * Clean and repair JSON string.
     */
    protected function cleanAndRepairJson(string $json): string
    {
        $cleanJson = trim($json);
        
        // Remove markdown code blocks
        if (preg_match('/^```(?:json)?\s+(.*)\s+```$/s', $cleanJson, $matches)) {
            $cleanJson = $matches[1];
        }

        // Clean trailing backticks
        $cleanJson = preg_replace('/":\s*`([\s\S]*?)`([,\s]*)/', function($matches) {
            $content = $matches[1];
            $trailing = $matches[2];
            $escaped = str_replace(['"', "\n", "\r"], ['\"', "\\n", "\\r"], $content);
            return '": "' . $escaped . '"' . $trailing;
        }, $cleanJson);

        $cleanJson = preg_replace('/\\\\\s*$/m', '', $cleanJson);
        
        // Extract JSON object
        $firstBracket = strpos($cleanJson, '{');
        $lastBracket = strrpos($cleanJson, '}');
        if ($firstBracket !== false) {
            if ($lastBracket !== false && $lastBracket > $firstBracket) {
                $cleanJson = substr($cleanJson, $firstBracket, ($lastBracket - $firstBracket) + 1);
            } else {
                $cleanJson = substr($cleanJson, $firstBracket);
            }
        }

        // Remove control characters
        $cleanJson = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $cleanJson);

        // Repair truncated JSON
        $cleanJson = $this->repairTruncatedJson($cleanJson);

        // Normalize newlines in strings
        $cleanJson = preg_replace_callback('/"(.*?)"/s', function($matches) {
            return '"' . str_replace(["\n", "\r"], ["\\n", "\\r"], $matches[1]) . '"';
        }, $cleanJson);

        return $cleanJson;
    }

    /**
     * Attempts to repair truncated or malformed JSON by balancing brackets and quotes.
     */
    protected function repairTruncatedJson(string $json): string
    {
        $json = trim($json);
        if (empty($json)) return $json;

        $quoteCount = substr_count($json, '"') - substr_count($json, '\"');
        if ($quoteCount % 2 !== 0) {
            $json .= '"';
        }
        $stack = [];
        $len = strlen($json);
        for ($i = 0; $i < $len; $i++) {
            $char = $json[$i];
            if ($char == '{' || $char == '[') {
                $stack[] = $char == '{' ? '}' : ']';
            } elseif (($char == '}' || $char == ']') && !empty($stack)) {
                if (end($stack) == $char) {
                    array_pop($stack);
                }
            }
        }

        while (!empty($stack)) {
            $json .= array_pop($stack);
        }

        return $json;
    }
}
