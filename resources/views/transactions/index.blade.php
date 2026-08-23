@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Cash & Bank List</h1>
            </div>
            <a href="{{ route('transactions.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                Add Transaction
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="px-3.5 py-4">Transaction No</th>
                        <th class="px-3.5 py-4">Date</th>
                        <th class="px-3.5 py-4">Type</th>
                        <th class="px-3.5 py-4">Keterangan</th>
                        <th class="px-3.5 py-4">Amount</th>
                        <th class="px-3.5 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse ($transactions as $trx)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-3 px-4 font-mono font-medium text-indigo-600">{{ $trx->transaction_number }}</td>
                            <td class="px-3 px-4">{{ \Carbon\Carbon::parse($trx->date)->format('d/M/Y') }}</td>
                            <td class="px-3 px-4">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full {{ in_array($trx->type, ['cash_in', 'bank_in']) ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ strtolower(str_replace('_', ' ', $trx->type)) }}
                            </td>
                            <td class="px-3 px-4 text-slate-600">{{ $trx->description }}</td>
                            <td class="px-3 px-4 text-right font-mono font-semibold">
                                {{ number_format($trx->total_amount, 2, ',', '.') }}</td>
                            <td class="px-3 px-4 text-center space-x-2">
                                <a href="{{ route('transactions.view', $trx->id) }}"
                                    class="text-indigo-600 hover:underline text-xs font-semibold">View</a>
                                <a href="{{ route('transactions.edit', $trx->id) }}"
                                    class="text-indigo-600 hover:underline text-xs font-semibold">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">Belum ada transaksi</td>
                        </tr>
                    @endforelse
            </table>
        </div>
    @endsection
