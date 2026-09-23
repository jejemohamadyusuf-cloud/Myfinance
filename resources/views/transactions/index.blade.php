@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ strtoupper(str_replace('_', ' ', $type ?? 'cash_in')) }}
                </h1>
                <p class="text-xs font-semibold text-slate-500 mt-1">
                    Kelola dan pantau seluruh catatan jurnal transaksi riil.
                </p>
            </div>

            <a href="{{ route(str_replace('_', '.', $type ?? 'cash_in') . '.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah {{ strtoupper(str_replace('_', ' ', $type ?? 'cash_in')) }}</span>
            </a>
        </div>

        <!-- Notification Banner -->
        @if (session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-600 hover:text-emerald-900 text-base leading-none">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div
                class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-rose-600 hover:text-rose-900 text-base leading-none">&times;</button>
            </div>
        @endif

        <!-- Table Card Container -->
        <div class="bg-white rounded-2xl border border-[#E2DDD5] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-[#F8F6F0] border-b border-[#E2DDD5] text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3.5 px-5">No. Voucher</th>
                            <th class="py-3.5 px-5">Tanggal</th>
                            <th class="py-3.5 px-5">Tipe</th>
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5 text-right">Total Nominal</th>
                            <th class="py-3.5 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2DDD5] text-xs font-medium text-slate-800">
                        @forelse ($transactions as $transaction)
                            <tr class="hover:bg-[#FBF9F5] transition">
                                <td class="py-3.5 px-5 font-mono font-bold text-slate-900">
                                    {{ $transaction->transaction_number }}
                                </td>
                                <td class="py-3.5 px-5">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <span
                                        class="inline-block px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wide border {{ in_array(strtolower($transaction->type), ['cash_in', 'bank_in']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                        {{ str_replace('_', ' ', $transaction->type) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5">
                                    @if (strtolower($transaction->status) === 'void')
                                        <span
                                            class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300 uppercase">
                                            VOID
                                        </span>
                                    @else
                                        <span
                                            class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">
                                            POSTED
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($transaction->total_amount, 2, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-center space-x-2">
                                    <!-- Detail -->
                                    <a href="{{ route('transactions.show', $transaction->id) }}"
                                        class="px-2.5 py-1.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-700 font-bold rounded-lg border border-[#D5CFC4] text-[11px] transition">
                                        Detail
                                    </a>
                                    <!-- Edit -->
                                    @if (strtolower($transaction->status) !== 'void')
                                        <a href="{{ route('transactions.edit', $transaction->id) }}"
                                            class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg border border-amber-200 text-[11px] transition">
                                            Edit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 font-semibold">
                                    Belum ada data transaksi untuk kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($transactions, 'links'))
                <div class="p-4 border-t border-[#E2DDD5] bg-[#F8F6F0]">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
