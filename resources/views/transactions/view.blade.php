@extends('layouts.app')

@section('content')
    @php
        $typeLower = strtolower($transaction->type);
        $isVoid = strtolower($transaction->status) === 'void';

        // Penentuan Badge Warna Tipe Transaksi
        $badgeClass = in_array($typeLower, ['cash_in', 'bank_in'])
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';

        // Route Kembali Dinamis
        $backRoute = match ($typeLower) {
            'cash_in' => route('cash.in.index'),
            'cash_out' => route('cash.out.index'),
            'bank_in' => route('bank.in.index'),
            'bank_out' => route('bank.out.index'),
            default => route('cash.in.index'),
        };
    @endphp

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header Page & Action Buttons -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-800">Detail Transaksi</h1>
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase rounded border {{ $badgeClass }}">
                        {{ strtoupper(str_replace('_', ' ', $typeLower)) }}
                    </span>
                    @if ($isVoid)
                        <span
                            class="px-2.5 py-0.5 text-xs font-bold uppercase rounded bg-slate-200 text-slate-600 border border-slate-300">
                            VOID
                        </span>
                    @else
                        <span
                            class="px-2.5 py-0.5 text-xs font-bold uppercase rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                            POSTED
                        </span>
                    @endif
                </div>
                <p class="text-sm font-mono text-indigo-600 font-semibold mt-1">
                    No: {{ $transaction->transaction_number }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ $backRoute }}"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm">
                    &larr; Kembali
                </a>

                @if (!$isVoid)
                    <a href="{{ route('transactions.edit', $transaction->id) }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                        Edit Transaksi
                    </a>

                    <!-- Modal / Form Void Trigger -->
                    <form action="{{ route('transactions.void', $transaction->id) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan (Void) transaksi ini? Jurnal transaksi akan ditandai VOID.');"
                        class="inline">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-lg transition">
                            Void Transaksi
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Alert Notification -->
        @if (session('success'))
            <div
                class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Information Card (Header Transaksi) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Ringkasan Informasi</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase">Tanggal Transaksi</span>
                        <span class="text-sm font-bold text-slate-800 mt-1 block">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('d F Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase">Total Nominal</span>
                        <span
                            class="text-base font-mono font-bold text-slate-900 mt-1 block {{ $isVoid ? 'line-through text-slate-400' : '' }}">
                            Rp {{ number_format($transaction->total_amount, 2, ',', '.') }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase">Dibuat Oleh</span>
                        <span class="text-sm font-semibold text-slate-800 mt-1 block">
                            {{ $transaction->creator->name ?? 'Sistem' }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <span class="block text-xs font-semibold text-slate-500 uppercase">Catatan / Keterangan</span>
                    <p class="text-sm text-slate-700 mt-1 italic">
                        {{ $transaction->description ?? 'Tidak ada catatan transaksi.' }}
                    </p>
                </div>
            </div>

            <!-- Double-Entry Journal Table (Rincian Jurnal Berpasangan) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                        Rincian Jurnal Berpasangan (Double-Entry Ledger)
                    </h2>
                    <span class="text-[11px] text-slate-400 font-medium">Otomatis Terkunci Backend</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50/50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                                <th class="px-6 py-3">Kode COA</th>
                                <th class="px-6 py-3">Nama Akun (Chart of Account)</th>
                                <th class="px-6 py-3 text-right">Debet (Rp)</th>
                                <th class="px-6 py-3 text-right">Kredit (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp

                            @forelse ($transaction->details as $detail)
                                @php
                                    $totalDebit += $detail->debit;
                                    $totalCredit += $detail->credit;
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-3.5 font-mono font-bold text-indigo-600">
                                        {{ $detail->coa->code ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 font-medium text-slate-800">
                                        {{ $detail->coa->name ?? '-' }}
                                        @if (optional($detail->coa)->is_cash_bank)
                                            <span
                                                class="ml-2 text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600 border border-indigo-100 font-normal">
                                                Akun Utama
                                            </span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-3.5 text-right font-mono font-semibold {{ $detail->debit > 0 ? 'text-emerald-700' : 'text-slate-300' }}">
                                        {{ number_format($detail->debit, 2, ',', '.') }}
                                    </td>
                                    <td
                                        class="px-6 py-3.5 text-right font-mono font-semibold {{ $detail->credit > 0 ? 'text-rose-700' : 'text-slate-300' }}">
                                        {{ number_format($detail->credit, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400">Rincian jurnal tidak
                                        ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t border-slate-200 font-bold text-sm text-slate-800">
                                <td colspan="2" class="px-6 py-3.5 text-right uppercase text-xs">Total Jurnal:</td>
                                <td class="px-6 py-3.5 text-right font-mono text-emerald-700">
                                    Rp {{ number_format($totalDebit, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-mono text-rose-700">
                                    Rp {{ number_format($totalCredit, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
