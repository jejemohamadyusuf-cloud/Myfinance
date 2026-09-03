<?php

use App\Models\Coa;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    #setup tenant user
    $this->tenant = User::factory()->create();
    $this->actingAs($this->tenant);

    #setup coa kas dan beban
    $this->cashCoa = Coa::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Kas utama',
        'code' => '101',
        'type' => 'asset',
        'is_cash_bank' => true,
    ]);

    $this->expenseCoa = Coa::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Beban Operasional',
        'code' => '501',
        'type' => 'expense',
        'is_cash_bank' => false,
    ]);

    $this->dashboardService = new DashboardService();
});

test('dapat menghitung saldo kas dengan benar (Debit - Credit)', function () {
    $trxIn = Transaction::factory()->create([
        'tenant_id' => $this->tenant->id,
        'type' => 'cash_in',
        'status' => 'posted',
        'date' => now()->format('Y-m-d')
    ]);
    TransactionDetail::factory()->create([
        'transaction_id' => $trxIn->id,
        'coa_id' => $this->cashCoa->id,
        'debit' => 1000000,
        'credit' => 0

    ]);
    $trxOut = Transaction::factory()->create([
        'tenant_id' => $this->tenant->id,
        'type' => 'cash_out',
        'status' => 'posted'
    ]);
    TransactionDetail::factory()->create([
        'transaction_id' => $trxOut->id,
        'coa_id' => $this->cashCoa->id,
        'debit' => 0,
        'credit' => 300000
    ]);
    #ekspektasi 1,000,000 - 300,000
    $balance = $this->dashboardService->getCashBalance();
    expect($balance)->toBeFloat()->toBe(700000.0);
});

test('mengabaikan transaksi yang bersifat draft atau terhapus (soft delete)', function () {
    $draftTrx = Transaction::factory()->create([
        'tenant_id' => $this->tenant->id,
        'type' => 'cash_in',
        'status' => 'void',
        'date' => now()->format('Y-m-d')
    ]);
    TransactionDetail::factory()->create([
        'transaction_id' => $draftTrx->id,
        'coa_id' => $this->cashCoa->id,
        'debit' => 500000,
        'credit' => 0
    ]);

    $balance = $this->dashboardService->getCashBalance();
    expect($balance)->toBe(0.0);
});

test('dapat menghitung total beban pengeluaran bulan ini', function () {
    $trxExpense = Transaction::factory()->create([
        'tenant_id' => $this->tenant->id,
        'type' => 'cash_out',
        'status' => 'posted',
        'date' => now()->format('Y-m-d'),
    ]);

    TransactionDetail::factory()->create([
        'transaction_id' => $trxExpense->id,
        'coa_id' => $this->expenseCoa->id,
        'debit' => 250000,
        'credit' => 0
    ]);

    $expenseBalance = $this->dashboardService->getExpenseBalance('this_month');
    expect($expenseBalance)->toBe(250000.0);
});

