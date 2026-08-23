<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'         => 'required|string|max:20|unique:coas,code',
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:asset,liability,equity,revenue,expense',
            'is_cash_bank' => 'nullable|boolean',
        ];
    }
}