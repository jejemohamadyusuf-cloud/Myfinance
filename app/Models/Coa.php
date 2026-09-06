<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = ['tenant_id', 'code', 'name', 'type', 'is_cash_bank', 'parent_id'];
    protected $casts = [
        'is_cash_bank' => 'boolean'
    ];

    public static function booted(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                if (!$user->isSuperAdmin()) {
                    $builder->where('tenant_id', $user->getTenantId());
                }
            }
        });

        static::creating(function (Coa $coa) {
            if (auth()->check()) {
                $coa->tenant_id = $coa->tenant_id ?? auth()->user()->getTenantId();
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Coa::class, 'parent_id');
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'coa_id');
    }

    public function scopeCashOnly(Builder $query): Builder
    {
        return $query->where('is_cash_bank', 1)
                     ->where('name', 'LIKE', '%kas%'); 
    }

    public function scopeBankOnly(Builder $query): Builder
    {
        return $query->where('is_cash_bank', 1)
                     ->where('name', 'LIKE', '%bank%'); 
    }
}
