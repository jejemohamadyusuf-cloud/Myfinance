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

#dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

#transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('cash.index');
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

#cash transactions
    Route::prefix('cash')->name('cash.')->group(function () {
        Route::get('/', [TransactionController::class, 'cashIndex'])->name('index');
        Route::get('/create', [TransactionController::class, 'cashCreate'])->name('create');
    });

#bank transactions
    Route::prefix('bank')->name('bank.')->group(function () {
        Route::get('/', [TransactionController::class, 'bankIndex'])->name('index');
        Route::get('/create', [TransactionController::class, 'bankCreate'])->name('create');
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


