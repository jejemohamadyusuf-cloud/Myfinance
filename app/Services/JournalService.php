<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\JournalDetail;
use App\Traits\HasTransactionNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\Concerns\Has;

class JournalService
{
    use HasTransactionNumber;

    public function createJournal(array $data, int $userId): Journal
    {
        return DB::transaction(function () use ($data, $userId) {
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($data['details'] as $item) {
                $totalDebit += floatval($item['debit'] ?? 0);
                $totalCredit += floatval($item['credit'] ?? 0);
            }

            $journalNumber = self::generateNumber('general', $data['transaction_date']);

            $journal = Journal::create([
                'user_id' => $userId,
                'journal_number' => $journalNumber,
                'transaction_date' => $data['transaction_date'],
                'description' => $data['description'],
                'source_type' => 'general',
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,                
            ]);

            foreach ($data['details'] as $item) {
                $debit = floatval($item['debit'] ?? 0);
                $credit = floatval($item['credit'] ?? 0);

                if ($debit > 0 || $credit > 0) {
                    JournalDetail::create([
                        'journal_id' => $journal->id,
                        'coa_id' => $item['coa_id'],
                        'debit' => $debit,
                        'credit' => $credit,
                        'memo' => $item['memo'],
                    ]);
                }
            }

            return $journal;
        });
    }
}



