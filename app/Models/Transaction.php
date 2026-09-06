<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use SoftDeletes, HasFactory;

    public const STATUS_POSTED = 'posted';
    public const STATUS_VOID = 'void';

    protected $fillable = [
        'tenant_id',
        'created_by',
        'transaction_number',
        'type',
        'date',
        'description',
        'total_amount',
        'status'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function booted(): void
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check()) {
                $user = auth()->user();
                if (! $user->isSuperAdmin()) {
                    $builder->where('tenant_id', $user->getTenantId());
                }
            }
        });

        static::creating(function (Transaction $transaction) {
            if (auth()->check()) {
                $user = auth()->user();
                $transaction->tenant_id = $transaction->tenant_id ?? $user->getTenantId();
                $transaction->created_by = $transaction->created_by ?? $user->id;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
