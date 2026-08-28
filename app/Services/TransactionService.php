<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Traits\HasTransactionNumber;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    use HasTransactionNumber;

    public function getCashTransactions(int $perPage = 10, int $masterId = null): LengthAwarePaginator
    {
        $query = Transaction::whereIn('type', ['cash_in', 'cash_out', 'CASH_IN', 'CASH_OUT']);

        if ($masterId) {
            $query->where('owner_id', $masterId);
        }

        return $query
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getBankTransactions(int $perPage = 10, int $masterId = null): LengthAwarePaginator
    {
        $query = Transaction::whereIn('type', ['bank_in', 'bank_out', 'BANK_IN', 'BANK_OUT']);
        
        if ($masterId) {
            $query->where('owner_id', $masterId);
        }
        
        return $query
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getTransactionById(int $id, ?int $masterId = null): Transaction
    {
        $query = Transaction::with('details.coa');

        if ($masterId) {
            $query->where('owner_id', $masterId);
        }

        return $query->findOrFail($id);
    }

    public function getCoaOptionsForTransaction(string $category, ?int $masterId = null): array
    {
        $cashBankQuery = Coa::where('is_cash_bank', 1);
        $opponentQuery = Coa::query();

        if ($masterId) {
            $cashBankQuery->where('owner_id', $masterId);
            $opponentQuery->where('owner_id', $masterId);
        }

        if ($category === 'bank') {
            $cashBankCoas = (clone $cashBankQuery)->where('name', 'LIKE', '%bank%')->orderBy('code')->get();
        } else {
            $cashBankCoas = (clone $cashBankQuery)->where('name', 'NOT LIKE', '%bank%')->orderBy('code')->get();
        }

        $opponentCoas = $opponentQuery->orderBy('code')->get();

        return compact('cashBankCoas', 'opponentCoas');
    }

    public function getTransactionFormData(int $id, ?int $masterId = null): array
    {
        $transaction = $this->getTransactionById($id, $masterId);
        $type = strtolower($transaction->type);
        $isBank = in_array($type, ['bank_in', 'bank_out']);
        $category = $isBank ? 'bank' : 'cash';

        $coas = $this->getCoaOptionsForTransaction($category, $masterId);
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];

        $cashBankCoaId = null;
        $opponentCoaId = null;

        foreach ($transaction->details as $detail) {
            if ($detail->coa && (int) $detail->coa->is_cash_bank === 1) {
                $cashBankCoaId = $detail->coa_id;
            } else {
                $opponentCoaId =$detail->coa_id;
            }
        }

        if (!$cashBankCoaId || !$opponentCoaId) {
            $details = $transaction->details->values();
            if ($details->count() >= 2) {
                if (in_array($type, ['cash_in', 'bank_in'])) {
                    $cashBankDetail = $details->firstWhere('debit', '>', 0);
                    $opponentDetail = $details->firstWhere('credit', '>', 0);
                } else {
                    $cashBankDetail = $details->firstWhere('credit', '>', 0);
                    $opponentDetail = $details->firstWhere('debit', '>', 0);
                }
            }

            $cashBankCoaId = $cashBankDetail ? $cashBankDetail->coa_id : null;
            $opponentCoaId = $opponentDetail ? $opponentDetail->coa_id : null;
        }
        
        $amount = $transaction->total_amount;

        return compact('transaction', 'cashBankCoas', 'opponentCoas', 'cashBankCoaId', 'opponentCoaId', 'amount', 'category');
    }

    public function createTransaction(array $data, ?int $masterId = null): Transaction
    {
        return DB::transaction(function () use ($data, $masterId) {
            //generate nomor transaksi
            $transactionNumber = self::generateNumber($data['type'], $data['date']);
            
            //header
            $transaction = Transaction::create([
                'owner_id' => $masterId ?? auth()->user()->owner_id ?? auth()->id(),
                'transaction_number' => $transactionNumber,
                'status' => Transaction::STATUS_POSTED,
                'type' => strtolower($data['type']),
                'date' => $data['date'],    
                'description' => $data['description'] ?? null,
                'total_amount' => (float) $data['total_amount'],
            ]);

            //tentukan posisi debit kreditnya
            $details = $this->prepareJournalEntries($data);

            foreach ($details as $detail) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'coa_id' => $detail['coa_id'],
                    'description' => $detail['description'] ?? null,
                    'debit' => $detail['debit'],
                    'credit' => $detail['credit'],
                ]);
            }

            return $transaction;
        });
    }

    public function updateTransaction(int $id, array $data, ?int $masterId = null): Transaction
    {
        return DB::transaction(function () use ($id, $data, $masterId) {
            $transaction = $this->getTransactionById($id, $masterId);

            $transaction->update([
                'type' => strtolower($data['type']),
                'date' => $data['date'],
                'description' => $data['description'] ?? null,
                'total_amount' => (float) $data['total_amount']
            ]);

            $transaction->details()->delete();

            $details = $this->prepareJournalEntries($data);

            foreach ($details as $detail) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'coa_id' => $detail['coa_id'],
                    'description' => $detail['description'] ?? null,
                    'debit' => $detail['debit'],
                    'credit' => $detail['credit']
                ]);   
            }

            return $transaction;
        });
            
    }

    private function prepareJournalEntries(array $data): array
    {
        $amount = (float) $data['total_amount'];
        $cashBankId = $data['cash_bank_coa_id'] ?? $data['cash_bank_id'] ?? null;
        $opponentId = $data['opponent_coa_id'] ?? $data['opponent_id'] ?? null;
        $type = strtolower($data['type']);

        if ($cashBankId === null || $opponentId === null) {
            throw new Exception('Akun kas/bank atau akun lawan tidak valid.');
        }

        switch ($type) {
            case 'cash_in':
            case 'bank_in':
                return[
                    [
                        'coa_id' => $cashBankId,
                        'debit' => $amount,
                        'credit' => 0,
                    ],
                    [
                        'coa_id' => $opponentId,
                        'debit' => 0,
                        'credit' => $amount,
                    ],
                ];

            case 'cash_out':
            case 'bank_out':
                return [
                    [
                        'coa_id' => $opponentId,
                        'debit' => $amount,
                        'credit' => 0,
                    ],
                    [
                        'coa_id' => $cashBankId,
                        'debit' => 0,
                        'credit' => $amount,
                    ],
                ];
            
            default:
                throw new Exception("Tipe transaksi tidak valid");
        }
    }

    public function voidTransaction(int $id, ?int $masterId = null): Transaction
    {
        return DB::transaction(function () use ($id, $masterId) {
            $transaction = $this->getTransactionById($id, $masterId);
            $transaction->update(['status' => Transaction::STATUS_VOID]);
            return $transaction;
        });
    }

    public function getDailyTransactionTrends(?string $startDate = null, ?string $endDate = null, ?int $masterId = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subDays(6)->startOfDay();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $startDateStr = $start->format('Y-m-d 00:00:00');
        $endDateStr = $end->format('Y-m-d 23:59:59');

        $daysCount = $start->diffInDays($end) + 1;

        $internalTransferIds = DB::table('transaction_details')
                ->join('coas', 'transaction_details.coa_id', '=', 'coas.id')
                ->where('coas.is_cash_bank', 1)
                ->select('transaction_id')
                ->groupBy('transaction_id')
                ->havingRaw('COUNT(DISTINCT coas.id) >= ?', [2])
                ->pluck('transaction_id');
        //fetch income
        $incomesQuery = DB::table('transactions')
                ->whereNull('deleted_at')
                ->where('status', ['posted', 'POSTED'])
                ->whereIn('type', ['cash_in', 'bank_in', 'CASH_IN', 'BANK_IN'])
                ->whereBetween('date', [$startDateStr, $endDateStr]);
        
        if ($internalTransferIds->isNotEmpty()) {
            $incomesQuery->whereNotIn('id', $internalTransferIds);
        }

        if ($masterId) {
            $incomesQuery->where('tenant_id', $masterId);
        }

        $rawIncomes = $incomesQuery->select('date', 'total_amount')->get();        

        $incomes = [];
        foreach ($rawIncomes as $item) {
            $formattedKey = Carbon::parse($item->date)->format('Y-m-d');
            $incomes[$formattedKey] = ($incomes[$formattedKey] ?? 0) + (float) $item->total_amount;
        }
        //fetch expense
        $expensesQuery = DB::table('transactions')
            ->whereNull('deleted_at')
            ->where('status', ['posted','POSTED'])
            ->whereIn('type', ['cash_out', 'bank_out', 'CASH_OUT', 'BANK_OUT'])
            ->whereBetween('date', [$startDateStr, $endDateStr]);
        
        if ($internalTransferIds->isNotEmpty()) {
            $expensesQuery->whereNotIn('id', $internalTransferIds);
        }

        if ($masterId) {
            $expensesQuery->where('tenant_id', $masterId);
        }

        $rawExpenses = $expensesQuery->select('date', 'total_amount')->get();

        $expenses = [];
        foreach ($rawExpenses as $item) {
            $formattedKey = Carbon::parse($item->date)->format('Y-m-d');
            $expenses[$formattedKey] = ($expenses[$formattedKey] ?? 0) + (float) $item->total_amount;
        }

        $labels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i=0; $i < $daysCount; $i++) { 
            $currentDate = (clone $start)->addDays($i);
            $dateStr = $currentDate->format('Y-m-d');

            $labels[] = $currentDate->format('d M Y');
            $incomeData[] = $incomes[$dateStr] ?? 0;
            $expenseData[] = $expenses[$dateStr] ?? 0;
        }

        return [
            'labels' => $labels,
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }
}