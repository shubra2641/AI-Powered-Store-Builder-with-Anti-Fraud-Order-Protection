<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DS_LandingPage;
use App\Services\DS_LandingPageService;
use App\Services\AIService;
use App\Services\SettingsService;
use App\Traits\DS_TranslationHelper;
use App\Http\Requests\Admin\UploadMediaRequest;
use App\Helpers\DS_IconHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Services\DS_ComponentRenderer;
use App\Services\Tracking\PixelManager;

class DS_LandingPageController extends Controller
{
    use DS_TranslationHelper;

    public function __construct(
        protected DS_LandingPageService $landingPageService,
        protected AIService $aiService,
        protected DS_ComponentRenderer $componentRenderer
    ) {}

    /**
     * Upload media file (Image) for landing page builder.
     * Uses strict Request validation and Service layer.
     */
    public function uploadMedia(UploadMediaRequest $request): JsonResponse
    {
        try {
            if ($request->hasFile('file')) {
                $path = $this->landingPageService->uploadMedia($request->file('file'));
                
                return response()->json([
                    'success' => true,
                    'url' => asset('storage/' . $path),
                    'message' => __('admin.upload_success')
                ]);
            }

            return response()->json(['success' => false, 'message' => __('admin.no_file_uploaded')], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('admin.generic_error')], 500);
        }
    }

    /**
     * Display a listing of landing pages.
     */
    public function index(): View
    {
        $pages = $this->landingPageService->getPaginatedPages(12);
        $stats = $this->landingPageService->getStats();

        return view('admin.landing_pages.index', compact('pages', 'stats'));
    }

    /**
     * Show the builder for a specific landing page.
     */
    public function builder(DS_LandingPage $landing_page): View
    {
        $components = \App\Models\DS_LandingPageComponent::all();
        $icons = DS_IconHelper::getIcons();
        
        $builderConfig = [
            "initialContent" => $landing_page->builder_content ?? [],
            "components" => $components,
            "icons" => $icons,
            "csrfToken" => csrf_token(),
            "saveRoute" => route('admin.landing-pages.save', $landing_page->id),
            "uploadRoute" => route('admin.landing-pages.upload-media'),
            "exportRoute" => route('admin.landing-pages.export', $landing_page->id),
            "translations" => [
                "library" => __('admin.library'),
                "variants" => __('admin.variants'),
                "confirm" => __('admin.confirm') ?? 'Confirm',
                "header" => __('admin.header'),
                "hero" => __('admin.hero'),
                "features" => __('admin.features'),
                "stats" => __('admin.stats'),
                "pricing" => __('admin.pricing'),
                "cta" => __('admin.cta'),
                "footer" => __('admin.footer'),
                "export_to_zip" => __('admin.export_to_zip'),
                "download_page" => __('admin.download_page'),
                "page_structure" => __('admin.page_structure'),
                "add_section" => __('admin.add_section'),
                "edit_active_section" => __('admin.edit_active_section'),
                "back" => __('admin.back'),
                "save_page" => __('admin.save_page'),
                "processing" => __('admin.processing'),
                "ready" => __('admin.ready'),
                "refresh" => __('admin.refresh'),
                "saved_successfully" => __('admin.saved_successfully') ?? 'Saved successfully',
                "error" => __('admin.error') ?? 'Error',
                "confirm_delete" => __('admin.confirm_delete') ?? 'Are you sure?',
                "delete" => __('admin.delete') ?? 'Delete',
                "close" => __('admin.close') ?? 'Close',
                "view_live" => __('admin.view_live') ?? 'View Live',
                "mobile" => __('admin.mobile') ?? 'Mobile',
                "tablet" => __('admin.tablet') ?? 'Tablet',
                "desktop" => __('admin.desktop') ?? 'Desktop',
                "no_sections_added" => __('admin.no_sections_added') ?? 'No sections added yet',
                "content" => __('admin.content') ?? 'Content',
                "styling" => __('admin.styling') ?? 'Styling',
                "section_settings" => __('admin.section_settings') ?? 'Section Settings',
                "section_content" => __('admin.section_content') ?? 'Section Content',
                "generic_error" => __('admin.generic_error'),
                "background_color" => __('admin.background_color') ?? 'Background Color',
                "text_color" => __('admin.text_color') ?? 'Text Color',
                "padding_px" => __('admin.padding_px') ?? 'Padding (px)',
                "done" => __('admin.done') ?? 'Done',
                "item" => __('admin.item') ?? 'Item',
                "remove" => __('admin.remove') ?? 'Remove',
                "add" => __('admin.add') ?? 'Add',
                "last_saved" => __('admin.last_saved'),
                "unsaved_changes" => __('admin.unsaved_changes'),
                "unsaved_changes_title" => __('admin.unsaved_changes_title'),
                "unsaved_changes_msg" => __('admin.unsaved_changes_msg'),
                "stay" => __('admin.stay'),
                "leave" => __('admin.leave'),
                "page_info" => __('admin.page_info'),
                "select_to_edit" => __('admin.select_to_edit'),
                "page_structure_hint" => __('admin.page_structure_hint'),
                "empty_state_title" => __('admin.empty_state_title'),
                "empty_state_msg" => __('admin.empty_state_msg')
            ]
        ];

        return view('admin.landing_pages.builder_v2', compact('landing_page', 'components', 'icons', 'builderConfig'));
    }

    /**
     * Handle AI generation requests.
     */
    public function generate(Request $request): RedirectResponse
    {
        $request->validate(['prompt' => 'required|string']);

        try {
            $structure = $this->aiService->generateLandingPageStructure($request->prompt);
            
            $page = $this->landingPageService->createPage([
                'user_id' => Auth::id(),
                'title' => 'Generated: ' . Str::limit($request->prompt, 30),
                'language_id' => app(SettingsService::class)->getCurrentLanguageId(),
                'builder_content' => $structure['sections'],
            ]);

            $this->notifySuccess('admin.landing_page_generated_success');
            return redirect()->route('admin.landing-pages.builder', $page->id);
        } catch (\Exception $e) {
            $this->notifyError($e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Handle AI generation requests via AJAX (for builder).
     */
    public function generateAjax(Request $request, DS_LandingPage $landing_page): JsonResponse
    {
        $request->validate(['prompt' => 'required|string']);

        try {
            $structure = $this->aiService->generateLandingPageStructure($request->prompt);
            return response()->json([
                'success' => true,
                'sections' => $structure['sections']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created landing page.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['title' => 'required|string']);
        
        $this->landingPageService->createPage($request->all() + [
            'user_id' => Auth::id(),
            'language_id' => app(SettingsService::class)->getCurrentLanguageId()
        ]);

        $this->notifySuccess('admin.landing_page_created_success');
        return redirect()->route('admin.landing-pages.index');
    }

    /**
     * Save builder content for a landing page.
     */
    public function save(Request $request, DS_LandingPage $landing_page): JsonResponse
    {
        $request->validate([
            'builder_content' => 'present|array',
            'html' => 'nullable|string',
            'css' => 'nullable|string',
        ]);

        $this->landingPageService->updatePage($landing_page, [
            'builder_content' => $request->builder_content,
        ]);

        return response()->json(['success' => true, 'message' => __('admin.saved_successfully')]);
    }

    /**
     * Remove the specified landing page.
     */
    public function destroy(DS_LandingPage $landing_page): RedirectResponse
    {
        $this->landingPageService->deletePage($landing_page);
        $this->notifySuccess('admin.landing_page_deleted_success');
        return redirect()->route('admin.landing-pages.index');
    }

    /**
     * View the public version of a landing page.
     */
    public function view(Request $request, string $slug)
    {
        $landingPage = $this->landingPageService->findBySlug($slug);
        
        if (!$landingPage) {
            abort(404);
        }

        if ($request->has('preview')) {
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }

        $isRtl = app()->getLocale() == 'ar';
        $cachedHtml = null;
        $processedSections = [];

        if (!$request->has('preview') && !$request->has('sections') && !empty($landingPage->cached_html)) {
            $directionKey = $isRtl ? 'rtl' : 'ltr';
        $cachedHtml = $landingPage->cached_html[$directionKey] ?? null;
        } else {
            $sectionsData = $landingPage->builder_content ?? [];
            if ($request->has('sections')) {
                $sectionsData = is_array($request->sections) ? $request->sections : json_decode($request->sections, true);
            }

            $processedSections = $this->prepareSections($sectionsData, $isRtl);
    
            if ($request->ajax() && $request->has('sections')) {
                $html = '';
                foreach ($processedSections as $section) {
                    if (!empty($section['template'])) {
                        $html .= view($section['template'], $section)->render();
                    }
                }
                return response($html);
            }
        }

        // Facebook CAPI Integration (Server-side track)
        $capiSettings = \App\Models\DS_Integration::where('user_id', $landingPage->user_id)
            ->where('service', 'facebook_capi')
            ->where('is_active', true)
            ->first();

        if ($capiSettings) {
             (new \App\Services\Tracking\FacebookCapiService($capiSettings->settings))->sendEvent('PageView');
        }

        return view('landing.public', [
            'landingPage' => $landingPage,
            'sections' => $processedSections,
            'cachedHtml' => $cachedHtml,
            'isRtl' => $isRtl,
            'trackingPixels' => (new PixelManager())->render($landingPage->user_id)
        ]);
    }

    /**
     * Prepare sections for rendering.
     */
    protected function prepareSections(array $rawSections, bool $isRtl): array
    {
        $components = \App\Models\DS_LandingPageComponent::all()->keyBy('blade_template');
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

    /**
     * Export the landing page as a ZIP file containing standalone HTML.
     */
    public function export(DS_LandingPage $landing_page)
    {
        $tempFile = $this->landingPageService->exportAsZip($landing_page);
        $fileName = Str::slug($landing_page->title ?: 'landing-page') . '.zip';

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
