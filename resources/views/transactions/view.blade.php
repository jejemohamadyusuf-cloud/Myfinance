@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header Page -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Voucher Transaksi</h1>
                <p class="text-xs font-semibold text-slate-500 mt-1">Rincian jurnal pencatatan keuangan double-entry.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $backRoute ?? route(str_replace('_', '.', $transactions->type) . '.index') }}"
                    class="px-4 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 border border-[#D5CFC4] text-xs font-semibold rounded-xl transition shadow-sm">
                    &larr; Back
                </a>
                @if (strtolower($transactions->status) !== 'void')
                    <a href="{{ route('transactions.edit', $transactions->id) }}"
                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Edit Transaksi
                    </a>
                @endif
            </div>
        </div>

        <!-- Voucher Card Container -->
        <div class="bg-white rounded-2xl border border-[#E2DDD5] shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">
            <!-- Voucher Header Details -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-[#E2DDD5]">
                <div>
                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">No. Voucher</span>
                    <span
                        class="text-sm font-mono font-extrabold text-slate-900">{{ $transactions->transaction_number }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal</span>
                    <span
                        class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($transactions->date)->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tipe Transaksi</span>
                    <span
                        class="inline-block px-2 py-0.5 mt-0.5 rounded text-[10px] font-extrabold uppercase border {{ in_array(strtolower($transactions->type), ['cash_in', 'bank_in']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                        {{ str_replace('_', ' ', $transactions->type) }}
                    </span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Nominal</span>
                    <span class="text-sm font-mono font-extrabold text-indigo-700">Rp
                        {{ number_format($transactions->total_amount, 2, ',', '.') }}</span>
                </div>
            </div>

            <!-- Detail Jurnal Berpasangan Table -->
            <div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Rincian Jurnal Berpasangan
                    (Double Entry)</h3>
                <div class="border border-[#E2DDD5] rounded-xl overflow-hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-[#F8F6F0] border-b border-[#E2DDD5] font-bold text-slate-600">
                                <th class="py-2.5 px-4">Kode & Nama Akun</th>
                                <th class="py-2.5 px-4 text-right">Debit (Rp)</th>
                                <th class="py-2.5 px-4 text-right">Kredit (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2DDD5] font-medium text-slate-800">
                            @foreach ($transactions->details as $detail)
                                <tr>
                                    <td class="py-2.5 px-4">
                                        <span
                                            class="font-mono font-bold text-indigo-600">[{{ $detail->coa->code ?? '-' }}]</span>
                                        {{ $detail->coa->name ?? '-' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900">
                                        {{ $detail->debit > 0 ? number_format($detail->debit, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900">
                                        {{ $detail->credit > 0 ? number_format($detail->credit, 2, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Keterangan / Memo -->
            <div class="p-4 bg-[#F8F6F0] border border-[#E2DDD5] rounded-xl">
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Keterangan /
                    Memori</span>
                <p class="text-xs font-medium text-slate-700">
                    {{ $transactions->description ?: 'Tidak ada keterangan tambahan.' }}</p>
            </div>
        </div>
    </div>
@endsection
