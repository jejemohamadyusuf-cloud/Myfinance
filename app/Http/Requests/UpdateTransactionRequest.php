<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
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
            'type' => ['required'],
            'date' => ['required', 'date'],
            'cash_bank_coa_id' => ['required', 'exists:coas,id'],
            'opponent_coa_id' => ['required', 'exists:coas,id', 'different:cash_bank_id'],
            'total_amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'opponent_coa_id.different' => 'Opponent Coa can`t be the same',
            'total_amount.gt' => 'value amount must be greater than 0'
        ];
    }
}
