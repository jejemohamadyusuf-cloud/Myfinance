<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalRequest;
use App\Models\Coa;
use App\Models\Journal;
use App\Services\JournalService;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    public function __construct(
        protected JournalService $journalService
    )
    {}

    public function index()
    {
        $journals = Journal::with(['details_coa'])
            ->where('user_id', Auth::id())
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(10);
        
            return view('reports.journal.index', compact('journals'));
    }

    public function create()
    {
        $coas = Coa::where('user_id', Auth::id())
            ->orderBy('code', 'asc')
            ->get();
        
        return view('reports.journal.create', compact('coas'));
    }

    public function store(StoreJournalRequest $request)
    {
        try {
            $journal = $this->journalService->createJournal(
                $request->validated(),Auth::id()
            );

            return redirect()->route('journals.index')->with('success', 'Jurnal Umum (' . $journal->journal_number . ') berhasil disimpan');
        
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
