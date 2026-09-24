<?php

namespace App\Services;

use App\Models\Coa;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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

        Cache::forget("balances_{$this->getTenantId()}");
    }

    public function updateCoa(int $id, array $data): Coa
    {
        $coa = $this->getCoaById($id);
        $data['is_cash_bank'] = filter_var($data['is_cash_bank'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $coa->update($data);

        Cache::forget("balances_{$this->getTenantId()}");
        return $coa;
    }

    public function deleteCoa(int $id,): bool
    {
        $coa = $this->getCoaById($id);

        if (!$coa) {
            throw new \Exception('COA not found.');
        }
        
        if ($coa->transactionDetails()->exists()) {
            throw new \Exception('Cannot delete COA because it has recorded transaction.');
        }

        if ($coa->children()->exists()) {
            throw new \Exception('Cannot delete COA because it has sub-accounts.');
        }
    
        Cache::forget("balances_{$this->getTenantId()}");

        return $coa->delete();
    }

    public function getCashBalance(): float
    {
        $cashCoaIds = Coa::where('tenant_id', $this->getTenantId())
            ->where('is_cash_bank', 1)
            ->where('sub_category', 'cash')
            ->pluck('id');
        
            return $this->getCalculateBalanceByCoaIds($cashCoaIds);
    }

    public function getBankBalance(): float
    {
        $bankCoaIds = Coa::where('tenant_id', $this->getTenantId())
            ->where('is_cash_bank', 1)
            ->where('sub_category', 'bank')
            ->pluck('id');
        
        return $this->getCalculateBalanceByCoaIds($bankCoaIds);
    }

    public function getExpenseBalance(string $period = 'this_month'): float
    {
        $expenseCoaIds = Coa::where('tenant_id', $this->getTenantId())
            ->whereIn('type', ['expense', 'liability'])->pluck('id');

        if ($expenseCoaIds->isEmpty()) {
            return 0.0;
        }
        
        return $this->getCalculateBalanceByCoaIds($expenseCoaIds, $period);
    }

    private function getCalculateBalanceByCoaIds($coaIds, string $period = 'all_time'): float
    {
        if ($coaIds->isEmpty()) {
            return 0.0;
        }

        return Cache::remember("coa_balance_{$this->getTenantId()}_{$period}", 300, function () use ($coaIds, $period) {
            $query = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereIn('transaction_details.coa_id', $coaIds)
            ->where('transactions.tenant_id', $this->getTenantId())
            ->whereNull('transactions.deleted_at')
            ->whereIn('transactions.status', ['posted', 'POSTED']);

            if ($period === 'this_month') {
                $query->whereBetween('transactions.date', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($period === 'this_year') {
                $query->whereBetween('transactions.date', [now()->startOfYear(),  now()->endOfYear()]);
            }

            $totals = $query->selectRaw('
                COALESCE(SUM(transaction_details.debit),0) as total_debit,
                COALESCE(SUM(transaction_details.credit),0) as total_credit')->first();

            return (float) (($totals->total_debit ?? 0) - ($totals->total_credit ?? 0));
        });  
    }
}
