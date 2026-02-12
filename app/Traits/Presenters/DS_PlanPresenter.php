<?php

namespace App\Traits\Presenters;

trait DS_PlanPresenter
{
    /**
     * Get the name for the current locale.
     */
    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? ($this->name['en'] ?? '');
    }

    /**
     * Get the price for display (formatted).
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price / 100, 2);
    }

    /**
     * Get the description for the current locale.
     */
    public function getTranslatedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $this->description[$locale] ?? ($this->description['en'] ?? null);
    }

    /**
     * Accessor for status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? __('admin.active') : __('admin.inactive');
    }

    /**
     * Get price in dollars for forms.
     */
    public function getDollarPriceAttribute(): float
    {
        return round($this->price / 100, 2);
    }
}
