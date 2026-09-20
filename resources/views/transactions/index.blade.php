@extends('layouts.app')

@section('content')
    @php
        // 🟢 [MARK: FIX] Tentukan Rute Tombol "Add Transaction" Berdasarkan Tipe Halaman
        $currentType = strtolower($type ?? 'cash_in');

        $createRoute = match ($currentType) {
            'cash_in' => route('cash.in.create'),
            'cash_out' => route('cash.out.create'),
            'bank_in' => route('bank.in.create'),
            'bank_out' => route('bank.out.create'),
            default => route('cash.in.create'),
        };
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Page -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <!-- 🟢 [MARK: FIX] Judul Dinamis Sesuai Modul ($title) -->
                <h1 class="text-2xl font-bold text-slate-800">{{ $title ?? 'Daftar Transaksi' }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kelola dan pantau seluruh histori pencatatan transaksi</p>
            </div>

            <!-- 🟢 [MARK: FIX] Dynamic Create Route Button -->
            <a href="{{ $createRoute }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Transaction</span>
            </a>
        </div>

        <!-- Notification Alerts -->
        @if (session('success'))
            <div
                class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
            </div>
        @endif

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <th class="px-4 py-4">Transaction No</th>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">Type</th>
                            <th class="px-4 py-4">Keterangan</th>
                            <th class="px-4 py-4 text-right">Amount (Rp)</th>
                            <th class="px-4 py-4 text-center">Status</th>
                            <th class="px-4 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse ($transactions as $trx)
                            @php
                                $trxTypeLower = strtolower($trx->type);
                                $isVoid = strtolower($trx->status) === 'void';
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition {{ $isVoid ? 'bg-slate-50/80 opacity-60' : '' }}">
                                <!-- Transaction Number -->
                                <td class="px-4 py-3.5 font-mono font-bold text-indigo-600">
                                    {{ $trx->transaction_number }}
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                                </td>

                                <!-- Type Badge -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ in_array($trxTypeLower, ['cash_in', 'bank_in']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                        {{ strtoupper(str_replace('_', ' ', $trxTypeLower)) }}
                                    </span>
                                </td>

                                <!-- Keterangan / Note -->
                                <td class="px-4 py-3.5 text-slate-600 max-w-xs truncate">
                                    {{ $trx->description ?? '-' }}
                                </td>

                                <!-- Amount -->
                                <td
                                    class="px-4 py-3.5 text-right font-mono font-bold whitespace-nowrap {{ $isVoid ? 'line-through text-slate-400' : 'text-slate-800' }}">
                                    Rp {{ number_format($trx->total_amount, 2, ',', '.') }}
                                </td>

                                <!-- Status Badge -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    @if ($isVoid)
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-slate-200 text-slate-600 border border-slate-300">
                                            VOID
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            POSTED
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap space-x-2">
                                    <!-- 🟢 [MARK: FIX] Menggunakan route 'transactions.show' yang benar -->
                                    <a href="{{ route('transactions.show', $trx->id) }}"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded transition inline-block">
                                        View
                                    </a>

                                    @if (!$isVoid)
                                        <a href="{{ route('transactions.edit', $trx->id) }}"
                                            class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold rounded transition inline-block">
                                            Edit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium text-slate-500">Belum ada data transaksi
                                        {{ strtolower($title ?? '') }}</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Add Transaction" di atas untuk
                                        menambah transaksi baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if (method_exists($transactions, 'hasPages') && $transactions->hasPages())
                <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
