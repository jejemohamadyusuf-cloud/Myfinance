<?php

namespace Database\Seeders;

use App\Models\Coa;
use Illuminate\Database\Seeder;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coas = [
            //asset (1000)
            ['code' => '1101', 'name' => 'Kas Kasir', 'type' => 'asset', 'is_cash_bank' => true],
            ['code' => '1102', 'name' => 'Bank Danamon', 'type' => 'asset', 'is_cash_bank' => true],
            ['code' => '1103', 'name' => 'Bank Bca Elsa', 'type' => 'asset', 'is_cash_bank' => true],
            ['code' => '1104', 'name' => 'Bank Bca JJ', 'type' => 'asset', 'is_cash_bank' => true],

            //kewajiban(2000)
            ['code' => '2101', 'name' => 'Hutang Usaha', 'type' => 'liability', 'is_cash_bank' => false],

            //equitas(3000)
            ['code' => '3101', 'name' => 'Modal Pemilik', 'type' => 'equity', 'is_cash_bank' => false],

            //pendapatan(4000)
            ['code' => '4101', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue', 'is_cash_bank' => false],
            ['code' => '4102', 'name' => 'Pendapatan Lain-lain', 'type' => 'revenue', 'is_cash_bank' => false],

            //beban(5000)
            ['code' => '5101', 'name' => 'Beban Gaji', 'type' => 'expense', 'is_cash_bank' => false],
            ['code' => '5102', 'name' => 'Beban Listrik Air', 'type' => 'expense', 'is_cash_bank' => false],
            ['code' => '5103', 'name' => 'Beban Operasional', 'type' => 'expense', 'is_cash_bank' => false],
            ['code' => '5104', 'name' => 'Beban Rumah Tangga', 'type' => 'expense', 'is_cash_bank' => false],
        ];

        foreach ($coas as $coa) {
            Coa::create($coa);
        }
    }
}
