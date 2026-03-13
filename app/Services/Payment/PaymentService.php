<?php

namespace App\Services\Payment;

use App\Models\Gateway;
use App\Services\Payment\GatewayFactory;
use Illuminate\Support\Facades\Log;

/**
 * Class PaymentService
 *
 * This service acts as an orchestrator for payment operations.
 * It implements a Fallback Strategy for payments, ensuring high availability
 * by cycling through providers, and handles direct refunds through specific adapters.
 */
class PaymentService
{
    /**
     * Executes the payment flow using a priority-based fallback mechanism.
     *
     * It iterates through all active gateways ordered by priority. If a provider fails,
     * the system automatically attempts the next one until a success is returned
     * or all options are exhausted.
     *
     * @param array $paymentData Data including amount, customer, and credit card info.
     * @return array{success: bool, gateway_id?: int, external_id?: string, error?: string}
     */
    public function pay(array $paymentData): array
    {
        $gateways = Gateway::where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();

        foreach ($gateways as $gateway) {
            try {
                // Instantiates the appropriate adapter via Factory
                $adapter = GatewayFactory::make($gateway->name);
                
                $result = $adapter->processPayment($paymentData);

                if ($result['success']) {
                    return array_merge($result, ['gateway_id' => $gateway->id]);
                }
                
                Log::warning("Gateway [{$gateway->name}] failed: " . ($result['error'] ?? 'Unknown error'));
                
            } catch (\Exception $e) {
                Log::error("Critical error on gateway [{$gateway->name}]: " . $e->getMessage());
                // Proceeds to the next gateway in the priority list
                continue;
            }
        }

        return [
            'success' => false, 
            'error'   => 'Todos os gateways de pagamento falharam ao processar a transação.'
        ];
    }

    /**
     * Processes a refund operation directly on the original payment provider.
     *
     * Unlike the payment flow, refunds are strictly bound to the gateway used
     * during the initial transaction to ensure financial consistency.
     *
     * @param string $gatewayName The name of the gateway to be instantiated.
     * @param string $externalId The unique transaction ID provided by the external gateway.
     * @return bool True on success, false otherwise.
     * @throws \Exception If the Factory fails to resolve the adapter.
     */
    public function refund(string $gatewayName, string $externalId): bool
    {
        // Resolves the specific adapter based on the original transaction record
        $adapter = GatewayFactory::make($gatewayName);
        
        // Executes the refund through the implementation defined in the Contract
        return $adapter->refund($externalId);
    }
}