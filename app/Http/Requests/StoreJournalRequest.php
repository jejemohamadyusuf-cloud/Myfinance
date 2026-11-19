<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJournalRequest extends FormRequest
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
            'transaction_data' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'details' => ['required', 'array', 'min:2'],
            'details.*coa_id' => ['required', 'exists:coas,id'],
            'details.*debit' => ['nullable', 'numeric', 'min:0'],
            'details.*credit' => ['nullable', 'numeric', 'min:0'],
            'details.*memo' => ['nullable', 'string', 'max:255']
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $details = $this->input('details', []);
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($details as $item) {
                $totalDebit += floatval($item['debit'] ?? 0);
                $totalCredit += floatval($item['credit'] ?? 0);
            }

            if (abs($totalDebit-$totalCredit) > 0.00001) {
                $validator->errors()->add(
                    'details',
                    'Transaksi gagal: Total debet (Rp ' . number_format($totalDebit, 0, ',', '.') . ') dan Total Kredit (Rp ' . number_format($totalCredit, 0, ',', '.') . ') Tidak Balance!'
                );
            }

            if ($totalDebit <= 0) {
                $validator->errors()->add(
                    'details',
                    'Transaksi gagal: Total nominal transaksi jurnal harus lebih besar dari 0.'
                );
            }
        });
    }
}
