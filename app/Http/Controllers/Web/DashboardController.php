<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CoaService;
use App\Services\TransactionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected CoaService $coaService;
    protected TransactionService $transactionService;

    public function __construct(CoaService $coaService, TransactionService $transactionService)
    {
        $this->coaService = $coaService;
        $this->transactionService = $transactionService;
    }

    public function index(): View
    {
        $cashBalance = $this->coaService->getCashBalance();
        $bankBalance = $this->coaService->getBankBalance();
        $totalBalance = $cashBalance + $bankBalance;

        $trends = $this->transactionService->getDailyTransactionTrends(7);

        return view('dashboard', compact('cashBalance', 'bankBalance', 'totalBalance', 'trends'));
    }
}
