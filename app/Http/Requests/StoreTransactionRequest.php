<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type'         => ['required'],
            'date'         => ['required', 'date'],
            'cash_bank_coa_id' => ['required', 'exists:coas,id'],
            'opponent_coa_id'  => ['required', 'exists:coas,id', 'different:cash_bank_id'],
            'total_amount' => ['required', 'numeric', 'gt:0'],
            'description'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'opponent_id.different' => 'COA Lawan tidak boleh sama dengan Akun Kas/Bank.',
            'total_amount.gt' => 'Nominal transaksi harus lebih dari 0.',
            'total_amount.required' => 'Nominal transaksi harus diisi.',
        ];
    }
}
