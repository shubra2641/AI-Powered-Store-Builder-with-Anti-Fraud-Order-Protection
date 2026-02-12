<?php

namespace App\Services\Payments\Providers;

use App\Contracts\DS_PaymentProviderInterface;
use App\DTOs\DS_PaymentResponse;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Exception;
use App\Traits\DS_CurrencyHelper;
use Illuminate\Support\Facades\Log;
use App\Services\Subscriptions\SubscriptionService;

class DS_StripeProvider implements DS_PaymentProviderInterface
{
    use DS_CurrencyHelper;

    public function __construct(protected array $config)
    {
        Stripe::setApiKey($this->config['secret_key'] ?? config('cashier.secret'));
        
        if (isset($this->config['api_version'])) {
            Stripe::setApiVersion($this->config['api_version']);
        }
    }

    public function createPayment(float $amount, string $currency, array $metadata = []): DS_PaymentResponse
    {
        try {
            $amountInUnits = $this->toSmallestUnit($amount, $currency);

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency),
                        'product_data' => [
                            'name' => $metadata['product_name'] ?? 'Credits Purchase',
                        ],
                        'unit_amount' => $amountInUnits,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $metadata['success_url'] ?? url('/payment/success'),
                'cancel_url' => $metadata['cancel_url'] ?? url('/payment/cancel'),
                'metadata' => [
                    'user_id' => $metadata['user_id'] ?? null,
                    'transaction_id' => $metadata['transaction_id'] ?? null,
                ],
            ]);

            return DS_PaymentResponse::successful(
                transactionId: $session->id,
                checkoutUrl: $session->url,
                metadata: ['session_id' => $session->id]
            );
        } catch (Exception $e) {
            return DS_PaymentResponse::failed($e->getMessage());
        }
    }

    public function handleWebhook(Request $request): bool
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = $this->config['webhook_secret'] ?? config('cashier.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Stripe Webhook Error: Invalid Payload');
            return false;
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Stripe Webhook Error: Invalid Signature');
            return false;
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            
            // Retrieve transaction_id from metadata
            $transactionId = $session->metadata->transaction_id ?? null;

            if ($transactionId) {
                // Resolve SubscriptionService
                $subscriptionService = app(SubscriptionService::class);
                $subscriptionService->completePurchase($transactionId);
            }
        }

        return true;
    }

    public function verifyPayment(string $transactionId): bool
    {
        try {
            $session = StripeSession::retrieve($transactionId);
            return $session->payment_status === 'paid';
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProviderSlug(): string
    {
        return 'stripe';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Stripe',
            'icon' => 'fab fa-stripe-s',
            'color' => 'blue',
            'type' => 'payment',
            'category' => 'payment',
            'desc_key' => 'admin.payment_gateway',
            'sub_key' => 'admin.stripe_desc'
        ];
    }

    public function getFormFields(): array
    {
        return [
            'publishable_key' => [
                'label' => 'Publishable Key',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'secret_key' => [
                'label' => 'Secret Key',
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
