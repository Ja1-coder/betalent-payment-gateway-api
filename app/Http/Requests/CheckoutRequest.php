<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CheckoutRequest
 * * Handles the validation logic for the checkout process, ensuring all 
 * required payment and product data are present and correctly formatted.
 */
class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|integer|min:1',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email',
            'card_number'  => 'required|digits:16',
            'cvv'          => 'required|digits:3',
        ];
    }
}