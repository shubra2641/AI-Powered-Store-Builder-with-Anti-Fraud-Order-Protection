<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Jobs\SendBulkEmailJob;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmailTemplateService
{
    /**
     * Get all templates with pagination.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getTemplates(int $perPage = 10): LengthAwarePaginator
    {
        return EmailTemplate::with('language')->latest()->paginate($perPage);
    }

    /**
     * Create a new custom template.
     *
     * @param array $data Template data.
     * @return EmailTemplate
     */
    public function createTemplate(array $data): EmailTemplate
    {
        return DB::transaction(function () use ($data) {
            return EmailTemplate::create([
                'slug' => $data['slug'] ?? str()->slug($data['name']),
                'name' => $data['name'],
                'subject' => $data['subject'],
                'content' => $data['content'],
                'description' => $data['description'] ?? null,
                'language_id' => $data['language_id'],
                'is_system' => false,
            ]);
        });
    }

    /**
     * Update an existing template.
     *
     * @param int $id Template ID.
     * @param array $data Updated data.
     * @return EmailTemplate
     */
    public function updateTemplate(int $id, array $data): EmailTemplate
    {
        $template = EmailTemplate::findOrFail($id);

        return DB::transaction(function () use ($template, $data) {
            $template->update([
                'name' => $data['name'],
                'subject' => $data['subject'],
                'content' => $data['content'],
                'description' => $data['description'] ?? $template->description,
            ]);

            return $template;
        });
    }

    /**
     * Dispatch bulk email job to selected users or all active users.
     *
     * @param int|null $templateId Template ID or null for direct message.
     * @param array|null $userIds Target user IDs.
     * @param array|null $customData Custom message data.
     * @return void
     */
    public function sendBulkEmail(?int $templateId, ?array $userIds = null, ?array $customData = null): void
    {
        if ($templateId) {
            $template = EmailTemplate::findOrFail($templateId);
        } else {
            $template = [
                'subject' => $customData['subject'],
                'content' => $customData['content'],
                'name' => 'Direct Message',
                'slug' => 'direct_message'
            ];
        }

        $query = User::where('is_active', true);
        if ($userIds) {
            $query->whereIn('id', $userIds);
        }
        $users = $query->get();

        if ($users->isNotEmpty()) {
            SendBulkEmailJob::dispatch($template, $users);
        }
    }

    /**
     * Delete a custom template.
     *
     * @param int $id Template ID.
     * @return bool
     */
    public function deleteTemplate(int $id): bool
    {
        $template = EmailTemplate::findOrFail($id);

        if ($template->is_system) {
            throw new \Exception('System templates cannot be deleted.');
        }

        return $template->delete();
    }
}
