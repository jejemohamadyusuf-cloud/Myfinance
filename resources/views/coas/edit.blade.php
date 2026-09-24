@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header Page -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Akun COA</h1>
                <p class="text-xs font-semibold text-slate-500 mt-1">Perbarui rincian Chart of Account.</p>
            </div>
            <a href="{{ route('coas.index') }}"
                class="px-4 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 border border-[#D5CFC4] text-xs font-semibold rounded-xl transition shadow-sm">
                &larr; Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 mb-6">
                <p class="font-extrabold text-sm mb-1">Gagal Memperbarui COA:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card Container -->
        <form action="{{ route('coas.update', $coa->id) }}" method="POST"
            class="bg-white p-6 sm:p-8 rounded-2xl border border-[#E2DDD5] shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <!-- Account Code -->
            <div>
                <label for="code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kode Akun <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="code" name="code" value="{{ old('code', $coa->code) }}" required
                    class="w-full px-4 py-3 bg-[#FBF9F5] border @error('code') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-mono font-bold text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                @error('code')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Akun Perkiraan <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $coa->name) }}" required
                    class="w-full px-4 py-3 bg-[#FBF9F5] border @error('name') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Type -->
            <div>
                <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kategori Akun <span class="text-rose-500">*</span>
                </label>
                <select id="type" name="type" required
                    class="w-full px-4 py-3 bg-[#FBF9F5] border @error('type') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                    <option value="asset" {{ old('type', $coa->type) === 'asset' ? 'selected' : '' }}>Asset (Harta)
                    </option>
                    <option value="liability" {{ old('type', $coa->type) === 'liability' ? 'selected' : '' }}>Liability
                        (Hutang)</option>
                    <option value="equity" {{ old('type', $coa->type) === 'equity' ? 'selected' : '' }}>Equity (Modal)
                    </option>
                    <option value="revenue" {{ old('type', $coa->type) === 'revenue' ? 'selected' : '' }}>Revenue
                        (Pendapatan)</option>
                    <option value="expense" {{ old('type', $coa->type) === 'expense' ? 'selected' : '' }}>Expense (Beban)
                    </option>
                </select>
                @error('type')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Panel Checkbox & Type Cash/Bank -->
            <div class="p-5 rounded-xl bg-[#F2EFE9] border border-[#E2DDD5] space-y-4">
                <div>
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="is_cash_bank" name="is_cash_bank" value="1"
                            {{ old('is_cash_bank', $coa->is_cash_bank ?? false) ? 'checked' : '' }} class="sr-only peer"
                            onchange="toggleSubCategoryContainer(this.checked)">
                        <div
                            class="relative w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                        </div>
                        <span class="ms-3 text-xs font-bold text-slate-800 uppercase tracking-wider">Tandai sebagai Akun Kas
                            atau Bank?</span>
                    </label>
                </div>

                <div id="sub_category_container"
                    class="{{ old('is_cash_bank', $coa->is_cash_bank ?? false) ? '' : 'hidden' }}">
                    <label for="sub_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tipe Kas / Bank <span class="text-rose-500">*</span>
                    </label>
                    <select id="sub_category" name="sub_category"
                        class="w-full px-4 py-3 bg-white border @error('sub_category') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-indigo-600 transition">
                        <option value="" disabled {{ old('sub_category', $coa->sub_category) ? '' : 'selected' }}>--
                            Pilih Tipe --</option>
                        <option value="cash" {{ old('sub_category', $coa->sub_category) === 'cash' ? 'selected' : '' }}>
                            Cash (Kas)</option>
                        <option value="bank" {{ old('sub_category', $coa->sub_category) === 'bank' ? 'selected' : '' }}>
                            Bank</option>
                    </select>
                    @error('sub_category')
                        <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2DDD5]">
                <a href="{{ route('coas.index') }}"
                    class="px-5 py-2.5 border border-[#D5CFC4] bg-[#F2EFE9] hover:bg-[#E2DDD5] rounded-xl text-xs font-semibold text-slate-800 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition">
                    Perbarui Akun
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleSubCategoryContainer(isChecked) {
            const container = document.getElementById('sub_category_container');
            const select = document.getElementById('sub_category');

            if (isChecked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
                select.value = '';
            }
        }
    </script>
@endsection
