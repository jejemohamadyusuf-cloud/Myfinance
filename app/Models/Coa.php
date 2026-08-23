<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coa extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['code', 'name', 'type', 'is_cash_bank', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Coa::class, 'parent_id');
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
