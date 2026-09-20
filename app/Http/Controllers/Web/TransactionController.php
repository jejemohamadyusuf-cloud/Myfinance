<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function cashInIndex(): View
    {
        $transactions = $this->transactionService->getTransactionsByType('cash_in', 10);
        $type = 'cash_in';
        $title = 'Cash In';

        return view('cash.index', compact('transactions', 'type', 'title'));
    }

    public function cashInCreate(): View
    {
        $coas = $this->transactionService->getCoaOptionsForTransaction('cash_in');
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];
        $type = 'cash_in';
        $title = 'Tambah Cash In';

        return view('cash.create', compact('cashBankCoas', 'opponentCoas', 'type', 'title'));
    }

    public function cashOutIndex(): View
    {
        $transactions = $this->transactionService->getTransactionsByType('cash_out', 10);
        $type = 'cash_out';
        $title = 'Cash Out';

        return view('cash.index', compact('transactions', 'type', 'title'));
    }

    public function cashOutCreate(): View
    {
        $coas = $this->transactionService->getCoaOptionsForTransaction('cash_out');
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];
        $type = 'cash_out';
        $title = 'Tambah Cash Out';

        return view('cash.create', compact('cashBankCoas', 'opponentCoas', 'type', 'title'));
    }

    public function bankInIndex(): View
    {
        $transactions = $this->transactionService->getTransactionsByType('bank_in', 10);
        $type = 'bank_in';
        $title = 'Bank In';
        
        return view('bank.index', compact('transactions', 'type', 'title'));
    }

    public function bankInCreate(): View
    {
        $coas = $this->transactionService->getCoaOptionsForTransaction('bank_in');
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];
        $type = 'bank_in';
        $title = 'Tambah Bank In';

        return view('bank.create', compact('cashBankCoas', 'opponentCoas', 'type', 'title'));
    }

    public function bankOutIndex(): View
    {
        $transactions = $this->transactionService->getTransactionsByType('bank_out', 10);
        $type = 'bank_out';
        $title = 'Bank Out';
        
        return view('bank.index', compact('transactions', 'type', 'title'));
    }

    public function bankOutCreate(): View
    {
        $coas = $this->transactionService->getCoaOptionsForTransaction('bank_out');
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];
        $type = 'bank_out';
        $title = 'Tambah Bank Out';

        return view('bank.create', compact('cashBankCoas', 'opponentCoas', 'type', 'title'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $transaction = $this->transactionService->createTransaction($request->validated());

        $route = $this->resolveRedirectRoute($transaction->type);

        return redirect()->route($route)->with('success', 'Transaction created successfully.');
    }

    public function show(int $id): View
    {
        $transactions = $this->transactionService->getTransactionById($id);
        $route = $this->resolveRedirectRoute($transaction->type);

        return view('transactions.view', compact('transactions', 'route'));
    }

    public function edit(int $id): View|RedirectResponse
    {
        $formData = $this->transactionService->getTransactionFormData($id);
        
        if ($formData['transaction']->status === 'void') {
            return redirect()->route('transactions.show', $formData['transaction']->id);
        }

        return view('transactions.edit', $formData);
    }

    public function update(updateTransactionRequest $request, int $id): RedirectResponse
    {
        $transaction = $this->transactionService->updateTransaction($id, $request->validated());

        if ($transaction->status === 'void') {
            return redirect()->back()->with('error', 'The transaction was voided and cannot be updated');
        }
        
        $route = $this->resolveRedirectRoute($transaction->type);

        return redirect()->route($route)
        ->with('success', 'Transaction updated sucessfully!');
    }

    public function void(int $id): RedirectResponse
    {
        $transaction = $this->transactionService->voidTransaction($id);
        $route = $this->resolveRedirectRoute($transaction->type);

        return redirect()->route($route)->with('success', 'Transaction '. $transaction->transaction_number . 'has been Voided');
    }

    private function resolveRedirectRoute(string $type):string
    {
        return match (strtolower($type)) {
            'cash_in' => 'cash.in.index',
            'cash_out' => 'cash.out.index',
            'bank_in' => 'bank.in.index',
            'bank_out' => 'bank.out.index',
            default => 'cash.in.index'
        };
    }
}
