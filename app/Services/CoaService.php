<?php

namespace App\Services;

use App\Models\Coa;
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
            'sub_category' => $data['sub_category'],
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
            ->where(function ($q) {
                $q->whereRaw("LOWER(sub_category) = 'cash'")
                ->orWhereNull('sub_category');
            })->pluck('id');
    
        return $this->getCalculateBalanceByCoaIds($cashCoaIds, 'all_time');
    }

    public function getBankBalance(): float
    {
        $bankCoaIds = Coa::where('tenant_id', $this->getTenantId())
            ->where('is_cash_bank', 1)
            ->where(function ($q) {
                $q->whereRaw("LOWER(sub_category) = 'bank'")
                ->orWhereRaw("LOWER(type) = 'bank'");
        })->pluck('id');
        
        return $this->getCalculateBalanceByCoaIds($bankCoaIds, 'all_time');
    }

    public function getExpenseBalance(string $period = 'all_time'): float
    {
        $expenseCoaIds = Coa::where('tenant_id', $this->getTenantId())
            ->where(function ($q) {
                $q->where('is_cash_bank', 0)->orWhereNull('is_cash_bank');
            })->pluck('id');

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

        $cacheKey = "coa_balance_{$this->getTenantId()}_{$period}_" .md5(json_encode($coaIds));

        return Cache::remember($cacheKey, 300, function () use ($coaIds, $period) {
            $queryTx = DB::table('transaction_details')
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->whereIn('transaction_details.coa_id', $coaIds)
                ->where('transactions.tenant_id', $this->getTenantId())
                ->whereNull('transactions.deleted_at')
                ->whereIn('transactions.status', ['posted', 'POSTED']);

            $queryJournal = DB::table('journal_details')
                ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
                ->whereIn('journal_details.coa_id', $coaIds)
                ->where('journals.user_id', $this->getTenantId());

            if ($period === 'this_month') {
                $start = now()->startOfMonth()->toDateString();
                $end = now()->endOfMonth()->toDateString();

                $queryTx->whereBetween('transactions.date', [$start, $end]);
                $queryJournal->whereBetween('journals.transaction_date', [$start, $end]);
            } elseif ($period === 'this_year') {
                $start = now()->startOfYear()->toDateString();
                $end = now()->endOfYear()->toDateString();

                $queryTx->whereBetween('transactions.date', [$start, $end]);
                $queryJournal->whereBetween('journals.transaction_date', [$start, $end]);
            }

            $txDebit = (float)$queryTx->sum('transaction_details.debit');
            $txCredit = (float)$queryTx->sum('transaction_details.credit');

            $jDebit = (float)$queryJournal->sum('journal_details.debit');
            $jCredit = (float)$queryJournal->sum('journal_details.credit');

            $totalDebit = $txDebit + $jDebit;
            $totalCredit = $txCredit + $jCredit;

            $isCashOrBank = Coa::whereIn('id', $coaIds)->where('is_cash_bank', 1)->exists();

            if ($isCashOrBank) {
                return $totalDebit - $totalCredit;
            }
            return $totalDebit;
        });  
    }

    public function clearBalanceCache(): void
    {
        Cache::flush();
    }
}
