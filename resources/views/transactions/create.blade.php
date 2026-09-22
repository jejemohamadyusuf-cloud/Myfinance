@extends('layouts.app')

@section('content')
    @php
        $typeLower = strtolower($type ?? (request()->routeIs('*in*') ? 'cash_in' : 'cash_out'));
        $isIncome = in_array($typeLower, ['cash_in', 'bank_in']);
        $isBank = str_contains($typeLower, 'bank');

        $primaryRoleText = $isIncome ? 'DEBIT (+ Kas/Bank)' : 'KREDIT (- Kas/Bank)';
        $primaryBadgeClass = $isIncome
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';

        $opponentRoleText = $isIncome ? 'KREDIT (Sumber/Lawan)' : 'DEBIT (Beban/Aset/Lawan)';
        $opponentBadgeClass = $isIncome
            ? 'bg-rose-50 text-rose-700 border-rose-200'
            : 'bg-emerald-50 text-emerald-700 border-emerald-200';

        $backRoute = match ($typeLower) {
            'cash_in' => route('cash.in.index'),
            'cash_out' => route('cash.out.index'),
            'bank_in' => route('bank.in.index'),
            'bank_out' => route('bank.out.index'),
            default => route('cash.in.index'),
        };
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header Page -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $title ?? 'Create Transaction' }}
                </h1>
                <p class="text-xs font-semibold text-slate-500 mt-1">
                    Record new {{ str_replace('_', ' ', $typeLower) }} transaction with automatic double-entry journal
                    locking.
                </p>
            </div>
            <a href="{{ $backRoute }}"
                class="px-4 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 border border-[#D5CFC4] text-xs font-semibold rounded-xl transition shadow-sm">
                &larr; Back to List
            </a>
        </div>

        <!-- Form Card Container -->
        <form method="POST" action="{{ route('transactions.store') }}" onsubmit="disableTransactionSubmitButton(this)"
            class="bg-white p-6 sm:p-8 rounded-2xl border border-[#E2DDD5] shadow-sm space-y-6">
            @csrf

            <!-- 🟢 [KING COMMAND]: DIKUNCI TOTAL VIA HIDDEN INPUT, TIDAK ADA DROPDOWN LAGI -->
            <input type="hidden" name="type" value="{{ $typeLower }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Transaction Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Transaction Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                        class="w-full px-4 py-3 bg-[#FBF9F5] border @error('date') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                    @error('date')
                        <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 🟢 [KING COMMAND]: KOTAK INFORMASI TERKUNCI (TANPA PILIHAN) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Transaction Type (Locked)
                    </label>
                    <div
                        class="w-full px-4 py-3 bg-[#FBF9F5] border border-[#E2DDD5] rounded-xl text-sm font-extrabold text-indigo-700 flex items-center justify-between">
                        <span>{{ strtoupper(str_replace('_', ' ', $typeLower)) }}</span>
                        <span
                            class="text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 border border-indigo-200">
                            LOCKED BY MENU
                        </span>
                    </div>
                </div>
            </div>

            <!-- Panel Pilihan COA -->
            <div class="p-5 rounded-xl bg-[#F2EFE9] border border-[#E2DDD5] space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Total Amount -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Total Amount (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-500">Rp</span>
                            <input type="number" name="total_amount" step="0.01" min="0.01"
                                value="{{ old('total_amount') }}" placeholder="0.00" required
                                class="w-full pl-10 pr-4 py-3 bg-white border @error('total_amount') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-indigo-600 transition">
                        </div>
                        @error('total_amount')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 1. Cash / Bank Account -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-800 uppercase">
                                {{ $isBank ? 'Bank' : 'Cash' }} Account <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border {{ $primaryBadgeClass }}">
                                {{ $primaryRoleText }}
                            </span>
                        </div>
                        <select name="cash_bank_id" required
                            class="w-full px-4 py-3 bg-white border @error('cash_bank_id') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-indigo-600 transition">
                            <option value="" disabled {{ old('cash_bank_id') ? '' : 'selected' }}>-- Select
                                {{ $isBank ? 'Bank' : 'Cash' }} Account --</option>
                            @foreach ($cashBankCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('cash_bank_id') == $coa->id ? 'selected' : '' }}>
                                    [{{ $coa->code }}] {{ $coa->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cash_bank_id')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. Opponent Account -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-800 uppercase">
                                Opponent Account <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border {{ $opponentBadgeClass }}">
                                {{ $opponentRoleText }}
                            </span>
                        </div>
                        <select name="opponent_id" required
                            class="w-full px-4 py-3 bg-white border @error('opponent_id') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-indigo-600 transition">
                            <option value="" disabled {{ old('opponent_id') ? '' : 'selected' }}>-- Select Opponent
                                Account --</option>
                            @foreach ($opponentCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('opponent_id') == $coa->id ? 'selected' : '' }}>
                                    [{{ $coa->code }}] {{ $coa->name }} ({{ ucfirst($coa->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('opponent_id')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description / Memo -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Description /
                    Memo</label>
                <textarea name="description" rows="3" placeholder="Enter transaction note..."
                    class="w-full px-4 py-3 bg-[#FBF9F5] border @error('description') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2DDD5]">
                <a href="{{ $backRoute }}"
                    class="px-5 py-2.5 border border-[#D5CFC4] bg-[#F2EFE9] hover:bg-[#E2DDD5] rounded-xl text-xs font-semibold text-slate-800 transition">
                    Cancel
                </a>
                <button type="submit" id="saveTransactionBtn"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition">
                    Save
                </button>
            </div>
        </form>
    </div>

    <script>
        function disableTransactionSubmitButton(form) {
            const btn = form.querySelector('#saveTransactionBtn');
            if (btn.dataset.submitted === "true") {
                event.preventDefault();
                return false;
            }

            btn.dataset.submitted = "true";
            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');
            btn.innerText = 'Saving...';
        }
    </script>
@endsection
