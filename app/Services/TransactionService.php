<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Traits\HasTransactionNumber;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    use HasTransactionNumber;

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