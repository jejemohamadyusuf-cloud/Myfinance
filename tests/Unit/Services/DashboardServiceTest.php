<?php

use App\Models\Coa;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Services\CoaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    #setup tenant user
    $tenantA = User::factory()->create([
        'role' => User::ROLE_MASTER,
        'owner_id' => null
    ]);

    $tenantB = User::factory()->create([
        'role' => User::ROLE_MASTER,
        'owner_id' => null
    ]);

    $this->actingAs($tenantA);
    $serviceA = new CoaService();

    #setup coa kas
    $cashA = Coa::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Kas Tenant A',
        'code' => '101A',
        'type' => 'asset',
        'is_cash_bank' => true,
    ]);

    $trxA = Transactoion::create([
        'tenant_id' => $tenantA->id,
        'type' => 'cash_in',
        'status' => 'posted',
        'date' => now()->format('Y-m-d'),
        'transaction_number' => 'TRX-A-' . uniqid(),
        'total_amount' => 1000000
    ]);
    TransactionDetail::create([
        'transaction_id' => $trxA->id,
        'coa_id' => $cashA->id,
        'debit' => 1000000,
        'credit' => 0
    ]);

    $this->actingAs($tenantB);
    $serviceB = new CoaService();

    $balanceB = $serviceB->getCashBalance();

    expect($balanceB)->toBe(0.0);
});


