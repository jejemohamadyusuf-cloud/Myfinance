@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header Page -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Transaksi</h1>
                <p class="text-xs font-semibold text-slate-600 mt-1">Perbarui rincian transaksi pencatatan keuangan.</p>
            </div>
            <a href="{{ $category === 'bank' ? route('bank.in.index') : route('cash.in.index') }}"
                class="px-4 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 text-xs font-semibold rounded-xl border border-[#D5CFC4] transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        <!-- Form Card Container -->
        <div class="bg-white rounded-2xl border border-[#E2DDD5] shadow-sm p-6 sm:p-8">
            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 🟢 [MARK: FIX] Hidden input tipe transaksi terkirim otomatis -->
                <input type="hidden" name="type" value="{{ strtolower($transaction->type) }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Voucher Number -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">No. Voucher
                            Transaksi</label>
                        <input type="text" value="{{ $transaction->transaction_number }}" readonly
                            class="w-full px-4 py-3 bg-[#FBF9F5] border border-[#E2DDD5] rounded-xl text-sm text-slate-500 font-mono font-bold cursor-not-allowed">
                        <p class="text-[11px] text-slate-500 mt-1">Nomor transaksi dibuat otomatis dan tidak dapat diubah.
                        </p>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label for="date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Tanggal Transaksi <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="date" name="date"
                            value="{{ old('date', \Carbon\Carbon::parse($transaction->date)->format('Y-m-d')) }}" required
                            class="w-full px-4 py-3 bg-[#FBF9F5] border @error('date') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                        @error('date')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 🟢 [MARK: FIX] Tipe Transaksi Tampilan Terkunci (Bukan Dropdown) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Transaksi
                            (Terkunci)</label>
                        <div
                            class="w-full px-4 py-3 bg-[#FBF9F5] border border-[#E2DDD5] rounded-xl text-sm font-extrabold text-indigo-700 flex items-center justify-between">
                            <span>{{ strtoupper(str_replace('_', ' ', $transaction->type)) }}</span>
                            <span
                                class="text-[10px] px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 border border-indigo-200">Locked</span>
                        </div>
                    </div>

                    <!-- Nominal -->
                    <div>
                        <label for="total_amount"
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nominal (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-sm font-bold">Rp</span>
                            <input type="number" step="0.01" min="1" id="total_amount" name="total_amount"
                                value="{{ old('total_amount', $amount) }}" placeholder="0.00" required
                                class="w-full pl-10 pr-4 py-3 bg-[#FBF9F5] border @error('total_amount') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm text-slate-900 font-mono font-bold focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                        </div>
                        @error('total_amount')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Akun Kas/Bank -->
                    <div>
                        <label for="cash_bank_coa_id"
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Akun Kas / Bank <span class="text-rose-500">*</span>
                        </label>
                        <select id="cash_bank_coa_id" name="cash_bank_coa_id" required
                            class="w-full px-4 py-3 bg-[#FBF9F5] border @error('cash_bank_coa_id') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm text-slate-900 font-medium focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                            <option value="">-- Pilih Akun Kas/Bank --</option>
                            @foreach ($cashBankCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('cash_bank_coa_id', $cashBankCoaId) == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cash_bank_coa_id')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Akun Lawan -->
                    <div>
                        <label for="opponent_coa_id"
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Akun Lawan <span class="text-rose-500">*</span>
                        </label>
                        <select id="opponent_coa_id" name="opponent_coa_id" required
                            class="w-full px-4 py-3 bg-[#FBF9F5] border @error('opponent_coa_id') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm text-slate-900 font-medium focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                            <option value="">-- Pilih Akun Lawan --</option>
                            @foreach ($opponentCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('opponent_coa_id', $opponentCoaId) == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }} ({{ ucfirst($coa->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('opponent_coa_id')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description"
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keterangan /
                            Memori</label>
                        <textarea id="description" name="description" rows="3" placeholder="Masukkan memori atau keterangan transaksi..."
                            class="w-full px-4 py-3 bg-[#FBF9F5] border @error('description') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm text-slate-900 font-medium focus:bg-white focus:outline-none focus:border-indigo-600 transition">{{ old('description', $transaction->description) }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 border-t border-[#E2DDD5] flex items-center justify-end gap-3">
                    <a href="{{ $category === 'bank' ? route('bank.in.index') : route('cash.in.index') }}"
                        class="px-5 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 font-semibold text-xs rounded-xl border border-[#D5CFC4] transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition">
                        Perbarui Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
