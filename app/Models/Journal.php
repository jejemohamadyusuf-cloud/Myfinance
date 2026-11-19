<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'journal_number',
        'transaction_date',
        'description',
        'source_type',
        'source_id',
        'total_debit',
        'total_credit',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('User::class');
    }

    public function details(): HasMany
    {
        return $this->hasMany(JournalDetail::class);
    }
}
