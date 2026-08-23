<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Services\TransactionService;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(): JsonResponse
    {
        $transactions = Transaction::with('details.coa')->latest('date')->get();

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ], 200);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->createTransaction($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'transaksi berhasil jurnal terbuat otomatis',
            'data' => $transaction->load('details.coa'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $transaction = Transaction::with('details.coa')->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $transaction->load('details.coa'),
        ], 200);
    }
}
