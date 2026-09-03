<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CoaService;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
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

    public function index(Request $request): View
    {
        $user = auth()->user();
        $masterId = $user->isMaster() ? $user->id : $user->owner_id;

        $cashBalance = $this->coaService->getCashBalance($masterId);
        $bankBalance = $this->coaService->getBankBalance($masterId);
        $expenseBalance = $this->coaService->getExpenseBalance('this_month');
        $expenseBalanceAllTime = $this->coaService->getExpenseBalance('all_time');
        $totalBalance = $cashBalance + $bankBalance;

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $trends = $this->transactionService->getDailyTransactionTrends($startDate, $endDate, $masterId);

        return view('dashboard', compact('cashBalance', 'bankBalance', 'totalBalance', 'trends', 'expenseBalance'));
    }


}
