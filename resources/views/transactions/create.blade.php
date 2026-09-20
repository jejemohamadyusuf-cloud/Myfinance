@extends('layouts.app')

@section('content')
    @php
        // 🟢 [MARK: FIX] Evaluasi Tipe Transaksi
        $currentType = old('type', $type ?? 'cash_in');
        $typeLower = strtolower($currentType);
        $isIncome = in_array($typeLower, ['cash_in', 'bank_in']);
        $isBank = str_contains($typeLower, 'bank');

        // 🟢 [MARK: FIX] Tentukan Indikator Debet & Kredit Terkunci
        $primaryRoleText = $isIncome ? 'DEBIT (+ Kas/Bank)' : 'KREDIT (- Kas/Bank)';
        $primaryBadgeClass = $isIncome
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';

        $opponentRoleText = $isIncome ? 'KREDIT (Sumber/Lawan)' : 'DEBIT (Beban/Aset/Lawan)';
        $opponentBadgeClass = $isIncome
            ? 'bg-rose-50 text-rose-700 border-rose-200'
            : 'bg-emerald-50 text-emerald-700 border-emerald-200';

        // 🟢 [MARK: FIX] Route Batal / Kembali Dinamis
        $backRoute = match ($typeLower) {
            'cash_in' => route('cash.in.index'),
            'cash_out' => route('cash.out.index'),
            'bank_in' => route('bank.in.index'),
            'bank_out' => route('bank.out.index'),
            default => route('cash.in.index'),
        };
    @endphp

    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $title ?? 'Create Transaction' }}</h1>
                <p class="text-sm text-slate-500">Fill in the details below to create a new transaction with automatic
                    double-entry locking.</p>
            </div>
            <a href="{{ $backRoute }}"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm">
                &larr; Back to List
            </a>
        </div>

        <!-- 🟢 [MARK: FIX] Form submit dilindungi dengan pencegah double-click -->
        <form method="POST" action="{{ route('transactions.store') }}" onsubmit="disableTransactionSubmitButton(this)"
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-5">
            @csrf

            <!-- 🟢 [MARK: FIX] Kirim parameter tipe transaksi secara terkunci -->
            <input type="hidden" name="type" value="{{ $typeLower }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tipe Transaksi (Disabled View Only agar tidak bisa dikacaukan pengguna) -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Transaction Type</label>
                    <input type="text" readonly value="{{ strtoupper(str_replace('_', ' ', $typeLower)) }}"
                        class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg text-sm font-bold text-indigo-700 cursor-not-allowed">
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">
                        Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 bg-slate-50 border @error('date') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('date')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Panel Pilihan COA dengan Penanda Debet/Kredit -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- 1. Akun Kas / Bank Utama -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">
                                1. {{ $isBank ? 'Bank' : 'Cash' }} Account <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border {{ $primaryBadgeClass }}">
                                {{ $primaryRoleText }}
                            </span>
                        </div>
                        <select name="cash_bank_id" required
                            class="w-full px-3 py-2 bg-white border @error('cash_bank_id') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="" disabled {{ old('cash_bank_id') ? '' : 'selected' }}>-- Select
                                {{ $isBank ? 'Bank' : 'Cash' }} --</option>
                            @foreach ($cashBankCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('cash_bank_id') == $coa->id ? 'selected' : '' }}>
                                    [{{ $coa->code }}] {{ $coa->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cash_bank_id')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. Akun Lawan (Opponent) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">
                                2. Opponent Account <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border {{ $opponentBadgeClass }}">
                                {{ $opponentRoleText }}
                            </span>
                        </div>
                        <select name="opponent_id" required
                            class="w-full px-3 py-2 bg-white border @error('opponent_id') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="" disabled {{ old('opponent_id') ? '' : 'selected' }}>-- Select Opponent
                                --</option>
                            @foreach ($opponentCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('opponent_id') == $coa->id ? 'selected' : '' }}>
                                    [{{ $coa->code }}] {{ $coa->name }} ({{ ucfirst($coa->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('opponent_id')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Amount / Nominal -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">
                    Amount (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-400">Rp</span>
                    <input type="number" name="total_amount" step="0.01" min="0.01"
                        value="{{ old('total_amount') }}" placeholder="0.00" required
                        class="w-full pl-10 pr-3 py-2 bg-slate-50 border @error('total_amount') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm font-mono font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                @error('total_amount')
                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- 🟢 [MARK: FIX] Perbaikan elemen Textarea yang benar -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Notes / Description</label>
                <textarea name="description" rows="3" placeholder="Enter transaction note..."
                    class="w-full px-3 py-2 bg-slate-50 border @error('description') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ $backRoute }}"
                    class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    Cancel
                </a>
                <!-- 🟢 [MARK: FIX] Perbaikan kelas CSS hover & pencegah double click -->
                <button type="submit" id="saveTransactionBtn"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                    Save Transaction
                </button>
            </div>
        </form>
    </div>

    <!-- 🟢 [MARK: FIX] Script Disable Submit Button saat Form Dikirikan -->
    <script>
        function disableTransactionSubmitButton(form) {
            const btn = form.querySelector('#saveTransactionBtn');
            if (btn.dataset.submitted === "true") {
                event.preventDefault();
                return false;
            }

            btn.dataset.submitted = "true";
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.innerText = 'Saving Transaction...';
        }
    </script>
@endsection
