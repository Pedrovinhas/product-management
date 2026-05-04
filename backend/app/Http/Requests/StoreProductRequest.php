<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0.01|max:99999999.99',
            'stock_quantity' => 'required|integer|min:0|max:50000',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'description.max' => 'A descrição deve ter no máximo 2000 caracteres.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'Informe um preço válido.',
            'price.min' => 'O preço deve ser maior que zero.',
            'price.max' => 'O preço máximo permitido é R$ 99.999.999,99.',
            'stock_quantity.required' => 'A quantidade em estoque é obrigatória.',
            'stock_quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'stock_quantity.min' => 'A quantidade não pode ser negativa.',
            'stock_quantity.max' => 'A quantidade máxima é 50.000 unidades.',
        ];
    }
}
