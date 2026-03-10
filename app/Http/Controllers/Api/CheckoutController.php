<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Client;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;

/**
 * Class CheckoutController
 * * Orchestrates the purchase process, including price calculation, 
 * customer management, and payment processing through the service layer.
 */
class CheckoutController extends Controller
{
    /**
     * @param PaymentService $paymentService
     */
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Handle the purchase process.
     *
     * @param CheckoutRequest $request
     * @return JsonResponse
     */
    public function store(CheckoutRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->product_id);
        $totalAmount = $product->amount * $request->quantity;

        $client = Client::firstOrCreate(
            ['email' => $request->email],
            ['name'  => $request->name]
        );

        $paymentData = array_merge($request->validated(), [
            'amount' => $totalAmount
        ]);

        $paymentResult = $this->paymentService->pay($paymentData);

        $transaction = Transaction::create([
            'client_id'         => $client->id,
            'gateway_id'        => $paymentResult['gateway_id'] ?? null,
            'product_id'        => $product->id,
            'amount'            => $totalAmount,
            'quantity'          => $request->quantity,
            'card_last_numbers' => substr($request->card_number, -4),
            'external_id'       => $paymentResult['external_id'] ?? null,
            'status'            => $paymentResult['success'] 
                                    ? Transaction::STATUS_PAID 
                                    : Transaction::STATUS_FAILED,
        ]);

        if (!$paymentResult['success']) {
            return response()->json([
                'message' => 'Pagamento recusado.',
                'error'   => $paymentResult['error'],
                'transaction_id' => $transaction->id
            ], 402);
        }

        return response()->json([
            'message' => 'Compra realizada com sucesso!',
            'transaction' => $transaction
        ], 201);
    }
}