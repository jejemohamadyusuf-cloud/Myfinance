<?php

namespace App\Services;

use App\Models\Coa;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CoaService
{
    public function getAllCoas(int $perPage = 10): LengthAwarePaginator
    {
        return Coa::orderBy('code', 'asc')->paginate($perPage);
    }

    public function getCoaOptions(): Collection
    {
        return Coa::select('id', 'code', 'name', 'type')->orderBy('code', 'asc')->get();
    }

    public function getCoaById(int $id): Coa
    {
        return Coa::findOrFail($id);
    }

    public function createCoa(array $data): Coa
    {
        $data['is_cash_bank'] = isset($data['is_cash_bank']) ? (bool) $data['is_cash_bank']: false;
        
        return Coa::create($data);
    }

    public function updateCoa(int $id, array $data): Coa
    {
        $coa = Coa::findOrFail($id);
        $data['is_cash_bank'] = isset($data['is_cash_bank']) ? (bool) $data['is_cash_bank']: false;

        $coa->update($data);
        return $coa;
    }

    public function deleteCoa(int $id,): bool
    {
        $coa = Coa::findOrFail($id);
        if ($coa->transactionDetails?->exists()) {
            throw new \Exception('Cannot delete COA because it has recorded transaction');
        }

        return $coa->delete();
    }

    public function getCashBalance(): float
    {
        $cashCoaIds = Coa::where('is_cash_bank', 1)->whereRaw('name NOT LIKE?' , '%bank%')->pluck('id');
        if ($cashCoaIds->isEmpty()) {
            return 0.0;
        }

        $totals = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereIn('transaction_details.coa_id', $cashCoaIds)
            ->whereNull('transactions.deleted_at')
            ->where('transactions.status', 'posted')
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $debit = $totals->total_debit ?? 0;
        $credit = $totals->total_credit ?? 0;

        return (float) ($debit-$credit);
    }

    public function getBankBalance(): float
    {
        $bankCoaIds = Coa::where('is_cash_bank', 1)->whereRaw('name LIKE?' , '%bank%')->pluck('id');
        if ($bankCoaIds->isEmpty()) {
            return 0.0;
        }

        $totals = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereIn('transaction_details.coa_id', $bankCoaIds)
            ->whereNull('transactions.deleted_at')
            ->where('transactions.status', 'posted')
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();
        
        $debit = $totals->total_debit ?? 0;
        $credit = $totals->total_credit ?? 0;

        return (float) ($debit-$credit);
    }

    private function calculateBalanceByCoaIds($coaIds): float
    {
        if ($coaIds->isEmpty()) {
            return 0.0;
        }

        $totals = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transaction_id')
            ->whereIn('transaction_details.coa_id', $coaIds)
            ->whereNull('transactions.deleted_at')
            ->where('transactions.status', 'posted')
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();
        
        $debit = $totals->total_debit ?? 0;
        $credit = $totals->total_credit ?? 0;

        return (float) ($debit - $credit);
    }
}
