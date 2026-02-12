<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\DS_Page;
use App\Models\Language;
use App\Services\DS_PageService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DS_PageController extends Controller
{
    use DS_TranslationHelper;

    public function __construct(
        protected DS_PageService $pageService
    ) {}

    /**
     * Display a listing of legal pages.
     */
    public function index(): View
    {
        $pages = $this->pageService->getAllPages();
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(): View
    {
        $languages = Language::all();
        return view('admin.pages.create', compact('languages'));
    }

    /**
     * Store a newly created page.
     */
    public function store(StorePageRequest $request): RedirectResponse
    {
        $this->pageService->createPage($request->validated());

        $this->notifySuccess('admin.page_created_success');
        return redirect()->route('admin.pages.index');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(DS_Page $page): View
    {
        $languages = Language::all();
        $page->load('translations');
        return view('admin.pages.edit', compact('page', 'languages'));
    }

    /**
     * Update the specified page.
     */
    public function update(UpdatePageRequest $request, DS_Page $page): RedirectResponse
    {
        $this->pageService->updatePage($page, $request->validated());

        $this->notifySuccess('admin.page_updated_success');
        return redirect()->route('admin.pages.index');
    }

    /**
     * Remove the specified page.
     */
    public function destroy(DS_Page $page): RedirectResponse
    {
        $this->pageService->deletePage($page);

        $this->notifySuccess('admin.page_deleted_success');
        return redirect()->route('admin.pages.index');
    }
}
