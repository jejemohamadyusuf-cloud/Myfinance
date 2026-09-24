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
        $transactions = $this->transactionService->getTransactionByType('cash_in', 10);
        $type = 'cash_in';

        return view('transactions.index', compact('transactions', 'type'));
    }

    public function cashInCreate(): View
    {
        $coas = $this->transactionService->getCoaOptionsForTransaction('cash_in');
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];
        $type = 'cash_in';

        return view('transactions.create', compact('cashBankCoas', 'opponentCoas', 'type'));
    }

    public function cashOutIndex(): View
    {
        $transactions = $this->transactionService->getTransactionByType('cash_out', 10);
        $type = 'cash_out';

        return view('transactions.index', compact('transactions', 'type'));
    }

    public function cashOutCreate(): View
    {
        $coas = $this->transactionService->getCoaOptionsForTransaction('cash_out');
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];
        $type = 'cash_out';

        return view('transactions.create', compact('cashBankCoas', 'opponentCoas', 'type'));
    }

    public function bankInIndex(): View
    {
        $transactions = $this->transactionService->getTransactionByType('bank_in', 10);
        $type = 'bank_in';
        
        return view('transactions.index', compact('transactions', 'type'));
    }

    public function bankInCreate(): View
    {
        $coas = $this->transactionService->getCoaOptionsForTransaction('bank_in');
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];
        $type = 'bank_in';

        return view('transactions.create', compact('cashBankCoas', 'opponentCoas', 'type'));
    }

    public function bankOutIndex(): View
    {
        $transactions = $this->transactionService->getTransactionByType('bank_out', 10);
        $type = 'bank_out';
        
        return view('transactions.index', compact('transactions', 'type'));
    }

    public function bankOutCreate(): View
    {
        $coas = $this->transactionService->getCoaOptionsForTransaction('bank_out');
        $cashBankCoas = $coas['cashBankCoas'];
        $opponentCoas = $coas['opponentCoas'];
        $type = 'bank_out';

        return view('transactions.create', compact('cashBankCoas', 'opponentCoas', 'type'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        try {
            $transaction = $this->transactionService->createTransaction($request->validated());
            
            // Hapus cache saldo dashboard setelah ada transaksi baru
            $tenantId = auth()->user()->isMaster() ? auth()->user()->id : auth()->user()->owner->id;
            \Illuminate\Support\Facades\Cache::forget("balances_{$tenantId}");

            $route = $this->resolveRedirectRoute($transaction->type);

            return redirect()->route($route)->with('success', 'Transaksi berhasil disimpan.');
        } catch (\Throwable $e) {
            // 🟢 Jika terjadi error di database / service, tampilkan pesan error aslinya ke layar
            return redirect()->back()
                ->withInput()
                ->withErrors(['database_error' => $e->getMessage()]);
        }
    }

    public function show(int $id): View
    {
        $transactions = $this->transactionService->getTransactionById($id);
        
        $backRoute = route($this->resolveRedirectRoute($transactions->type));

        return view('transactions.view', compact('transactions', 'backRoute'));
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
