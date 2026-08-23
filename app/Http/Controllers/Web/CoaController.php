<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCoaRequest;
use App\Http\Requests\UpdateCoaRequest;
use App\Services\CoaService;
use App\Models\Coa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Database\QueryExeption;
use Exception;

class CoaController extends Controller
{
    protected CoaService $coaService;

    public function __construct(CoaService $coaService)
    {
        $this->coaService = $coaService;
    }

    public function index(): View
    {
        $coas = $this->coaService->getAllCoas();
        
        return view('coas.index', compact('coas'));
    }

    public function create(): View
    {
        $coas = Coa::select('code', 'type')->get();
        return view('coas.create', compact('coas'));
    }

    public function store(StoreCoaRequest $request): RedirectResponse
    {
        $this->coaService->createCoa($request->validated());
        // dd($request->all());
        return redirect()->route('coas.index')->with('success', 'COA Account created successfully');
        
    }

    public function edit(int $id): View
    {
        $coa = Coa::firstOrFail($id);

        return view('coas.edit', compact('coa'));
    }

    public function update(UpdateCoaRequest $request, int $id): RedirectResponse
    {
        $this->CoaService->updateCoa($id, $request->validated());
        
        return redirect()->route('coas.index')->with('success', 'COA Account updated successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->coaService->deleteCoa($id);
            return redirect()->route('coas.index')
                ->with('success', 'COA deleted succesfully');
        } catch (QueryException $e) {
            return redirect()->route('coas.index')
                ->with('error', 'Cannot delete COA because it has recorded transaction');
        } catch (Exception $e) {
            return redirect()->route('coas.index')
                ->with('error', 'Cannot delete COA because it has recorded transaction');
        }   
    }
}
