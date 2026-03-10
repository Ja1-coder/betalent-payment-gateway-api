<?php

namespace App\Services\Payment\Contracts;

/**
 * Interface PaymentGatewayInterface
 * Define the standard methods for any payment provider.
 */
interface PaymentGatewayInterface
{
    /**
     * Process the payment request.
     * * @param array $data Contains customer info, amount, card_info
     * @return array ['success' => bool, 'external_id' => string|null, 'error' => string|null]
     */
    public function processPayment(array $data): array;

    /**
     * Handle the refund of a transaction.
     * * @param string $transactionId
     * @return bool
     */
    public function refund(string $transactionId): bool;
}