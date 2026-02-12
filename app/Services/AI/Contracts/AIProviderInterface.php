<?php

namespace App\Services\AI\Contracts;

interface AIProviderInterface
{
    /**
     * Generate content based on the prompt.
     * 
     * @param string $prompt
     * @param array $config
     * @return string
     * @throws \Exception
     */
    public function generateContent(string $prompt, array $config = []): string;

    /**
     * Test the connection to the AI provider.
     * 
     * @return array
     */
    public function testConnection(): array;

    /**
     * Get supported models for this provider.
     * 
     * @return array
     */
    public function getModels(): array;
}
