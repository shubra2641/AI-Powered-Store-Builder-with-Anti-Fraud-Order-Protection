<?php

namespace App\Services\Communication;

use App\Models\DS_Integration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\LoadsIntegrationSettings;
/**
 * Class SendgridService
 *
 * Handles sending emails via SendGrid API.
 * Follows SRP: Focused only on email delivery.
 *
 * @package App\Services\Communication
 */
class SendgridService
{
    use LoadsIntegrationSettings;

    /**
     * SendgridService constructor.
     *
     * @param int|null $userId
     */
    public function __construct(?int $userId = null)
    {
        $this->loadSettings($userId, 'sendgrid');
    }

    /**
     * Send an email via SendGrid.
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param array $options
     * @return bool
     */
    public function sendEmail(string $to, string $subject, string $content, array $options = []): bool
    {
        if (!$this->active || empty($this->settings['api_key'])) {
            Log::warning('SendGrid Service is not active or missing API key.');
            return false;
        }

        $apiKey = $this->settings['api_key'];
        $fromEmail = $this->settings['from_email'] ?? config('mail.from.address');
        $fromName = $this->settings['from_name'] ?? config('mail.from.name');

        try {
            $response = Http::withToken($apiKey)
                ->post('https://api.sendgrid.com/v3/mail/send', [
                    'personalizations' => [
                        [
                            'to' => [['email' => $to]],
                            'subject' => $subject,
                        ],
                    ],
                    'from' => [
                        'email' => $fromEmail,
                        'name' => $fromName,
                    ],
                    'content' => [
                        [
                            'type' => 'text/html',
                            'value' => $content,
                        ],
                    ],
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('SendGrid Email Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the service is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
