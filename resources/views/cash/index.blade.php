@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Cash Transactions</h1>
                <p class="text-sm text-slate-500 mt-0.5">Manage physical cash transactions (Cash In & Cash Out)</p>
            </div>
            <a href="{{ route('cash.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                Insert
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Voucher No.</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4 text-right">Amount (RP)</th>
                        <th class="py-3.5 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 text-center">
                                @if (strtolower($trx->status) === 'void')
                                    <span
                                        class="px-2 py-1 bg-rose-100 text-rose-700 text-[10px] font-bold uppercase rounded-full border border-rose-200">Void</span>
                                @else
                                    <span
                                        class="px-2 py-1 bg-emerald-100 text-emeral-700 text-[10px] font-bold uppercase rounded-full border border-emerald-200">Posted</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-indigo-600">{{ $trx->transaction_number }}</td>
                            <td class="py-3 px-4">
                                <span
                                    class="px-2.5 py-1 text-[11px] font-semibold rounded-full 
                                {{ strtolower($trx->type) === 'cash_in' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ strtoupper($trx->type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">{{ $trx->description ?? '-' }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold">
                                {{ number_format($trx->total_amount, 2, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                @if ($trx->status !== 'void')
                                    <a href="{{ route('transactions.edit', $trx->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold mr-2">Edit</a>

                                    <form action="{{ route('transactions.void', $trx->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini (VOID)? Vouchernya akan tetap tersimpan.');">
                                        @csrf
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-900 text-xs font-semibold">
                                            Void
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('transactions.show', $trx->id) }}"
                                        class="text-slate-500 hover:text-slate-800 text-xs font-semibold underline">
                                        View
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No Cash transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
