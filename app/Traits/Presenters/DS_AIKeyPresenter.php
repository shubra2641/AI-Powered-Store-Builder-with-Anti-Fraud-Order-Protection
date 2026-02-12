<?php

namespace App\Traits\Presenters;

trait DS_AIKeyPresenter
{
    /**
     * Accessor for usage percentage.
     */
    public function getUsagePercentageAttribute(): int
    {
        if ($this->max_tokens <= 0) {
            return 0;
        }

        $percentage = ($this->tokens_used / $this->max_tokens) * 100;

        return (int) min(100, max(0, $percentage));
    }

    /**
     * Accessor for provider icon.
     */
    public function getProviderIconAttribute(): string
    {
        return match (strtolower($this->provider)) {
            'gemini' => 'fa-robot',
            'chatgpt', 'openai' => 'fa-comments',
            'groq' => 'fa-bolt',
            'claude', 'anthropic' => 'fa-brain',
            'perplexity' => 'fa-search',
            default => 'fa-microchip',
        };
    }

    /**
     * Accessor for status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? __('admin.available') : __('admin.inactive');
    }

    /**
     * Accessor for status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'badge-tag-green' : 'badge-tag-gray';
    }

    /**
     * Accessor for provider color.
     */
    public function getProviderColorAttribute(): string
    {
        return match (strtolower($this->provider)) {
            'gemini' => 'success',
            'chatgpt', 'openai' => 'primary',
            'groq' => 'purple',
            'claude', 'anthropic' => 'cyan',
            'perplexity' => 'orange',
            default => 'secondary',
        };
    }
}
