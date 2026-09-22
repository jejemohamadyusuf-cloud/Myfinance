@extends('layouts.app')

@section('content')
    @php
        $currentType = strtolower($type ?? 'cash_in');

        $createRoute = match ($currentType) {
            'cash_in' => route('cash.in.create'),
            'cash_out' => route('cash.out.create'),
            'bank_in' => route('bank.in.create'),
            'bank_out' => route('bank.out.create'),
            default => route('cash.in.create'),
        };
    @endphp

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Page -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $title ?? 'Daftar Transaksi' }}
                </h1>
                <p class="text-xs font-semibold text-slate-600 mt-1">
                    Kelola dan pantau seluruh histori pencatatan transaksi kas & bank secara realtime.
                </p>
            </div>

            <a href="{{ $createRoute }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Transaksi</span>
            </a>
        </div>

        <!-- Notification Alerts -->
        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold rounded-2xl flex items-center justify-between shadow-sm">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-rose-600 hover:text-rose-800 font-bold">&times;</button>
            </div>
        @endif

        <!-- Table Card Container (Pure White Card with Soft Beige Border) -->
        <div class="bg-white rounded-2xl border border-[#E2DDD5] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <!-- Table Header (Soft Beige) -->
                        <tr
                            class="bg-[#F2EFE9] border-b border-[#E2DDD5] text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">No. Voucher</th>
                            <th class="px-5 py-4">Tipe</th>
                            <th class="px-5 py-4">Keterangan</th>
                            <th class="px-5 py-4 text-right">Nominal (Rp)</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2DDD5]/60 text-xs sm:text-sm text-slate-800">
                        @forelse ($transactions as $trx)
                            @php
                                $trxTypeLower = strtolower($trx->type);
                                $isVoid = strtolower($trx->status) === 'void';
                            @endphp
                            <tr class="hover:bg-[#FBF9F5] transition {{ $isVoid ? 'bg-slate-50/80 opacity-60' : '' }}">
                                <!-- Status Badge -->
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if ($isVoid)
                                        <span
                                            class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                                            VOID
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            POSTED
                                        </span>
                                    @endif
                                </td>

                                <!-- Tanggal -->
                                <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-800">
                                    {{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}
                                </td>

                                <!-- No Voucher -->
                                <td class="px-5 py-4 font-mono font-bold text-indigo-600 whitespace-nowrap">
                                    {{ $trx->transaction_number }}
                                </td>

                                <!-- Tipe Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg border {{ in_array($trxTypeLower, ['cash_in', 'bank_in']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                        {{ strtoupper(str_replace('_', ' ', $trxTypeLower)) }}
                                    </span>
                                </td>

                                <!-- Keterangan -->
                                <td class="px-5 py-4 text-slate-600 max-w-xs truncate font-medium">
                                    {{ $trx->description ?? '-' }}
                                </td>

                                <!-- Nominal -->
                                <td
                                    class="px-5 py-4 text-right font-mono font-bold whitespace-nowrap {{ $isVoid ? 'line-through text-slate-400' : 'text-slate-900' }}">
                                    Rp {{ number_format($trx->total_amount, 2, ',', '.') }}
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4 text-center whitespace-nowrap space-x-1.5">
                                    <a href="{{ route('transactions.show', $trx->id) }}"
                                        class="px-3 py-1 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 text-xs font-semibold rounded-lg border border-[#D5CFC4] transition inline-block">
                                        Lihat
                                    </a>

                                    @if (!$isVoid)
                                        <a href="{{ route('transactions.edit', $trx->id) }}"
                                            class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-semibold rounded-lg transition inline-block">
                                            Edit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">
                                    <p class="font-bold text-slate-700">Belum ada data transaksi</p>
                                    <p class="text-xs text-slate-500 mt-1">Klik tombol "Tambah Transaksi" di atas untuk
                                        memulai pencatatan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if (method_exists($transactions, 'hasPages') && $transactions->hasPages())
                <div class="px-5 py-4 bg-[#F2EFE9] border-t border-[#E2DDD5]">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
