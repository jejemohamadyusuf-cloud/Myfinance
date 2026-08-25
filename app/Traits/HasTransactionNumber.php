<?php

namespace App\Traits;
use App\Models\Transaction;
use Carbon\Carbon;

trait HasTransactionNumber
{
    public static function generateNumber(string $type, string $date): string
    {
        $prefixes = [
            'cash_in' => 'KM0',
            'cash_out' => 'KK0',
            'bank_in' => 'BM0',
            'bank_out' => 'BK0',
        ];

        $prefix = $prefixes[$type] ?? 'TR0';
        $carbonDate = Carbon::parse($date);
        $yearMonth = $carbonDate->format('ym');
        $searchPrefix = $prefix . $yearMonth;

        $tenantId = auth()->check() ? auth()->user()->getTenantId() : null;

        $lastTransaction = Transaction::where('tenant_id', $tenantId)->where('transaction_number', 'LIKE', $searchPrefix .'%')
        ->withTrashed()
        ->orderBy('transaction_number', 'desc')
        ->first();

        if (! $lastTransaction) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($lastTransaction->transaction_number, -5);
            $number = $lastNumber +1;
        }

        return $searchPrefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
