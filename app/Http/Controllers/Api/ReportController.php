<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function generalJournal(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $journals = $this->reportService->getGeneralJournal(
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'status' => 'success',
            'data' => $journals
        ]);
    }

    public function generalLedger(Request $request): JsonResponse
    {
        $request->validate([
            'coa_id' => 'required|exists:coas.id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $ledger = $this->reportService->getGeneralLedger(
            (int) $request->coa_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'status' => 'success',
            'data' => $ledger
        ]);
    }
}
