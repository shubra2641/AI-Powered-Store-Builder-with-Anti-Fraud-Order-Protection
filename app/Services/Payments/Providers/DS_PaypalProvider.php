<?php

namespace App\Services\Payments\Providers;

use App\Contracts\DS_PaymentProviderInterface;
use App\DTOs\DS_PaymentResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Traits\DS_CurrencyHelper;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DS_PaypalProvider implements DS_PaymentProviderInterface
{
    use DS_CurrencyHelper;

    protected string $baseUrl;

    public function __construct(protected array $config)
    {
        $this->baseUrl = ($this->config['mode'] ?? 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function createPayment(float $amount, string $currency, array $metadata = []): DS_PaymentResponse
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => strtoupper($currency),
                            'value' => $this->formatAmount($amount, $currency),
                        ],
                        'description' => $metadata['product_name'] ?? 'Credits Purchase',
                        'reference_id' => $metadata['transaction_id'] ?? (string) Str::orderedUuid(),
                    ]],
                    'application_context' => [
                        'return_url' => $metadata['success_url'] ?? url('/payment/success'),
                        'cancel_url' => $metadata['cancel_url'] ?? url('/payment/cancel'),
                        'brand_name' => config('app.name'),
                        'user_action' => 'PAY_NOW',
                    ],
                ]);

            if ($response->failed()) {
                Log::error('PayPal Order Creation Failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'metadata' => $metadata
                ]);
                return DS_PaymentResponse::failed(__('payments.provider_error', ['provider' => 'PayPal']));
            }

            $order = $response->json();
            $approveUrl = collect($order['links'])->where('rel', 'approve')->first()['href'];

            return DS_PaymentResponse::successful(
                transactionId: $order['id'],
                checkoutUrl: $approveUrl
            );
        } catch (Exception $e) {
            return DS_PaymentResponse::failed($e->getMessage());
        }
    }

    public function handleWebhook(Request $request): bool
    {
        try {
            $webhookId = $this->config['webhook_id'] ?? null;
            
            if (!$webhookId) {
                Log::warning('PayPal Webhook ID not configured.');
                return false;
            }

            $body = $request->getContent();
            $headers = $request->headers->all();

            // Verify Signature using PayPal API
            $verifyResponse = Http::withToken($this->getAccessToken())
                ->post("{$this->baseUrl}/v1/notifications/verify-webhook-signature", [
                    'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
                    'cert_url'          => $request->header('PAYPAL-CERT-URL'),
                    'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
                    'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
                    'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                    'webhook_id'        => $webhookId,
                    'webhook_event'     => json_decode($body, true),
                ]);

            if ($verifyResponse->json()['verification_status'] !== 'SUCCESS') {
                Log::error('PayPal Webhook Signature Verification Failed');
                return false;
            }

            $data = json_decode($body, true);
            $eventType = $data['event_type'] ?? '';

            if ($eventType === 'CHECKOUT.ORDER.APPROVED' || $eventType === 'PAYMENT.CAPTURE.COMPLETED') {
                $resource = $data['resource'];
                // reference_id was sent during creation as transaction_id
                $transactionId = $resource['purchase_units'][0]['reference_id'] ?? null; 

                // Fallback: sometimes ID is in the resource itself if it's the Order object
                if (!$transactionId && isset($resource['id'])) {
                     // If we track by Order ID (which we do in createPayment returning order['id'])
                     $transactionId = $resource['id'];
                }

                if ($transactionId) {
                     // Resolve service 
                    $subscriptionService = app(SubscriptionService::class);
                    // Note: If transactionId is the Order ID, completePurchase might need to handle it.
                    // SubscriptionService::completePurchase searches by transaction_id or id.
                    // Our createPayment returns order['id'] as transactionId.
                    // So passing order['id'] is correct.
                    $subscriptionService->completePurchase($transactionId);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error('PayPal Webhook Error: ' . $e->getMessage());
            return false;
        }
    }

    public function verifyPayment(string $transactionId): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$transactionId}");

            if ($response->failed()) {
                return false;
            }

            $order = $response->json();
            return ($order['status'] ?? '') === 'COMPLETED' || ($order['status'] ?? '') === 'APPROVED';
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProviderSlug(): string
    {
        return 'paypal';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'PayPal',
            'icon' => 'fab fa-paypal',
            'color' => 'blue',
            'type' => 'payment',
            'category' => 'payment',
            'desc_key' => 'admin.payment_gateway',
            'sub_key' => 'admin.paypal_desc'
        ];
    }

    public function getFormFields(): array
    {
        return [
            'client_id' => [
                'label' => 'Client ID',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'client_secret' => [
                'label' => 'Client Secret',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'app_id' => [
                'label' => 'App ID (Optional)',
                'type' => 'text',
                'rule' => 'nullable|string'
            ],
            'webhook_id' => [
                'label' => 'Webhook ID',
                'type' => 'text',
                'rule' => 'nullable|string'
            ],
        ];
    }

    protected function getAccessToken(): string
    {
        $clientId = $this->config['client_id'] ?? '';
        $clientSecret = $this->config['client_secret'] ?? '';

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            Log::error('PayPal Auth Failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new Exception(__('payments.auth_failed', ['provider' => 'PayPal']));
        }

        return $response->json()['access_token'];
    }
}
