@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header Page -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Transaction Details</h1>
                <p class="text-sm text-slate-500 mt-0.5">Read-only view for financial transaction record</p>
            </div>
            <a href="{{ route($route) }}"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm">
                &larr; Back to List
            </a>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">

            <!-- Banner Status Void -->
            @if ($transactions->status === 'void')
                <div
                    class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between text-rose-800">
                    <div class="flex items-center gap-3">
                        <span
                            class="px-2.5 py-1 bg-rose-600 text-white text-xs font-bold uppercase rounded-md shadow-sm">VOID</span>
                        <span class="text-xs font-medium">Transaksi ini telah dibatalkan (Canceled) dan tidak dapat diubah
                            kembali.</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Voucher Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Voucher No.</label>
                    <div
                        class="p-3 bg-slate-100 border border-slate-200 rounded-lg text-sm font-mono font-bold text-slate-700">
                        {{ $transactions->transaction_number }}
                    </div>
                </div>

                <!-- Transaction Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Transaction Date</label>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800">
                        {{ \Carbon\Carbon::parse($transactions->date)->format('d F Y') }}
                    </div>
                </div>

                <!-- Transaction Type -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Transaction Type</label>
                    <div
                        class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm font-semibold uppercase text-slate-800">
                        {{ $transactions->type }}
                    </div>
                </div>

                <!-- Total Amount -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Total Amount</label>
                    <div
                        class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm font-mono font-bold text-slate-800">
                        Rp {{ number_format($transactions->total_amount, 2, ',', '.') }}
                    </div>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description / Memo</label>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700">
                        {{ $transactions->description ?? '-' }}
                    </div>
                </div>
            </div>

            <!-- Detail Jurnal Berpasangan -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Journal Entries</h2>
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-4">Account Code</th>
                                <th class="py-2.5 px-4">Account Name</th>
                                <th class="py-2.5 px-4 text-right">Debit (RP)</th>
                                <th class="py-2.5 px-4 text-right">Credit (RP)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono">
                            @foreach ($transactions->details as $detail)
                                <tr>
                                    <td class="py-2.5 px-4 font-bold text-indigo-600">{{ $detail->coa->code ?? '-' }}</td>
                                    <td class="py-2.5 px-4 font-sans text-slate-700">{{ $detail->coa->name ?? '-' }}</td>
                                    <td class="py-2.5 px-4 text-right">{{ number_format($detail->debit, 2, ',', '.') }}</td>
                                    <td class="py-2.5 px-4 text-right">{{ number_format($detail->credit, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                <a href="{{ route($route) }}"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-xs rounded-lg transition">
                    Close
                </a>
            </div>
        </div>
    </div>
@endsection
