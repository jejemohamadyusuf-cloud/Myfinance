@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header Page -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Jurnal Umum (General Journal)</h1>
            </div>
        </div>

        <!-- Filter Form Card -->
        <form method="GET" action="{{ route('reports.journal') }}"
            class="bg-white p-5 rounded-2xl border border-[#E2DDD5] shadow-sm">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                        onchange="document.getElementById('end_date').min = this.value"
                        class="w-full px-4 py-2.5 bg-[#FBF9F5] border @error('start_date') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sampai
                        Tanggal</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                        min="{{ $startDate }}"
                        class="w-full px-4 py-2.5 bg-[#FBF9F5] border @error('end_date') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                </div>

                <div class="flex items-center gap-2 pt-2 sm:pt-0">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition">
                        Filter Laporan
                    </button>
                    <a href="{{ route('reports.journal') }}"
                        class="px-4 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 border border-[#D5CFC4] text-xs font-semibold rounded-xl transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        @error('end_date')
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-700">
                {{ $message }}
            </div>
        @enderror

        <!-- Table Card Container -->
        <div class="bg-white rounded-2xl border border-[#E2DDD5] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-[#F8F6F0] border-b border-[#E2DDD5] text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Tanggal / No. Voucher</th>
                            <th class="py-3.5 px-5">Kode COA</th>
                            <th class="py-3.5 px-5">Nama Akun & Keterangan</th>
                            <th class="py-3.5 px-5 text-right">Debit (Rp)</th>
                            <th class="py-3.5 px-5 text-right">Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2DDD5] text-xs font-medium text-slate-800">
                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp
                        @forelse($journal as $transaction)
                            @foreach ($transaction->details as $index => $detail)
                                @php
                                    $totalDebit += $detail->debit;
                                    $totalCredit += $detail->credit;
                                @endphp
                                <tr class="hover:bg-[#FBF9F5] transition">
                                    @if ($index === 0)
                                        <td class="py-3.5 px-5 align-top border-r border-[#E2DDD5] bg-[#FDFDFB]"
                                            rowspan="{{ count($transaction->details) }}">
                                            <div class="font-bold text-slate-900">
                                                {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-[11px] text-indigo-700 font-mono font-bold mt-0.5">
                                                {{ $transaction->transaction_number }}
                                            </div>
                                        </td>
                                    @endif

                                    <td class="py-2.5 px-5 font-mono font-bold text-indigo-700 whitespace-nowrap">
                                        {{ optional($detail->coa)->code ?? '-' }}
                                    </td>

                                    <td class="py-2.5 px-5">
                                        <span
                                            class="{{ $detail->credit > 0 ? 'pl-6 block text-slate-600 font-semibold' : 'font-bold text-slate-900' }}">
                                            {{ optional($detail->coa)->name ?? 'Akun Tidak Diketahui' }}
                                        </span>
                                        @if ($index === count($transaction->details) - 1 && $transaction->description)
                                            <div class="text-[11px] text-slate-500 italic mt-0.5 font-normal">
                                                Catatan: {{ $transaction->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <td
                                        class="py-2.5 px-5 text-right font-mono {{ $detail->debit > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ $detail->debit > 0 ? number_format($detail->debit, 2, ',', '.') : '-' }}
                                    </td>

                                    <td
                                        class="py-2.5 px-5 text-right font-mono {{ $detail->credit > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ $detail->credit > 0 ? number_format($detail->credit, 2, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-500 font-semibold">
                                    Tidak ada catatan jurnal transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($journal) > 0)
                        <tfoot>
                            <tr class="bg-[#F2EFE9] border-t-2 border-[#E2DDD5] text-xs font-extrabold text-slate-900">
                                <td colspan="3" class="py-3.5 px-5 text-right uppercase tracking-wider">Total
                                    Keseimbangan Jurnal:</td>
                                <td class="py-3.5 px-5 text-right font-mono text-indigo-700">
                                    Rp {{ number_format($totalDebit, 2, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right font-mono text-indigo-700">
                                    Rp {{ number_format($totalCredit, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
