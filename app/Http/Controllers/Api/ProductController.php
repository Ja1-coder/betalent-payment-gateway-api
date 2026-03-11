<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ProductController
 *
 * Provides endpoints for product catalog management.
 * Includes public listing and administrative actions (create/delete)
 * protected by role-based access control.
 */
class ProductController extends Controller
{
    /**
     * Retrieve a list of all products in the database.
     * * @return JsonResponse Returns a JSON array of all Product models.
     */
    public function index(): JsonResponse
    {
        return response()->json(Product::all());
    }

    /**
     * Create and persist a new product.
     *
     * Accessible only to users with 'admin' privileges.
     * Validates input data through ProductRequest.
     * * @param ProductRequest $request Validated product data (name, description, amount).
     * @return JsonResponse Returns the created product and success message.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            'message' => 'Produto cadastrado com sucesso!',
            'product' => $product
        ], 201);
    }

    /**
     * Delete a specific product from the database.
     *
     * Uses Laravel Route Model Binding to locate the product.
     * Restricted to users with 'admin' privileges.
     * * @param Product $product The product instance to be deleted.
     * @return JsonResponse Returns a confirmation message of the deletion.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Produto removido com sucesso.'
        ]);
    }
}