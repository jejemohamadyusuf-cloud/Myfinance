@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header Page -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Transaksi</h1>
            </div>
            <a href="{{ route(str_replace('_', '.', $transaction->type) . '.index') }}"
                class="px-4 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 border border-[#D5CFC4] text-xs font-semibold rounded-xl transition shadow-sm">
                &larr; Back
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 mb-6">
                <p class="font-extrabold text-sm mb-1">Gagal Memperbarui Transaksi:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card Container -->
        <form method="POST" action="{{ route('transactions.update', $transaction->id) }}"
            class="bg-white p-6 sm:p-8 rounded-2xl border border-[#E2DDD5] shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <!-- Hidden Input Tipe Transaksi -->
            <input type="hidden" name="type" value="{{ strtolower($transaction->type) }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- No. Voucher -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">No. Voucher</label>
                    <input type="text" value="{{ $transaction->transaction_number }}" readonly
                        class="w-full px-4 py-3 bg-[#FBF9F5] border border-[#E2DDD5] rounded-xl text-sm font-mono font-bold text-slate-500 cursor-not-allowed">
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Transaksi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date"
                        value="{{ old('date', \Carbon\Carbon::parse($transaction->date)->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 bg-[#FBF9F5] border @error('date') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                    @error('date')
                        <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Panel Pilihan COA -->
            <div class="p-5 rounded-xl bg-[#F2EFE9] border border-[#E2DDD5] space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Total Amount -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Total Nominal (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-500">Rp</span>
                            <input type="number" name="total_amount" step="0.01" min="0.01"
                                value="{{ old('total_amount', $amount ?? $transaction->total_amount) }}" required
                                class="w-full pl-10 pr-4 py-3 bg-white border @error('total_amount') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-indigo-600 transition">
                        </div>
                        @error('total_amount')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 1. Akun Kas / Bank -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase mb-2">
                            Akun {{ str_contains(strtolower($transaction->type), 'bank') ? 'Bank' : 'Kas' }} <span
                                class="text-rose-500">*</span>
                        </label>
                        <select name="cash_bank_id" required
                            class="w-full px-4 py-3 bg-white border @error('cash_bank_id') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-indigo-600 transition">
                            <option value="" disabled>-- Pilih Akun Kas/Bank --</option>
                            @foreach ($cashBankCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('cash_bank_id', $cashBankCoaId) == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cash_bank_id')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. Akun Lawan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase mb-2">
                            Akun Lawan <span class="text-rose-500">*</span>
                        </label>
                        <select name="opponent_id" required
                            class="w-full px-4 py-3 bg-white border @error('opponent_id') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-indigo-600 transition">
                            <option value="" disabled>-- Pilih Akun Lawan --</option>
                            @foreach ($opponentCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('opponent_id', $opponentCoaId) == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->name }}
                                    ({{ ucfirst($coa->category_type ?? ($coa->type ?? 'Lawan')) }})
                                </option>
                            @endforeach
                        </select>
                        @error('opponent_id')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keterangan / Memori
                    Transaksi</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-3 bg-[#FBF9F5] border @error('description') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">{{ old('description', $transaction->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2DDD5]">
                <a href="{{ route(str_replace('_', '.', $transaction->type) . '.index') }}"
                    class="px-5 py-2.5 border border-[#D5CFC4] bg-[#F2EFE9] hover:bg-[#E2DDD5] rounded-xl text-xs font-semibold text-slate-800 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection
