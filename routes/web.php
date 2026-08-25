<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\CoaController;
use App\Http\Controllers\Web\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/transaction/{id}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('update');
        Route::post('/transaction/{id}/void', [TransactionController::class, 'void'])->name('void');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('cash')->name('cash.')->group(function () {
        Route::get('/', [TransactionController::class, 'cashIndex'])->name('index');
        Route::get('/create', [TransactionController::class, 'cashCreate'])->name('create');
    });
});


Route::middleware(['auth'])->group(function () {
    Route::prefix('bank')->name('bank.')->group(function () {
        Route::get('/', [TransactionController::class, 'bankIndex'])->name('index');
        Route::get('/create', [TransactionController::class, 'bankCreate'])->name('create');
    });
});


Route::middleware(['auth'])->group(function () {
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/journal', [ReportController::class, 'generalJournal'])->name('journal');
        Route::get('/ledger', [ReportController::class, 'generalLedger'])->name('ledger');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('coas')->name('coas.')->group(function () {
        Route::get('/', [CoaController::class, 'index'])->name('index');
        Route::get('/create', [CoaController::class, 'create'])->name('create');
        Route::post('/', [CoaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CoaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CoaController::class, 'update'])->name('update');
        Route::delete('/{id}', [CoaController::class, 'destroy'])->name('destroy');
    });
});


require __DIR__.'/auth.php';


