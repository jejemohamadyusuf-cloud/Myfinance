@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Create Cash Transaction</h1>
                <p class="text-sm text-slate-500 mt-0.5">Record new cash in or cash out transaction</p>
            </div>
            <a href="{{ route('cash.index') }}"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm">&larr;
                Back to List</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date -->
                    <div>
                        <label for="date" class="block text-xs font-bold text-slate-500 uppercase mb-2">Transaction Date
                            <span class="text-rose-500">*</span></label>
                        <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('date') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('date')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-xs font-bold text-slate-500 uppercase mb-2">Transaction Type
                            <span class="text-rose-500">*</span></label>
                        <select id="type" name="type" required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('type') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="cash_in" {{ old('type') === 'cash_in' ? 'selected' : '' }}>CASH IN (Kas Masuk)
                            </option>
                            <option value="cash_out" {{ old('type') === 'cash_out' ? 'selected' : '' }}>CASH OUT (Kas
                                Keluar)</option>
                        </select>
                        @error('type')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Amount -->
                    <div>
                        <label for="total_amount" class="block text-xs font-bold text-slate-500 uppercase mb-2">Total Amount
                            (RP) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm font-semibold">Rp</span>
                            <input type="number" step="0.01" min="1" id="total_amount" name="total_amount"
                                value="{{ old('total_amount') }}" placeholder="0.00" required
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border @error('total_amount') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        @error('total_amount')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cash Account -->
                    <div>
                        <label for="cash_bank_coa_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Cash
                            Account <span class="text-rose-500">*</span></label>
                        <select id="cash_bank_coa_id" name="cash_bank_coa_id" required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('cash_bank_coa_id') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Select Cash Account --</option>
                            @foreach ($cashBankCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('cash_bank_coa_id') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cash_bank_coa_id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Opponent Account -->
                    <div>
                        <label for="opponent_coa_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Opponent
                            Account <span class="text-rose-500">*</span></label>
                        <select id="opponent_coa_id" name="opponent_coa_id" required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('opponent_coa_id') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Select Opponent Account --</option>
                            @foreach ($opponentCoas as $coa)
                                <option value="{{ $coa->id }}"
                                    {{ old('opponent_coa_id') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }} ({{ ucfirst($coa->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('opponent_coa_id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-xs font-bold text-slate-500 uppercase mb-2">Description /
                            Memo</label>
                        <textarea id="description" name="description" rows="3" placeholder="Enter cash transaction note..."
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('description') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('cash.index') }}"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-xs rounded-lg transition">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
