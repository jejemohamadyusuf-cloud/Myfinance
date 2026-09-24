@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-2">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-none">
                    {{ strtoupper(str_replace('_', ' ', $type ?? 'cash_in')) }}
                </h1>

                <!-- CHECKBOX TAMPILKAN VOID DI BAWAH JUDUL (SEBELAH KIRI) -->
                <form method="GET" action="{{ url()->current() }}" id="voidFilterForm" class="pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="show_void" value="1"
                            {{ request()->boolean('show_void') ? 'checked' : '' }}
                            onchange="document.getElementById('voidFilterForm').submit()"
                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition">
                        <span class="text-xs font-bold text-slate-600">Tampilkan Transaksi Dibatalkan (Void)</span>
                    </label>
                </form>
            </div>

            <!-- Tombol Tambah Transaksi -->
            <a href="{{ route(str_replace('_', '.', $type ?? 'cash_in') . '.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Insert</span>
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
                            <!-- 🟢 [MARK: FIX] STATUS DIKEMBALIKAN / DIPINDAHKAN KE PALING AWAL -->
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5">No. Voucher</th>
                            <th class="py-3.5 px-5">Tanggal</th>
                            <th class="py-3.5 px-5">Tipe</th>
                            <th class="py-3.5 px-5 text-right">Total Nominal</th>
                            <th class="py-3.5 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2DDD5] text-xs font-medium text-slate-800">
                        @forelse ($transactions as $transaction)
                            <tr
                                class="hover:bg-[#FBF9F5] transition {{ strtolower($transaction->status) === 'void' ? 'bg-rose-50/40 opacity-75' : '' }}">
                                <!-- 🟢 [MARK: FIX] KOLOM STATUS DI PALING KIRI -->
                                <td class="py-3.5 px-5">
                                    @if (strtolower($transaction->status) === 'void')
                                        <span
                                            class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-300 uppercase">
                                            VOID
                                        </span>
                                    @else
                                        <span
                                            class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">
                                            POSTED
                                        </span>
                                    @endif
                                </td>
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
                                <td class="py-3.5 px-5 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($transaction->total_amount, 2, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-center whitespace-nowrap space-x-1.5">
                                    <!-- Detail Button -->
                                    <a href="{{ route('transactions.show', $transaction->id) }}"
                                        class="px-2.5 py-1.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-700 font-bold rounded-lg border border-[#D5CFC4] text-[11px] transition inline-block">
                                        Detail
                                    </a>

                                    @if (strtolower($transaction->status) !== 'void')
                                        <!-- Edit Button -->
                                        <a href="{{ route('transactions.edit', $transaction->id) }}"
                                            class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg border border-amber-200 text-[11px] transition inline-block">
                                            Edit
                                        </a>

                                        <!-- Tombol Void Di Sebelah Kanan Edit -->
                                        <form method="POST" action="{{ route('transactions.void', $transaction->id) }}"
                                            class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin membatalkan (VOID) transaksi ini? Jurnal akan dinonaktifkan.');">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg border border-rose-200 text-[11px] transition">
                                                Void
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 font-semibold">
                                    Belum ada data transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($transactions, 'links'))
                <div class="p-4 border-t border-[#E2DDD5] bg-[#F8F6F0]">
                    {{ $transactions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
