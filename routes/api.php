<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController as ApiTransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    //end point untuk transaksi kas bank masuk/keluar
    Route::get('/transactions', [ApiTransactionController::class, 'index']);
    Route::post('/transactions', [ApiTransactionController::class, 'store']);
    Route::get('/transactions{id}', [ApiTransactionController::class, 'show']);
});

