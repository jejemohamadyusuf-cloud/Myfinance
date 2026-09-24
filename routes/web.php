<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\CoaController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProfileController;

#home
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/logout', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
#dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
#cash-in
    Route::get('/cash-in', [TransactionController::class, 'cashInIndex'])->name('cash.in.index');
    Route::get('/cash-in/create', [TransactionController::class, 'cashInCreate'])->name('cash.in.create');
#bank-in
    Route::get('/bank-in', [TransactionController::class, 'bankInIndex'])->name('bank.in.index');
    Route::get('/bank-in/create', [TransactionController::class, 'bankInCreate'])->name('bank.in.create');
#cash-out
    Route::get('/cash-out', [TransactionController::class, 'cashOutIndex'])->name('cash.out.index');
    Route::get('/cash-out/create', [TransactionController::class, 'cashOutCreate'])->name('cash.out.create');
#bank-out
    Route::get('/bank-out', [TransactionController::class, 'bankOutIndex'])->name('bank.out.index');
    Route::get('/bank-out/create', [TransactionController::class, 'bankOutCreate'])->name('bank.out.create');

#transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('cash.in.index');
        });
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('update');
        Route::post('/{id}/void', [TransactionController::class, 'void'])->name('void');
    });

#reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/journal', [ReportController::class, 'generalJournal'])->name('journal');
        Route::get('/ledger', [ReportController::class, 'generalLedger'])->name('ledger');
    });

#coas
    Route::prefix('coas')->name('coas.')->group(function () {
        Route::get('/', [CoaController::class, 'index'])->name('index');
        Route::get('/create', [CoaController::class, 'create'])->name('create');
        Route::post('/', [CoaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CoaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CoaController::class, 'update'])->name('update');
        Route::delete('/{id}', [CoaController::class, 'destroy'])->name('destroy');
    });

#profiles
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::post('/staff', [ProfileController::class, 'storeStaff'])->name('store');
            Route::patch('{id}/toggle', [ProfileController::class, 'toggleStaff'])->name('toggle');
        });     
    });
});

require __DIR__.'/auth.php';


