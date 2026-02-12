<?php

namespace App\Contracts;

use App\DTOs\DS_PaymentResponse;
use Illuminate\Http\Request;

interface DS_PaymentProviderInterface
{
    public function createPayment(float $amount, string $currency, array $metadata = []): DS_PaymentResponse;

    public function handleWebhook(Request $request): bool;

    public function verifyPayment(string $transactionId): bool;

    public function getProviderSlug(): string;

    /**
     * Get the gateway metadata (name, icon, description, etc.).
     *
     * @return array
     */
    public function getMetadata(): array;

    /**
     * Get the gateway configuration fields.
     *
     * @return array
     */
    public function getFormFields(): array;
}
