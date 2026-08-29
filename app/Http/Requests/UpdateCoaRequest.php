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
        $user = auth()->user();
        $tenantId = $user->isMaster() ? $user->id : $user->owner_id;
        $coaId = $this->route('coa')?->id ?? $this->route('coa') ?? $this->id;

        return [
            'code' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('coas', 'code')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($coaId)
            ],
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:asset,liability,equity,revenue,expense',
            'is_cash_bank' => 'nullable|boolean'
        ];
    }
}