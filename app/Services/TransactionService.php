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

    private function resolveTenantId(?int $masterId = null): int
    {
        if($masterId) {
            return $masterId;
        }

        $user = auth()->user();
        if (! $user) {
            throw new Exception('User must be authenticated');
        }

        return $user->getTenantId() ?? $user->id;
    }

    public function getTransactionByType(string $type, int $perPage = 10, ?int $masterId = null):LengthAwarePaginator
    {
        $tenantId = $this->resolveTenantId($masterId);

        return Transaction::where('tenant_id', $tenantId)
            ->where('type', strtolower($type))
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getCashTransactions(int $perPage = 10, int $masterId = null): LengthAwarePaginator
    {
        $tenantId = $this->resolveTenantId($masterId);

        return Transaction::where('tenant_id', $tenantId)
            ->whereIn('type', ['cash_in', 'cash_out', 'CASH_IN', 'CASH_OUT'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getBankTransactions(int $perPage = 10, int $masterId = null): LengthAwarePaginator
    {
        $tenantId = $this->resolveTenantId($masterId);

        return Transaction::where('tenant_id', $tenantId)
            ->whereIn('type', ['bank_in', 'bank_out', 'BANK_IN', 'BANK_OUT'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getTransactionById(int $id, ?int $masterId = null): Transaction
    {
        $tenantId = $this->resolveTenantId($masterId);

        return Transaction::with('details.coa')
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);
    }

    public function getCoaOptionsForTransaction(string $type, ?int $masterId = null): array
    {
        $tenantId = $this->resolveTenantId($masterId);
        $isBank = str_contains(strtolower($type), 'bank');
        $targetCategory = $isBank ? 'bank' : 'cash';

        $cashBankCoas = Coa::where('tenant_id', $tenantId)
            ->where('is_cash_bank', 1)
            ->where('sub_category', $targetCategory)
            ->orderBy('code')
            ->get();
        
        $opponentCoas = Coa::where('tenant_id', $tenantId)
            ->where('is_cash_bank', 0)
            ->orderBy('code')
            ->get();

        return compact('cashBankCoas', 'opponentCoas');
    }

    public function getTransactionFormData(int $id, ?int $masterId = null): array
    {
        $transaction = $this->getTransactionById($id, $masterId);
        $type = strtolower($transaction->type);
        $isBank = in_array($type, ['bank_in', 'bank_out']);
        $category = $isBank ? 'bank' : 'cash';

        $coas = $this->getCoaOptionsForTransaction($type, $masterId);
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
            $tenantId = $this->resolveTenantId($masterId);
            //generate nomor transaksi
            $transactionNumber = self::generateNumber($data['type'], $data['date']);
            
            //header
            $transaction = Transaction::create([
                'tenant_id' => $tenantId,
                'created_by' => auth()->id(),
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
        $cashBankId = $data['cash_bank_id'] ?? $data['cash_bank_coa_id'] ?? null;
        $opponentId = $data['opponent_id'] ?? $data['opponent_coa_id'] ?? null;
        $type = strtolower($data['type']);

        if (empty($cashBankId) || empty($opponentId)) {
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
        $tenantId = $this->resolveTenantId($masterId);

        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subDays(6)->startOfDay();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $startDateStr = $start->format('Y-m-d 00:00:00');
        $endDateStr = $end->format('Y-m-d 23:59:59');

        $daysCount = $start->diffInDays($end) + 1;

        $internalTransferIds = DB::table('transaction_details')
            ->join('coas', 'transaction_details.coa_id', '=', 'coas.id')
            ->where('coas.is_cash_bank', 1)
            ->where('coas.tenant_id', $tenantId)
            ->select('transaction_id')
            ->groupBy('transaction_id')
            ->havingRaw('COUNT(DISTINCT coas.id) >= ?', [2])
            ->pluck('transaction_id');
        //fetch income
        $incomesQuery = DB::table('transactions')
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['posted', 'POSTED'])
            ->whereIn('type', ['cash_in', 'bank_in', 'CASH_IN', 'BANK_IN'])
            ->whereBetween('date', [$startDateStr, $endDateStr]);
        
        if ($internalTransferIds->isNotEmpty()) {
            $incomesQuery->whereNotIn('id', $internalTransferIds);
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
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['posted','POSTED'])
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