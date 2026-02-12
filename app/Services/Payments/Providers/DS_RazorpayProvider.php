<?php

namespace App\Services\Payments\Providers;

use App\Contracts\DS_PaymentProviderInterface;
use App\DTOs\DS_PaymentResponse;
use Illuminate\Http\Request;
use Razorpay\Api\Api as RazorpayApi;
use Exception;
use App\Traits\DS_CurrencyHelper;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DS_RazorpayProvider implements DS_PaymentProviderInterface
{
    use DS_CurrencyHelper;

    protected RazorpayApi $api;

    public function __construct(protected array $config)
    {
        $this->api = new RazorpayApi(
            $this->config['key_id'] ?? '',
            $this->config['key_secret'] ?? ''
        );
    }

    public function createPayment(float $amount, string $currency, array $metadata = []): DS_PaymentResponse
    {
        try {
            $order = $this->api->order->create([
                'receipt' => $metadata['transaction_id'] ?? (string) Str::orderedUuid(),
                'amount' => $this->toSmallestUnit($amount, $currency),
                'currency' => strtoupper($currency),
                'notes' => [
                    'user_id' => $metadata['user_id'] ?? null,
                    'product_name' => $metadata['product_name'] ?? 'Credits Purchase',
                ],
            ]);

            return DS_PaymentResponse::successful(
                transactionId: $order->id,
                metadata: [
                    'amount' => $order->amount,
                    'currency' => $order->currency,
                    'key' => $this->config['key_id'] ?? '',
                ]
            );
        } catch (Exception $e) {
            return DS_PaymentResponse::failed($e->getMessage());
        }
    }

    public function handleWebhook(Request $request): bool
    {
        try {
            $webhookSecret = $this->config['webhook_secret'] ?? '';
            $signature = $request->header('X-Razorpay-Signature');
            $payload = $request->getContent(); // Get raw body

            if (empty($webhookSecret) || empty($signature)) {
                return false;
            }

            $this->api->utility->verifyWebhookSignature($payload, $signature, $webhookSecret);

            $data = json_decode($payload, true);

            if (($data['event'] ?? '') === 'order.paid') {
                $orderId = $data['payload']['order']['entity']['id'] ?? null;
                
                if ($orderId) {
                    // Resolve service to avoid constructor injection (circular dependency risk)
                    $subscriptionService = app(SubscriptionService::class);
                    $subscriptionService->completePurchase($orderId);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error('Razorpay Webhook Error: ' . $e->getMessage());
            return false;
        }
    }

    public function verifyPayment(string $transactionId): bool
    {
        try {
            $order = $this->api->order->fetch($transactionId);
            return $order->status === 'paid';
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProviderSlug(): string
    {
        return 'razorpay';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Razorpay',
            'icon' => 'fas fa-money-bill-wave',
            'color' => 'blue',
            'type' => 'payment',
            'category' => 'payment',
            'desc_key' => 'admin.payment_gateway',
            'sub_key' => 'admin.razorpay_desc'
        ];
    }

    public function getFormFields(): array
    {
        return [
            'key_id' => [
                'label' => 'Key ID',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'key_secret' => [
                'label' => 'Key Secret',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'webhook_secret' => [
                'label' => 'Webhook Secret',
                'type' => 'text',
                'rule' => 'nullable|string'
            ],
        ];
    }
}
