<?php

namespace App\Traits\Presenters;

trait DS_LandingPagePresenter
{
    /**
     * Accessor for search-friendly text.
     */
    public function getSearchTextAttribute(): string
    {
        return strtolower($this->translations->first()?->title ?? '');
    }

    /**
     * Accessor for status slug.
     */
    public function getStatusSlugAttribute(): string
    {
        return $this->is_active ? 'active' : 'draft';
    }

    /**
     * Accessor for correct preview URL.
     */
    public function getPreviewUrlAttribute(): string
    {
        return route('lp.view', $this->slug);
    }
}
