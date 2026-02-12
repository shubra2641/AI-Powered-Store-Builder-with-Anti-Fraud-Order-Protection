<?php

namespace App\DTOs;

class DS_PaymentResponse
{
    public function __construct(
        public bool $success,
        public ?string $transactionId = null,
        public ?string $checkoutUrl = null,
        public array $metadata = [],
        public ?string $error = null
    ) {}

    public static function successful(string $transactionId, ?string $checkoutUrl = null, array $metadata = []): self
    {
        return new self(
            success: true,
            transactionId: $transactionId,
            checkoutUrl: $checkoutUrl,
            metadata: $metadata
        );
    }

    public static function failed(string $error, array $metadata = []): self
    {
        return new self(
            success: false,
            error: $error,
            metadata: $metadata
        );
    }
}
