@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Create Transaction</h1>
            <p class="text-sm text-slate-500">Fill in the details below to create a new transaction.</p>
        </div>

        <form method="POST" action="{{ route('transactions.store') }}"
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 uppercase mb-1">Tipe Transaksi</label>
                    <select name="type"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="cash_in">Kas Masuk</option>
                        <option value="cash_out">Kas Keluar</option>
                        <option value="bank_in">Bank Masuk</option>
                        <option value="bank_out">Bank Keluar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Date</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 uppercase mb-1">Account</label>
                    <select name="cash_bank_id"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach ($cashBankCoas as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 uppercase mb-1">Account</label>
                    <select name="opponent_id"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach ($opponentCoas as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 uppercase mb-1">Amount</label>
                <input type="number" name="total_amount" step="0.01"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 font-mono">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 uppercase mb-1">Notes</label>
                <input type="textarea" name="description" rows="3"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('transactions.index') }}"
                    class="px-4 py-2 border rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                    Save
                </button>
            </div>
        </form>
    </div>
@endsection
