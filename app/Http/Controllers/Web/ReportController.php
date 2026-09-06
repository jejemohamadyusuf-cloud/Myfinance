<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\View\View;
use App\Models\Coa;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function generalJournal(Request $request): View
    {
        $startDate = $request->input('start_date') ?: date('Y-m-d');
        $endDate = $request->input('end_date') ?: date('Y-m-d');
        
        $journal = $this->reportService->getGeneralJournal($startDate, $endDate);

        return view('reports.journal', compact('journal', 'startDate', 'endDate'));
    }

    public function generalLedger(Request $request): View
    {
        $tenantId = auth()->user()->getTenantId() ?? auth()->id();
        $coas = Coa::where('tenant_id', $tenantId)->orderBy('code')->get();

        if ($request->has('start_date')) {
            $startDate = $request->input('start_date') ?: date('Y-m-d');
            $endDate = $request->input('end_date') ?: date('Y-m-d');
        } else {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }
        
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

        return view('reports.ledger', compact('coas', 'ledger', 'startDate', 'endDate'));
    }
}
