<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DS_Page;
use App\Models\DS_PageTranslation;
use App\Models\Language;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DS_PageService
{
    /**
     * Get all pages with their translations.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPages(int $perPage = 15): LengthAwarePaginator
    {
        return DS_Page::with('translations.language')->latest()->paginate($perPage);
    }

    /**
     * Create a new legal page with translations.
     *
     * @param array $data
     * @return DS_Page
     */
    public function createPage(array $data): DS_Page
    {
        return DB::transaction(function () use ($data) {
            $firstTitle = collect($data['translations'])->first()['title'] ?? 'page-' . Str::random(5);
            $slug = Str::limit(Str::slug($firstTitle), 200, '');

            $originalSlug = $slug;
            $count = 1;
            while (DS_Page::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $page = DS_Page::create([
                'slug' => $slug,
                'is_active' => $data['is_active'] ?? true,
            ]);

            foreach ($data['translations'] as $langId => $transData) {
                DS_PageTranslation::create([
                    'page_id' => $page->id,
                    'language_id' => $langId,
                    'title' => $transData['title'],
                    'content' => $transData['content'],
                ]);
            }

            return $page;
        });
    }

    /**
     * Update an existing legal page.
     *
     * @param DS_Page $page
     * @param array $data
     * @return DS_Page
     */
    public function updatePage(DS_Page $page, array $data): DS_Page
    {
        return DB::transaction(function () use ($page, $data) {
            $page->update([
                'is_active' => $data['is_active'] ?? $page->is_active,
            ]);
            foreach ($data['translations'] as $langId => $transData) {
                DS_PageTranslation::updateOrCreate(
                    ['page_id' => $page->id, 'language_id' => $langId],
                    ['title' => $transData['title'], 'content' => $transData['content']]
                );
            }

            return $page;
        });
    }

    /**
     * Delete a page and its translations.
     *
     * @param DS_Page $page
     * @return bool
     */
    public function deletePage(DS_Page $page): bool
    {
        return DB::transaction(function () use ($page) {
            return (bool) $page->delete();
        });
    }
}
