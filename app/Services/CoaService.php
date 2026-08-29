<?php

namespace App\Services;

use App\Models\Coa;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CoaService
{
    private function getTenantId(): int
    {
        $user = auth()->user();
        return $user->isMaster() ? $user->id : $user->owner->id;
    }

    public function getAllCoas(int $perPage = 10): LengthAwarePaginator
    {
        return Coa::where('tenant_id', $this->getTenantId())
            ->orderBy('code', 'asc')->paginate($perPage);
    }

    public function getCoaOptions(): Collection
    {
        return Coa::where('tenant_id', $this->getTenantId())
            ->select('id', 'code', 'name', 'type')
            ->orderBy('code', 'asc')
            ->get();
    }

    public function getCoaById(int $id): Coa
    {
        return Coa::where('tenant_id', $this->getTenantId())->findOrFail($id);
    }

    public function createCoa(array $data): Coa
    {
        $data['is_cash_bank'] = isset($data['is_cash_bank']) ? (bool) $data['is_cash_bank']: false;
        
        return Coa::create([
            'tenant_id' => $this->getTenantId(),
            'code' => $data['code'],
            'name' => $data['name'],
            'type' => $data['type'],
            'is_cash_bank' => filter_var($data['is_cash_bank'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function updateCoa(int $id, array $data): Coa
    {
        $coa = $this->getCoaById($id);
        $data['is_cash_bank'] = filter_var($data['is_cash_bank'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $coa->update($data);
        return $coa;
    }

    public function deleteCoa(int $id,): bool
    {
        $coa = $this->getCoaById($id);
        $hasTransactions = $coa->transactionDetails?->exists();
        if ($hasTransactions) {
            throw new \Exception('Cannot delete COA because it has recorded transaction');
        }
    
        return $coa->delete();
    }

    public function getCashBalance(): float
    {
        $cashCoaIds = Coa::where('tenant_id', $this->getTenantId())
            ->where('is_cash_bank', 1)
            ->where(function ($query) {
                $query->where('name', 'LIKE' , '%kas%')
                      ->orWhere('name', 'LIKE', '%cash%')
                      ->orWhere('name', 'NOT LIKE', '%bank%');
            })->pluck('id');
        
            return $this->getCalculateBalanceByCoaIds($cashCoaIds);
    }

    public function getBankBalance(): float
    {
        $cashCoaIds = Coa::where('tenant_id', $this->getTenantId())
            ->where('is_cash_bank', 1)
            ->where(function ($query) {
                $query->where('name', 'LIKE', '%kas%')
                      ->orWhere('name', 'LIKE', '%cash%');
            })->pluck('id');

        $bankCoaIds = Coa::where('tenant_id', $this->getTenantId())->where('is_cash_bank', 1)->whereNotIn('id', $cashCoaIds)->pluck('id');
        
        return $this->getCalculateBalanceByCoaIds($bankCoaIds);
    }

    private function getCalculateBalanceByCoaIds($coaIds): float
    {
        if ($coaIds->isEmpty()) {
            return 0.0;
        }

        $totals = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereIn('transaction_details.coa_id', $coaIds)
            ->whereNull('transactions.deleted_at')
            ->whereIn('transactions.status', ['posted', 'POSTED'])
            ->selectRaw('SUM(transaction_details.debit) as total_debit,
                         SUM(transaction_details.credit) as total_credit')
            ->first();
        
        $debit = $totals->total_debit ?? 0;
        $credit = $totals->total_credit ?? 0;

        return (float) ($debit - $credit);
    }
}
