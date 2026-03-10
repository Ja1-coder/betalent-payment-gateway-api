<?php

namespace App\Services\Payment\Adapters;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class Gateway2Adapter
 * * Adapter for Gateway 2 integration. 
 * Handles authentication via custom headers and translates data to Portuguese fields.
 */
class Gateway2Adapter implements PaymentGatewayInterface
{
    /** @var string */
    private string $baseUrl;

    /** @var string */
    private string $token;

    /** @var string */
    private string $secret;

    /**
     * Gateway2Adapter constructor.
     */
    public function __construct()
    {
        $this->baseUrl = config('services.gateway2.url');
        $this->token   = config('services.gateway2.token');
        $this->secret  = config('services.gateway2.secret');
    }

    /**
     * Processes payment using Gateway 2 API structure.
     * * @param array $data
     * @return array{success: bool, external_id: string|null, error: string|null}
     */
    public function processPayment(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Gateway-Auth-Token'  => $this->token,
                'Gateway-Auth-Secret' => $this->secret,
            ])->post("{$this->baseUrl}/transacoes", [
                'valor'        => $data['amount'],
                'nome'         => $data['name'],
                'email'        => $data['email'],
                'numeroCartao' => $data['card_number'],
                'cvv'          => $data['cvv'],
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
                'error'       => $response->json('mensagem') ?? 'Erro desconhecido no Gateway 2'
            ];

        } catch (\Exception $e) {
            Log::error("Falha de conexão com Gateway 2: " . $e->getMessage());
            return [
                'success'     => false,
                'external_id' => null,
                'error'       => 'Provedor de pagamento 2 indisponível.'
            ];
        }
    }

    /**
     * Refunds a transaction via POST body.
     * * @param string $transactionId
     * @return bool
     */
    public function refund(string $transactionId): bool
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => $this->token,
            'Gateway-Auth-Secret' => $this->secret,
        ])->post("{$this->baseUrl}/transacoes/reembolso", [
            'id' => $transactionId
        ]);

        return $response->successful();
    }
}