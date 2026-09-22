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

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $cacheKey = "dashboard_data{$masterId}_" . ($startDate ?? 'default') . '_' . ($endDate ?? 'default');

        $data = Cache::remember($cacheKey, 60, function () use ($masterId, $startDate, $endDate) {
            $cashBalance = $this->coaService->getCashBalance($masterId);
            $bankBalance = $this->coaService->getBankBalance($masterId);
            $expenseBalance = $this->coaService->getExpenseBalance('this_month');
            $expenseBalanceAllTime = $this->coaService->getExpenseBalance('all_time');
            $totalBalance = $cashBalance + $bankBalance;
            $trends = $this->transactionService->getDailyTransactionTrends($startDate, $endDate, $masterId);
        });

        return compact('cashBalance', 'bankBalance', 'totalBalance', 'trends', 'expenseBalance');
        return view('dashboard', $data);
    }


}
