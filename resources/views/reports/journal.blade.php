@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header & Filter -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">General Journal Report</h1>
                <p class="text-sm text-slate-500 mt-0.5">Recap of double-entry transaction journal records</p>
            </div>

            <form method="GET" action="{{ route('reports.journal') }}"
                class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}"
                        class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-t')) }}"
                        class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="self-end">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs rounded-lg shadow-sm transition">
                        Filter Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Journal -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <th class="py-3.5 px-4">DATE / VOUCHER NO</th>
                            <th class="py-3.5 px-4">COA CODE</th>
                            <th class="py-3.5 px-4">ACCOUNT NAME & DESCRIPTION</th>
                            <th class="py-3.5 px-4 text-right">DEBIT (RP)</th>
                            <th class="py-3.5 px-4 text-right">CREDIT (RP)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($journal as $transaction)
                            @foreach ($transaction->details as $index => $detail)
                                <tr class="hover:bg-slate-50/50 transition">
                                    @if ($index === 0)
                                        <td class="py-3.5 px-4 font-medium text-slate-900 align-top border-b border-slate-100"
                                            rowspan="{{ count($transaction->details) }}">
                                            <div class="font-semibold">
                                                {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</div>
                                            <div class="text-xs text-indigo-600 font-mono mt-0.5">
                                                {{ $transaction->transaction_number }}</div>
                                        </td>
                                    @endif
                                    <td class="py-2.5 px-4 font-mono text-slate-600">
                                        {{ $detail->coa->code }}
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <span
                                            class="{{ $detail->credit > 0 ? 'pl-6 block text-slate-500' : 'font-medium text-slate-800' }}">
                                            {{ $detail->coa->name }}
                                        </span>
                                        @if ($index === count($transaction->details) - 1 && $transaction->description)
                                            <div class="text-xs text-slate-400 italic mt-0.5">Note:
                                                {{ $transaction->description }}</div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-800">
                                        {{ $detail->debit > 0 ? number_format($detail->debit, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-800">
                                        {{ $detail->credit > 0 ? number_format($detail->credit, 2, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">No transaction records found in
                                    this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
