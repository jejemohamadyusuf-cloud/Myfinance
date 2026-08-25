<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Traits\HasTransactionNumber;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    use HasTransactionNumber;

    public function getCashTransaction(int $perPage = 10): LengthAwarePaginator
    {
        return Transaction::whereIn('type', ['cash_in', 'cash_out', 'CASH_IN', 'CASH_OUT'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getBankTransactions(int $perpage = 10): LengthAwarePaginator
    {
        return Transaction::whereIn('type', ['bank_in', 'bank_out', 'BANK_IN', 'BANK_OUT'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getTransactionById(int $id): Transaction
    {
        return Transaction::with('details.coa')->findOrFail($id);
    }

    public function getCoaOptionsForTransaction(string $category): array
    {
        if ($category === 'bank') {
            $cashBankCoas = Coa::where('is_cash_bank', 1)->where('name', 'LIKE', '%bank%')->orderBy('code')->get();
        } else {
            $cashBankCoas = Coa::where('is_cash_bank', 1)->where('name', 'NOT LIKE', '%bank%')->orderBy('code')->get();
        }

        $opponentCoas = Coa::orderBy('code')->get();

        return compact('cashBankCoas', 'opponentCoas');
    }

    public function getTransactionFormData(int $id): array
    {
        $transaction = $this->getTransactionById($id);
        $type = strtolower($transaction->type);
        $isBank = in_array($type, ['bank_in', 'bank_out']);
        $category = $isBank ? 'bank' : 'cash';

        $coas = $this->getCoaOptionsForTransaction($category);
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
                    $cashBankCoaId = optional($details->firstWhere('debit', '>', 0));
                    $opponentCoaId = optional($details->firstWhere('credit', '>', 0));
                } else {
                    $cashBankCoaId = optional($details->firstWhere('credit', '>', 0));
                    $opponentCoaId = optional($details->firstWhere('debit', '>', 0));
                }
            }
        }
        
        $amount = $transaction->total_amount;

        return compact('transaction', 'cashBankCoas', 'opponentCoas', 'cashBankCoaId', 'opponentCoaId', 'amount', 'category');
    }

    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            //generate nomor transaksi
            $transactionNumber = self::generateNumber($data['type'], $data['date']);
            
            //header
            $transaction = Transaction::create([
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

    public function updateTransaction(int $id, array $data): Transaction
    {
        return DB::transaction(function () use ($id, $data) {
            $transaction = Transaction::findOrFail($id);

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

    public function voidTransaction(int $id): Transaction
    {
        return DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);
            $transaction->update(['status' => Transaction::STATUS_VOID]);
            return $transaction;
        });
    }
}