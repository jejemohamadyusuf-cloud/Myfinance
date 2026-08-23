<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    //ambil data laporan jurnal umum berdasarkan rentang tanggal
    public function getGeneralJournal(string $startDate, string $endDate): Collection
    {
        return Transaction::with(['details.coa'])
            ->whereRaw('LOWER(status) = ?', [Transaction::STATUS_POSTED])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    //ambil data laporan buku besar per coa beserta saldo awal saldo akhir
    public function getGeneralLedger(int $coaId, string $startDate, string $endDate): array
    {   
        //mengitung saldo awal(mutasi sebelum $startDate)
        $coa = Coa::findOrFail($coaId);

        $previousMutations = TransactionDetail::where('coa_id', $coaId)
        ->whereHas('transaction', function ($query) use ($startDate) {
            $query->where('status', 'posted')
            ->where('date', '<', $startDate);
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
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'posted')
                    ->whereBetween('date', [$startDate, $endDate]);
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
                'description'    => $detail->transaction->description,
                'debit'    => $detail->debit,
                'credit'    => $detail->credit,
                'balance'    => $runningBalance,
            ];
        }

        return [
            'coa'           => $coa,
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'initial_balance'=> $initialBalance,
            'mutations'     => $mutations,
            'final_balance' => $runningBalance
        ];
    }
}