<?php

namespace App\Services;

use App\Models\DS_LandingPage;
use App\Models\DS_LandingPageTranslation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\DS_LandingPageComponent;
use App\Traits\DS_UploadHelper;
use App\Services\Tracking\PixelManager;

class DS_LandingPageService
{
    protected $componentRenderer;

    public function __construct(DS_ComponentRenderer $componentRenderer)
    {
        $this->componentRenderer = $componentRenderer;
    }

    /**
     * Get paginated landing pages.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedPages(int $perPage = 12)
    {
        return DS_LandingPage::with('translations')->latest()->paginate($perPage);
    }

    /**
     * Get landing page statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'total' => DS_LandingPage::count(),
            'active' => DS_LandingPage::where('is_active', true)->count(),
            'pending' => DS_LandingPage::where('is_active', false)->count(),
            'views' => 0
        ];
    }

    /**
     * Get all landing pages with their translations.
     *
     * @return Collection
     */
    public function getAllPages(): Collection
    {
        return DS_LandingPage::with('translations')->latest()->get();
    }

    public function createPage(array $data): DS_LandingPage
    {
        return DB::transaction(function () use ($data) {
            $slug = $data['slug'] ?? Str::slug($data['title']);
            
            $originalSlug = $slug;
            $count = 1;
            while (DS_LandingPage::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $cachedHtml = null;
            if (!empty($data['builder_content'])) {
                $cachedHtml = [
                    'ltr' => $this->renderHtml($data['builder_content'], false),
                    'rtl' => $this->renderHtml($data['builder_content'], true),
                ];
            }

            $page = DS_LandingPage::create([
                'user_id' => $data['user_id'] ?? auth()->id(),
                'slug' => $slug,
                'builder_content' => $data['builder_content'] ?? [],
                'cached_html' => $cachedHtml,
                'is_active' => $data['is_active'] ?? true,
            ]);

            DS_LandingPageTranslation::create([
                'landing_page_id' => $page->id,
                'language_id' => $data['language_id'],
                'title' => $data['title'],
                'meta_description' => $data['meta_description'] ?? null,
            ]);

            return $page;
        });
    }

    /**
     * Update an existing landing page.
     *
     * @param DS_LandingPage $page
     * @param array $data
     * @return DS_LandingPage
     */
    public function updatePage(DS_LandingPage $page, array $data): DS_LandingPage
    {
        return DB::transaction(function () use ($page, $data) {
            $slug = $data['slug'] ?? $page->slug;

            if ($slug !== $page->slug) {
                $originalSlug = $slug;
                $count = 1;
                while (DS_LandingPage::where('slug', $slug)->where('id', '!=', $page->id)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
            }

            $updateData = [
                'slug' => $slug,
                'is_active' => $data['is_active'] ?? $page->is_active,
            ];

            if (isset($data['builder_content'])) {
                $updateData['builder_content'] = $data['builder_content'];
                $updateData['cached_html'] = [
                    'ltr' => $this->renderHtml($data['builder_content'], false),
                    'rtl' => $this->renderHtml($data['builder_content'], true),
                ];
            }

            $page->update($updateData);

            if (isset($data['title'])) {
                DS_LandingPageTranslation::updateOrCreate(
                    [
                        'landing_page_id' => $page->id,
                        'language_id' => $data['language_id'],
                    ],
                    [
                        'title' => $data['title'],
                        'meta_description' => $data['meta_description'] ?? null,
                    ]
                );
            }

            return $page;
        });
    }

    /**
     * Render builder content to HTML strings.
     *
     * @param array $sections
     * @return string
     */
    protected function renderHtml(array $sections, bool $isRtl = false): string
    {
        $html = '';
        $components = DS_LandingPageComponent::all()->keyBy('blade_template');

        foreach ($sections as $section) {
            $template = $section['blade_template'] ?? null;
            $componentModel = $components->get($template);
            
            $defaults = $componentModel ? ($componentModel->config_schema ?? []) : [];
            $defaultContent = $defaults['content'] ?? $defaults;
            $section['content'] = array_merge($defaultContent, $section['content'] ?? []);
            $section['style'] = array_merge($defaults['style'] ?? [], $section['style'] ?? []);
            $section['template'] = $template;

            if ($template) {
                try {
                    $processed = $this->componentRenderer->prepare($section, $isRtl);
                    $html .= view($template, $processed)->render();
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        
        return $html;
    }

    /**
     * Delete a landing page.
     *
     * @param DS_LandingPage $page
     * @return bool
     */
    public function deletePage(DS_LandingPage $page): bool
    {
        return $page->delete();
    }

    /**
     * Find a landing page by slug.
     *
     * @param string $slug
     * @return DS_LandingPage|null
     */
    public function findBySlug(string $slug): ?DS_LandingPage
    {
        return DS_LandingPage::where('slug', $slug)->with('translations')->first();
    }

    /**
     * Upload media file for landing page.
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function uploadMedia($file): string
    {
        return $this->uploadFile($file, 'landing-pages');
    }

    /**
     * Export the landing page as a ZIP file.
     * 
     * @param DS_LandingPage $landing_page
     * @return string Path to the temporary zip file.
     */
    public function exportAsZip(DS_LandingPage $landing_page): string
    {
        $isRtl = app()->getLocale() == 'ar';
        $processedSections = $this->renderHtml($landing_page->builder_content ?? [], $isRtl); 
        // Note: renderHtml returns string, but for export we might want the full structure.
        // Re-using the logic from Controller's prepareSections but keeping it internal or reusing renderHtml if appropriate.
        // Actually, the previous controller logic was:
        // $processedSections = $this->prepareSections($landing_page->builder_content ?? [], $isRtl);
        // And then: view('landing.public', ...)->render();
        
        // Since prepareSections is protected in Controller, we should move that logic here or duplicate/adapt.
        // But wait, renderHtml returns a string of HTML, not the array of sections required for the view 'landing.public'.
        // Let's refactor `prepareSections` into this Service as well to properly support the view.
        
        $processedSections = $this->prepareSectionsForExport($landing_page->builder_content ?? [], $isRtl);

        $html = view('landing.public', [
            'landingPage' => $landing_page,
            'sections' => $processedSections,
            'isRtl' => $isRtl,
            'isExport' => true,
            'trackingPixels' => (new PixelManager())->render($landing_page->user_id)
        ])->render();

        $tempFile = tempnam(sys_get_temp_dir(), 'export_');

        $zip = new \ZipArchive();
        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFromString('index.html', $html);
            $zip->close();
        }

        return $tempFile;
    }

    /**
     * Prepare sections for export (similar to Controller logic).
     */
    protected function prepareSectionsForExport(array $rawSections, bool $isRtl): array
    {
        $components = DS_LandingPageComponent::all()->keyBy('blade_template');
        $processedSections = [];

        foreach ($rawSections as $section) {
            $template = $section['blade_template'] ?? null;
            $componentModel = $components->get($template);
            
            $defaults = $componentModel ? ($componentModel->config_schema ?? []) : [];
            $defaultContent = $defaults['content'] ?? $defaults;
            $section['content'] = array_merge($defaultContent, $section['content'] ?? []);
            $section['style'] = array_merge($defaults['style'] ?? [], $section['style'] ?? []);
            $section['template'] = $template;

            $processedSections[] = $this->componentRenderer->prepare($section, $isRtl);
        }

        return $processedSections;
    }
}
