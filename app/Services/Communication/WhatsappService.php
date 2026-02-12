<?php

namespace App\Services\Communication;

use App\Models\DS_Integration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class WhatsappService
 *
 * Handles sending messages via WhatsApp Business API (Meta).
 * Follows SRP: Focused only on message delivery.
 *
 * @package App\Services\Communication
 */
class WhatsappService
{
    use \App\Traits\LoadsIntegrationSettings;

    /**
     * WhatsappService constructor.
     *
     * @param int|null $userId
     */
    public function __construct(?int $userId = null)
    {
        $this->loadSettings($userId, 'whatsapp');
    }

    /**
     * Send a template message via WhatsApp.
     *
     * @param string $to Phone number with country code
     * @param string $templateName
     * @param array $components
     * @param string $language
     * @return bool
     */
    public function sendTemplate(string $to, string $templateName, array $components = [], string $language = 'en_US'): bool
    {
        if (!$this->active || empty($this->settings['phone_number_id']) || empty($this->settings['access_token'])) {
            Log::warning('WhatsApp Service is not active or missing configuration.');
            return false;
        }

        $phoneNumberId = $this->settings['phone_number_id'];
        $accessToken = $this->settings['access_token'];

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v17.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => $language,
                        ],
                        'components' => $components,
                    ],
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp Template Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a raw text message via WhatsApp.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (!$this->active || empty($this->settings['phone_number_id']) || empty($this->settings['access_token'])) {
            return false;
        }

        $phoneNumberId = $this->settings['phone_number_id'];
        $accessToken = $this->settings['access_token'];

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v17.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp Message Failed: ' . $e->getMessage());
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
