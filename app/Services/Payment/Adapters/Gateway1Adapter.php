<?php

namespace App\Services\Payment\Adapters;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class Gateway1Adapter
 * * Adapter for Gateway 1 integration.
 * Handles Bearer Token authentication and translates data to English fields.
 */
class Gateway1Adapter implements PaymentGatewayInterface
{
    /** @var string */
    private string $baseUrl;

    /** @var string */
    private string $token;

    /**
     * Gateway1Adapter constructor.
     */
    public function __construct()
    {
        $this->baseUrl = config('services.gateway1.url');
        $this->token = config('services.gateway1.token');
    }

    /**
     * Processes payment using Gateway 1 API structure.
     *
     * @param array $data
     * @return array{success: bool, external_id: string|null, error: string|null}
     */
    public function processPayment(array $data): array
    {
        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/transactions", [
                    'amount'     => $data['amount'],
                    'name'       => $data['name'],
                    'email'      => $data['email'],
                    'cardNumber' => $data['card_number'],
                    'cvv'        => $data['cvv'],
                ]);

            if ($response->successful()) {
                return [
                    'success'     => true,
                    'external_id' => $response->json('id'),
                    'error'       => null
                ];
            }

            return [
                'success'     => false,
                'external_id' => null,
                'error'       => $response->json('message') ?? 'Erro desconhecido no Gateway 1'
            ];

        } catch (\Exception $e) {
            Log::error("Falha de conexão com Gateway 1: " . $e->getMessage());
            return [
                'success'     => false,
                'external_id' => null,
                'error'       => 'Falha de comunicação com o provedor.'
            ];
        }
    }

    /**
     * Refunds a transaction via charge_back endpoint.
     *
     * @param string $transactionId
     * @return bool
     */
    public function refund(string $transactionId): bool
    {
        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/transactions/{$transactionId}/charge_back");

        return $response->successful();
    }
}