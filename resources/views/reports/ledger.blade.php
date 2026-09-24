@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header Page -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Buku Besar (General Ledger)</h1>
            </div>
        </div>

        <!-- Filter Form Card -->
        <form method="GET" action="{{ route('reports.ledger') }}"
            class="bg-white p-5 rounded-2xl border border-[#E2DDD5] shadow-sm space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Akun Perkiraan
                        (COA)</label>
                    <select name="coa_id" required
                        class="w-full px-4 py-2.5 bg-[#FBF9F5] border border-[#E2DDD5] rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                        <option value="">-- Pilih COA --</option>
                        @foreach ($coas as $coaItem)
                            <option value="{{ $coaItem->id }}" {{ request('coa_id') == $coaItem->id ? 'selected' : '' }}>
                                [{{ $coaItem->code }}] {{ $coaItem->name }} ({{ strtoupper($coaItem->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full px-4 py-2.5 bg-[#FBF9F5] border border-[#E2DDD5] rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sampai
                        Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-4 py-2.5 bg-[#FBF9F5] border border-[#E2DDD5] rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                </div>
            </div>

            <div class="pt-3 border-t border-[#E2DDD5] flex justify-end gap-2">
                <a href="{{ route('reports.ledger') }}"
                    class="px-4 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 border border-[#D5CFC4] text-xs font-semibold rounded-xl transition">
                    Reset
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition">
                    Tampilkan Buku Besar
                </button>
            </div>
        </form>

        @if (isset($ledger))
            <!-- Ringkasan Saldo Akun -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-[#E2DDD5] shadow-sm">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-extrabold">Akun Terpilih</span>
                    <div class="text-base font-extrabold text-slate-900 mt-1">
                        [{{ $ledger['coa']->code }}] {{ $ledger['coa']->name }}
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-[#E2DDD5] shadow-sm">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-extrabold">Saldo Awal
                        Periode</span>
                    <div class="text-base font-extrabold text-slate-900 mt-1 font-mono">
                        Rp {{ number_format($ledger['initial_balance'], 2, ',', '.') }}
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-[#E2DDD5] shadow-sm">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-extrabold">Saldo Akhir
                        Periode</span>
                    <div class="text-base font-extrabold text-indigo-700 mt-1 font-mono">
                        Rp {{ number_format($ledger['final_balance'], 2, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- Table Buku Besar -->
            <div class="bg-white rounded-2xl border border-[#E2DDD5] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-[#F8F6F0] border-b border-[#E2DDD5] text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                <th class="py-3.5 px-5">Tanggal</th>
                                <th class="py-3.5 px-5">No. Voucher</th>
                                <th class="py-3.5 px-5">Keterangan</th>
                                <th class="py-3.5 px-5 text-right">Debit (Rp)</th>
                                <th class="py-3.5 px-5 text-right">Kredit (Rp)</th>
                                <th class="py-3.5 px-5 text-right">Saldo Berjalan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2DDD5] text-xs font-medium text-slate-800">
                            <!-- Saldo Awal -->
                            <tr class="bg-[#FBF9F5] font-bold text-slate-800">
                                <td class="py-3.5 px-5">{{ \Carbon\Carbon::parse($ledger['start_date'])->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-5 font-mono text-slate-400">-</td>
                                <td class="py-3.5 px-5 italic">SALDO AWAL PERIODE</td>
                                <td class="py-3.5 px-5 text-right font-mono text-slate-400">-</td>
                                <td class="py-3.5 px-5 text-right font-mono text-slate-400">-</td>
                                <td class="py-3.5 px-5 text-right font-mono font-extrabold text-slate-900">
                                    Rp {{ number_format($ledger['initial_balance'], 2, ',', '.') }}
                                </td>
                            </tr>

                            <!-- Baris Mutasi -->
                            @forelse($ledger['mutations'] as $mutation)
                                <tr class="hover:bg-[#FBF9F5] transition">
                                    <td class="py-3.5 px-5">{{ \Carbon\Carbon::parse($mutation['date'])->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-5 font-mono font-bold text-indigo-700">
                                        {{ $mutation['transaction_number'] }}
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-800 font-medium">
                                        {{ $mutation['description'] ?? '-' }}
                                    </td>
                                    <td
                                        class="py-3.5 px-5 text-right font-mono {{ $mutation['debit'] > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ $mutation['debit'] > 0 ? number_format($mutation['debit'], 2, ',', '.') : '-' }}
                                    </td>
                                    <td
                                        class="py-3.5 px-5 text-right font-mono {{ $mutation['credit'] > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ $mutation['credit'] > 0 ? number_format($mutation['credit'], 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-5 text-right font-mono font-bold text-indigo-700">
                                        Rp {{ number_format($mutation['balance'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500 font-semibold">
                                        Tidak ada mutasi transaksi untuk akun ini pada periode yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white p-12 rounded-2xl border border-[#E2DDD5] text-center text-slate-500 font-semibold">
                Silakan pilih Akun COA dari filter di atas untuk menampilkan rincian buku besar.
            </div>
        @endif
    </div>
@endsection
