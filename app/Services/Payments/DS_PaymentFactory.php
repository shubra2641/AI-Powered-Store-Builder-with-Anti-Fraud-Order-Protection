<?php

namespace App\Services\Payments;

use App\Contracts\DS_PaymentProviderInterface;
use App\Models\DS_PaymentGateway;
use App\Services\Payments\Providers\DS_PaypalProvider;
use App\Services\Payments\Providers\DS_RazorpayProvider;
use App\Services\Payments\Providers\DS_StripeProvider;
use App\Services\Payments\Providers\DS_BankTransferProvider;
use InvalidArgumentException;

/**
 * Class DS_PaymentFactory
 * 
 * Factory class for instantiating the appropriate payment gateway provider 
 * based on the requested service slug.
 */
class DS_PaymentFactory
{
    /**
     * Get all supported payment gateway slugs.
     *
     * @return array
     */
    public static function getSupportedGateways(): array
    {
        return [
            'stripe',
            'paypal',
            'razorpay',
            'bank_transfer',
        ];
    }

    /**
     * Instantiate a payment provider instance.
     *
     * @param string $slug
     * @return DS_PaymentProviderInterface
     * @throws InvalidArgumentException
     */
    public static function make(string $slug): DS_PaymentProviderInterface
    {
        $gateway = DS_PaymentGateway::where('slug', $slug)->first();
        $credentials = $gateway ? ($gateway->credentials ?? []) : [];

        return match ($slug) {
            'stripe'        => new DS_StripeProvider($credentials),
            'paypal'        => new DS_PaypalProvider($credentials),
            'razorpay'      => new DS_RazorpayProvider($credentials),
            'bank_transfer' => new DS_BankTransferProvider($credentials),
            default         => throw new InvalidArgumentException("Unsupported payment gateway: {$slug}"),
        };
    }

}
