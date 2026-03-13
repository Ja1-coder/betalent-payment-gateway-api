<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class TransactionController
 *
 * Responsible for handling financial transaction operations, including 
 * comprehensive reporting with relationship-based filtering and 
 * payment reversal (refund) processing.
 */
class TransactionController extends Controller
{
    /**
     * Display a listing of transactions with advanced filtering capabilities.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Inicia a query com Eager Loading para evitar o problema de N+1 consultas
        $query = Transaction::with(['client', 'product', 'gateway']);

        // Filtro por Nome do Cliente (Busca dentro do relacionamento 'client')
        if ($request->has('customer_name')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        // Filtro por Status da transação
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Retorna os resultados com paginação para melhor performance da API
        $transactions = $query->latest()->paginate(15);

        return response()->json($transactions);
    }

    /**
     * Process a refund for a specific transaction.
     *
     * @param Transaction $transaction
     * @param PaymentService $paymentService
     * @return JsonResponse
     */
    public function refund(Transaction $transaction, PaymentService $paymentService): JsonResponse
    {
        // Validação de Regra de Negócio: Apenas transações PAGAS podem ser estornadas
        if ($transaction->status !== Transaction::STATUS_PAID) {
            return response()->json([
                'message' => 'Apenas transações pagas podem ser estornadas.'
            ], 400);
        }

        try {
            // Delega a operação externa para o serviço orquestrador de pagamentos
            $success = $paymentService->refund(
                $transaction->gateway->name, 
                $transaction->external_id
            );

            if ($success) {
                // Em caso de sucesso no gateway, atualiza o status local para REFUNDED (3)
                $transaction->update(['status' => Transaction::STATUS_REFUNDED]);

                return response()->json([
                    'message' => 'Transação estornada com sucesso!',
                    'status' => $transaction->status_name
                ]);
            }

            return response()->json(['message' => 'O gateway recusou o estorno.'], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar estorno: ' . $e->getMessage()
            ], 500);
        }
    }
}