<?php

namespace App\Services\Payment;

use App\Models\Gateway;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;

/**
 * Class PaymentService
 * * Orchestrates the payment process using a fallback mechanism 
 * across available providers based on their priority.
 */
class PaymentService
{
    /**
     * Processes payment by iterating through active gateways by priority.
     *
     * @param array $paymentData
     * @return array{success: bool, gateway_id?: int, external_id?: string, error?: string}
     */
    public function pay(array $paymentData): array
    {
        $gateways = Gateway::where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();

        foreach ($gateways as $gateway) {
            try {
                $adapter = GatewayFactory::make($gateway->name);
                
                $result = $adapter->processPayment($paymentData);

                if ($result['success']) {
                    return array_merge($result, ['gateway_id' => $gateway->id]);
                }
                
                Log::warning("Gateway {$gateway->name} falhou: " . ($result['error'] ?? 'Erro desconhecido'));
                
            } catch (\Exception $e) {
                Log::error("Erro crítico no gateway {$gateway->name}: " . $e->getMessage());
                continue;
            }
        }

        return [
            'success' => false, 
            'error'   => 'Todos os gateways de pagamento falharam ao processar a transação.'
        ];
    }
}