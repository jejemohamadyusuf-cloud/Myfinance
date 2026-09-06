<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Exception;

class ReportService
{
    private function resolveTenantId(?int $masterId = null):int
    {
        if ($masterId) {
            return $masterId;
        }

        $user = auth()->user();
        if (! $user) {
            throw new Exception('User must be authenticated');
        }

        return $user->getTenantId() ?? $user->id;
    }

    //ambil data laporan jurnal umum berdasarkan rentang tanggal
    public function getGeneralJournal(?string $startDate = null, ?string $endDate = null, ?int $masterId = null): Collection
    {
        $tenantId = $this->resolveTenantId($masterId);

        $startStr = $startDate ? Carbon::parse($startDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $endStr = $endDate ? Carbon::parse($endDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        return Transaction::with(['details.coa'])
            ->where('tenant_id', $tenantId)
            ->whereRaw('LOWER(status) = ?', [Transaction::STATUS_POSTED])
            ->whereBetween('date', [$startStr, $endStr])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    //ambil data laporan buku besar per coa beserta saldo awal saldo akhir
    public function getGeneralLedger(int $coaId, ?string $startDate = null, ?string $endDate = null, ?int $masterId = null): array
    {   
        $tenantId = $this->resolveTenantId($masterId);

        $startStr = $startDate ? Carbon::parse($startDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $endStr = $endDate ? Carbon::parse($endDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        //mengitung saldo awal(mutasi sebelum $startDate)
        $coa = Coa::where('tenant_id', $tenantId)->findOrFail($coaId);

        $previousMutations = TransactionDetail::where('coa_id', $coaId)
        ->whereHas('transaction', function ($query) use ($startStr, $tenantId) {
            $query->where('tenant_id', $tenantId)->where('status', 'posted')
            ->where('date', '<', $startStr);
        })
        ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
        ->first();
        
        $prevDebit = (float) $previousMutations->total_debit ?? 0;
        $prevCredit = (float) $previousMutations->total_credit ?? 0;

        //saldo normal: asset & expanse = debit(+), liability, equity, revenue = credit(+)
        $isDdebitNormal = in_array(strtolower($coa->type), ['asset', 'expense']);
        $initialBalance = $isDdebitNormal ? ($prevDebit - $prevCredit) : ($prevCredit - $prevDebit);

        //ambil mutasi transaksi pada periode ini
        $details = TransactionDetail::with('transaction')
            ->where('coa_id', $coaId)
            ->whereHas('transaction', function ($query) use ($startStr, $endStr, $tenantId) {
                $query->where('tenant_id', $tenantId)->where('status', 'posted')
                    ->whereBetween('date', [$startStr, $endStr]);
            })
            ->get()
            ->sortBy([
                fn($a, $b) => strcmp($a->transaction->date, $b->transaction->date),
                fn($a, $b) => $a->transaction->id <=> $b->transaction->id,
            ]);
        
        $runningBalance = $initialBalance;
        $mutations = [];

        foreach ($details as $detail) {
            $debit = (float) $detail->debit;
            $credit = (float) $detail->credit;
            
            if ($isDdebitNormal) {
                $runningBalance += ($detail->debit - $detail->credit);
            } else {
                $runningBalance +=($detail->credit - $detail->debit);
            }

            $mutations[] = [
                'date'                  => $detail->transaction->date,
                'transaction_number'    => $detail->transaction->transaction_number,
                'description'           => $detail->transaction->description,
                'debit'                 => $detail->debit,
                'credit'                => $detail->credit,
                'balance'               => $runningBalance,
            ];
        }

        return [
            'coa'           => $coa,
            'start_date'    => $startStr,
            'end_date'      => $endStr,
            'initial_balance'=> $initialBalance,
            'mutations'     => $mutations,
            'final_balance' => $runningBalance
        ];
    }
}