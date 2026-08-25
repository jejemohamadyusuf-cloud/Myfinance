<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $coaId = $this->route('id') ?? $this->route('coa');
        $tenantId = auth()->check() ? auth()->user()->getTenantId() : null;

        return [
            'code' => [
                'required', 
                'string', 
                'max:20', 
                Rule::unique('coas', 'code')
                    ->ignore($coaId)
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId))
            ],
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:asset,liability,equity,revenue,expense',
            'is_cash_bank' => 'nullable|boolean',
        ];
    }
}