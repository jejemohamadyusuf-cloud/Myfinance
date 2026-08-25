<?php

namespace App\Services;

use App\Models\Coa;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CoaService
{
    public function getAllCoas(int $perPage = 10): LengthAwarePaginator
    {
        return Coa::orderBy('code', 'asc')->paginate($perPage);
    }

    public function getCoaOptions(): Collection
    {
        return Coa::select('id', 'code', 'name', 'type')->orderBy('code', 'asc')->get();
    }

    public function getCoaById(int $id): Coa
    {
        return Coa::findOrFail($id);
    }

    public function createCoa(array $data): Coa
    {
        $data['is_cash_bank'] = isset($data['is_cash_bank']) ? (bool) $data['is_cash_bank']: false;
        
        return Coa::create($data);
    }

    public function updateCoa(int $id, array $data): Coa
    {
        $coa = Coa::findOrFail($id);
        $data['is_cash_bank'] = isset($data['is_cash_bank']) ? (bool) $data['is_cash_bank']: false;

        $coa->update($data);
        return $coa;
    }

    public function deleteCoa(int $id,): bool
    {
        $coa = Coa::findOrFail($id);
        if ($coa->transactionDetails?->exists()) {
            throw new \Exception('Cannot delete COA because it has recorded transaction');
        }

        return $coa->delete();
    }
}
