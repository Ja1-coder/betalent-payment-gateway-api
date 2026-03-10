<?php

namespace App\Services\Payment;

use App\Services\Payment\Adapters\Gateway1Adapter;
use App\Services\Payment\Adapters\Gateway2Adapter;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

/**
 * Class GatewayFactory
 * * Responsible for instantiating the appropriate payment gateway adapter
 * based on the provider's name.
 */
class GatewayFactory
{
    /**
     * Factory method to create a concrete gateway instance.
     *
     * @param string $gatewayName
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public static function make(string $gatewayName): PaymentGatewayInterface
    {
        return match (strtolower($gatewayName)) {
            'gateway 1' => new Gateway1Adapter(),
            'gateway 2' => new Gateway2Adapter(),
            default     => throw new InvalidArgumentException("O adaptador do gateway [{$gatewayName}] não é suportado."),
        };
    }
}