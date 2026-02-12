<?php

namespace App\Services\Payments\Providers;

use App\Contracts\DS_PaymentProviderInterface;
use App\DTOs\DS_PaymentResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class DS_BankTransferProvider
 * 
 * Offline payment provider implementation for Bank Transfer transactions.
 */
class DS_BankTransferProvider implements DS_PaymentProviderInterface
{
    public function __construct(protected array $config)
    {
    }

    /**
     * Create a payment record for bank transfer.
     *
     * @param float $amount
     * @param string $currency
     * @param array $metadata
     * @return DS_PaymentResponse
     */
    public function createPayment(float $amount, string $currency, array $metadata = []): DS_PaymentResponse
    {
        return DS_PaymentResponse::successful(
            transactionId: 'BT-' . strtoupper(Str::orderedUuid()),
            metadata: [
                'details'        => $this->config['details'] ?? '',
                'require_proof'  => $this->config['require_proof'] ?? false,
            ]
        );
    }

    /**
     * Handle incoming webhooks (Not applicable for Bank Transfer).
     *
     * @param Request $request
     * @return bool
     */
    public function handleWebhook(Request $request): bool
    {
        return true;
    }

    /**
     * Verify payment status (Not applicable for offline transfers).
     *
     * @param string $transactionId
     * @return bool
     */
    public function verifyPayment(string $transactionId): bool
    {
        return false;
    }

    public function getProviderSlug(): string
    {
        return 'bank_transfer';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Bank Transfer',
            'icon' => 'fas fa-university',
            'color' => 'blue',
            'type' => 'payment',
            'category' => 'payment',
            'desc_key' => 'admin.payment_gateway',
            'sub_key' => 'admin.bank_desc'
        ];
    }

    public function getFormFields(): array
    {
        return [
            'details' => [
                'label' => 'Bank Details / Instructions',
                'type' => 'textarea',
                'rule' => 'required|string'
            ],
            'require_proof' => [
                'label' => 'Require Payment Proof',
                'type' => 'checkbox',
                'rule' => 'nullable|boolean'
            ],
        ];
    }
}
