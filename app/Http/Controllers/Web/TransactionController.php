<?php


namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Services\TransactionService;
use App\Models\Transaction;
use App\Models\Coa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function cashIndex(): View
    {
        $transactions = Transaction::whereIn('type',['cash_in', 'cash_out', 'CASH_IN', 'CASH_OUT'])->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        $category = 'cash';
        return view('cash.index', compact('transactions', 'category'));
    }

    public function cashCreate(): View
    {
        $cashBankCoas = Coa::where('is_cash_bank', 1)->where('name', 'NOT LIKE', '%bank%')->orderBy('code')->get();
        $opponentCoas = Coa::orderBy('code')->get();
        $category = 'cash';

        return view('cash.create', compact('cashBankCoas', 'opponentCoas', 'category'));
    }

    public function bankIndex(): View
    {
        $transactions = Transaction::whereIn('type',['bank_in', 'bank_out', 'BANK_IN', 'BANK_OUT'])->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        $category = 'bank';
        return view('bank.index', compact('transactions', 'category'));
    }

    public function bankCreate(): View
    {
        $cashBankCoas = Coa::where('is_cash_bank', 1)->where('name', 'LIKE', '%bank%')->orderBy('code')->get();
        $opponentCoas = Coa::orderBy('code')->get();
        $category = 'bank';

        return view('bank.create', compact('cashBankCoas', 'opponentCoas', 'category'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $transaction = $this->transactionService->createTransaction($request->validated());

        $isBank = in_array(strtolower($transaction->type), ['bank_in', 'bank_out']);
        $route = $isBank ? 'bank.index' : 'cash.index';

        return redirect()->route($route)->with('success', 'Transaction created successfully.');
    }

    public function show(int $id): View
    {
        $transactions = Transaction::with('details.coa')->findOrFail($id);

        $isBank = in_array(strtolower($transactions->type), ['bank_in', 'bank_out']);
        $route = $isBank ? 'bank.index' : 'cash.index';

        return view('transactions.view', compact('transactions', 'route'));
    }

    public function edit(int $id): View|RedirectResponse
    {
        $transaction = Transaction::with('details.coa')->findOrFail($id);

        if ($transaction->status === 'void') {
            return redirect()->route('transactions.show', $transaction->id);
        }

        $type = strtolower($transaction->type);
        $isBank = in_array(strtolower($transaction->type), ['bank_in', 'bank_out']);

        $category = $isBank ? 'bank' : 'cash';

        if ($isBank) {
            $cashBankCoas = Coa::where('is_cash_bank', 1)->where('name', 'LIKE', '%bank%')->orderBy('code')->get();
        } else {
            $cashBankCoas = Coa::where('is_cash_bank', 1)->where('name', 'NOT LIKE', '%bank%')->orderBy('code')->get();
        }
        
        $opponentCoas = Coa::orderBy('code')->get();

        $cashBankCoaId = null;
        $opponentCoaId = null;

        foreach ($transaction->details as $detail) {
            if ($detail->coa && (int) $detail->coa->is_cash_bank === 1) {
                $cashBankCoaId = $detail->coa_id;
            } else {
                $opponentCoaId = $detail->coa_id;
            }
        }

        if (!$cashBankCoaId || $opponentCoaId) {
            $details = $transaction->details->values();
            if ($details->count() >= 2) {
                if (in_array($type, ['cash_in', 'bank_in'])) {
                    $cashBankCoaId = optional($details->firstWhere('debit', '>', 0))->coa_id;
                    $opponentCoaId = optional($details->firstWhere('credit', '>', 0))->coa_id;
                } else {
                    $cashBankCoaId = optional($details->firstWhere('credit', '>', 0))->coa_id;
                    $opponentCoaId = optional($details->firstWhere('debit', '>', 0))->coa_id;
                }
            }
        }

        $amount = $transaction->total_amount;
    
        return view('transactions.edit', compact(
             'transaction',
             'cashBankCoas',
             'opponentCoas',
             'cashBankCoaId',
             'opponentCoaId',
             'amount' ,
             'category'
            ));
    }

    public function update(updateTransactionRequest $request, int $id): RedirectResponse
    {
        $transaction = $this->transactionService->updateTransaction($id, $request->validated());

        if ($transaction->status === 'void') {
            return redirect()->back()->with('error', 'The transaction was voided and cannot be updated');
        }
        
        $isBank = in_array(strtolower($transaction->type), ['bank_in', 'bank_out']);
        $route = $isBank ? 'bank.index' : 'cash.index';

        return redirect()->route($route)
        ->with('success', 'Transaction updated sucessfully!');
    }

    public function void(int $id): RedirectResponse
    {
        $transaction = $this->transactionService->voidTransaction($id);

        $isBank = in_array(strtolower($transaction->type), ['bank_in', 'bank_out']);
        $route = $isBank ? 'bank.index' : 'cash.index';

        return redirect()->route($route)->with('success', 'Transaction '. $transaction->transaction_number . 'has Voided');
    }
}
