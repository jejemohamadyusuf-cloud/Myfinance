<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\View\View;
use App\Models\Coa;
use App\Models\TransasctionDetail;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function generalJournal(Request $request): View
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        
        $journal = $this->reportService->getGeneralJournal($startDate, $endDate);

        return view('reports.journal', compact('journal'));
    }

    public function generalLedger(Request $request): View
    {
        $coas = Coa::orderBy('code')->get();

        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));

        $ledger = null;

        if ($request->filled('coa_id')) {
            $request->validate([
                'coa_id' => 'exists:coas,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $ledger = $this->reportService->getGeneralLedger(
                (int) $request->coa_id,
                $startDate,
                $endDate
            );
        }

        return view('reports.ledger', compact('coas', 'ledger'));
    }
}
