<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmailTemplateRequest;
use App\Http\Requests\Admin\UpdateEmailTemplateRequest;
use App\Http\Requests\Admin\SendBulkEmailRequest;
use App\Services\EmailTemplateService;
use App\Models\Language;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    use DS_TranslationHelper;

    public function __construct(
        protected EmailTemplateService $templateService
    ) {}

    /**
     * Display a listing of templates.
     */
    public function index(): View
    {
        $templates = $this->templateService->getTemplates();
        return view('admin.emails.index', compact('templates'));
    }

    /**
     * Show form to create a custom template.
     */
    public function create(): View
    {
        $languages = Language::all();
        return view('admin.emails.create', compact('languages'));
    }

    /**
     * Store a new custom template.
     */
    public function store(StoreEmailTemplateRequest $request): RedirectResponse
    {
        $this->templateService->createTemplate($request->validated());
        $this->notifySuccess('admin.template_created_success');

        return redirect()->route('admin.emails.index');
    }

    /**
     * Show form to edit a template.
     */
    public function edit(int $id): View
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.emails.edit', compact('template'));
    }

    /**
     * Update an existing template.
     */
    public function update(UpdateEmailTemplateRequest $request, int $id): RedirectResponse
    {
        $this->templateService->updateTemplate($id, $request->validated());
        $this->notifySuccess('admin.template_updated_success');

        return redirect()->route('admin.emails.index');
    }

    /**
     * Show form to send bulk emails.
     */
    public function showSendForm(): View
    {
        $templates = EmailTemplate::all();
        $users = User::where('is_active', true)->get();
        return view('admin.emails.send', compact('templates', 'users'));
    }

    /**
     * Handle bulk email dispatching.
     */
    public function sendBulk(SendBulkEmailRequest $request): RedirectResponse
    {
        $this->templateService->sendBulkEmail(
            $request->template_id ? (int) $request->template_id : null,
            $request->user_ids,
            $request->only(['subject', 'content'])
        );

        $this->notifySuccess('admin.bulk_email_queued_success');

        return redirect()->route('admin.emails.index');
    }

    /**
     * Delete a custom template.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->templateService->deleteTemplate($id);
            $this->notifySuccess('admin.template_deleted_success');
        } catch (\Exception $e) {
            $this->notifyError($e->getMessage());
        }

        return redirect()->route('admin.emails.index');
    }
}
