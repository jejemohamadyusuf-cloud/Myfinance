@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit COA Account</h1>
                <p class="text-sm text-slate-500 mt-0.5">Update Chart of Account details</p>
            </div>
            <a href="{{ route('coas.index') }}"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm">&larr;
                Back to List</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
            <form action="{{ route('coas.update', $coa->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label for="code" class="block text-xs font-bold text-slate-500 uppercase mb-2">Account Code
                            <span class="text-rose-500">*</span></label>
                        <input type="text" id="code" name="code" value="{{ old('code', $coa->code) }}" required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('code') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('code')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-500 uppercase mb-2">Account Name
                            <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $coa->name) }}" required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('name') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-xs font-bold text-slate-500 uppercase mb-2">Category Type
                            <span class="text-rose-500">*</span></label>
                        <select id="type" name="type" required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('type') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="asset" {{ old('type', $coa->type) === 'asset' ? 'selected' : '' }}>Asset (Harta)
                            </option>
                            <option value="liability" {{ old('type', $coa->type) === 'liability' ? 'selected' : '' }}>
                                Liability (Hutang)</option>
                            <option value="equity" {{ old('type', $coa->type) === 'equity' ? 'selected' : '' }}>Equity
                                (Modal)</option>
                            <option value="revenue" {{ old('type', $coa->type) === 'revenue' ? 'selected' : '' }}>Revenue
                                (Pendapatan)</option>
                            <option value="expense" {{ old('type', $coa->type) === 'expense' ? 'selected' : '' }}>Expense
                                (Beban)</option>
                        </select>
                        @error('type')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Toggle Cash/Bank -->
                    <div class="pt-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="is_cash_bank" name="is_cash_bank" value="1"
                                {{ old('is_cash_bank', $coa->is_cash_bank ?? false) ? 'checked' : '' }}
                                class="sr-only peer" onchange="toggleSubCategoryContainer(this.checked)">
                            <div
                                class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                            </div>
                            <span class="ms-3 text-sm font-medium text-slate-700">Is Cash or Bank Account?</span>
                        </label>
                    </div>

                    <div id="sub_category_container"
                        class="{{ old('is_cash_bank', $coa->is_cash_bank ?? false) ? '' : 'hidden' }}">
                        <label for="sub_category" class="block text-xs font-bold text-slate-500 uppercase mb-2">
                            Cash / Bank Type <span class="text-rose-500">*</span>
                        </label>
                        <select id="sub_category" name="sub_category"
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('sub_category') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="" disabled
                                {{ old('sub_category', $coa->sub_category) ? '' : 'selected' }}>-- Select Type --</option>
                            <option value="cash"
                                {{ old('sub_category', $coa->sub_category) === 'cash' ? 'selected' : '' }}>Cash (Kas)
                            </option>
                            <option value="bank"
                                {{ old('sub_category', $coa->sub_category) === 'bank' ? 'selected' : '' }}>Bank</option>
                        </select>
                        @error('sub_category')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('coas.index') }}"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-xs rounded-lg transition">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSubCategoryContainer(isChecked) {
            const container = document.getElementById('sub_category_container');
            const select = document.getElementById('sub_category');

            if (isChecked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
                select.value = ''; // Reset pilihan jika toggle dimatikan
            }
        }
    </script>
@endsection
