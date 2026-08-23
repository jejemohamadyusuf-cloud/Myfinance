<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $coaId = $this->route('id') ?? $this->route('coa');

        return [
            'code'         => 'required|string|max:20|unique:coas,code,' . $coaId,
            'name'         => 'required|string|max:255',
            'type'         => 'required',
            'is_cash_bank' => 'nullable|boolean',
        ];
    }
}