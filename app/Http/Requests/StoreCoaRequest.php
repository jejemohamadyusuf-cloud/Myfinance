<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = auth()->user();
        $tenantId = $user->isMaster() ? $user->id : $user->owner_id;

        return [
            'code'         => [
                'required',
                'string',
                'max:20',
                Rule::unique('coas', 'code',)->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })
            ],
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:asset,liability,equity,revenue,expense',
            'is_cash_bank' => 'boolean',
        ];
    }
}