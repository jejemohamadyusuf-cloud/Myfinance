<?php

namespace App\Http\Requests;

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

    protected function prepareForValidation(): void
    {
        $cashBankId = $this->cash_bank_id ?? $this->cash_bank_coa_id;
        $opponentId = $this->opponent_id ?? $this->opponent_coa_id;

        $this->merge([
            'cash_bank_id'     => $cashBankId,
            'cash_bank_coa_id' => $cashBankId,
            'opponent_id'      => $opponentId,
            'opponent_coa_id'  => $opponentId,
        ]);
    }

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
            'cash_bank_id.required'     => 'Akun Kas / Bank wajib dipilih.',
            'cash_bank_coa_id.required' => 'Akun Kas / Bank wajib dipilih.',
            'opponent_id.required'      => 'Akun Lawan wajib dipilih.',
            'opponent_coa_id.required'  => 'Akun Lawan wajib dipilih.',
            'opponent_id.different'     => 'Akun Lawan tidak boleh sama dengan Akun Kas/Bank.',
            'total_amount.gt'          => 'Nominal transaksi harus lebih dari 0.',
            'total_amount.required'    => 'Nominal transaksi harus diisi.',
        ];
    }
}
