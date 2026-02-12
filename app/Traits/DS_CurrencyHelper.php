<?php

namespace App\Traits;

trait DS_CurrencyHelper
{
    /**
     * List of currencies that do not support decimal places.
     * 
     * @var array
     */
    protected array $zeroDecimalCurrencies = [
        'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 
        'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'
    ];

    /**
     * Convert amount to the smallest currency unit (e.g., cents or paise).
     *
     * @param float $amount
     * @param string $currency
     * @return int
     */
    public function toSmallestUnit(float $amount, string $currency): int
    {
        $currency = strtoupper($currency);

        if (in_array($currency, $this->zeroDecimalCurrencies)) {
            return (int) $amount;
        }

        return (int) round($amount * 100);
    }

    /**
     * Format amount for display based on currency rules.
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public function formatAmount(float $amount, string $currency): string
    {
        $currency = strtoupper($currency);

        if (in_array($currency, $this->zeroDecimalCurrencies)) {
            return number_format($amount, 0, '.', '');
        }

        return number_format($amount, 2, '.', '');
    }
}
