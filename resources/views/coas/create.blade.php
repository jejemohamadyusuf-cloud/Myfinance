@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Create New COA Account</h1>
                <p class="text-sm text-slate-500 mt-0.5">Add a new account to Chart of Accounts</p>
            </div>
            <a href="{{ route('coas.index') }}"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm">&larr;
                Back to List</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
            <form action="{{ route('coas.store') }}" method="POST" onsubmit="disableCoaSubmitButton(this)">
                @csrf
                <div class="space-y-5">

                    <!-- 1. Category Type -->
                    <div>
                        <label for="type" class="block text-xs font-bold text-slate-500 uppercase mb-2">
                            1. Select Category Type <span class="text-rose-500">*</span>
                        </label>
                        <select id="type" name="type" required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('type') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Category --</option>
                            <option value="asset" {{ old('type') === 'asset' ? 'selected' : '' }}>Asset (Aset / Harta / Kas
                                / Bank)</option>
                            <option value="liability" {{ old('type') === 'liability' ? 'selected' : '' }}>Liability (Hutang
                                / Kewajiban)</option>
                            <option value="equity" {{ old('type') === 'equity' ? 'selected' : '' }}>Equity (Modal)</option>
                            <option value="revenue" {{ old('type') === 'revenue' ? 'selected' : '' }}>Revenue (Pendapatan
                                Penjualan)</option>
                            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Expense (Beban / Biaya
                                Operasional)</option>
                        </select>
                        @error('type')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. Account Code -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="code" class="block text-xs font-bold text-slate-500 uppercase">
                                2. Account Code <span class="text-rose-500">*</span>
                            </label>
                            <span id="codeBadge"
                                class="hidden text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                ✓ Auto Sugesti Terverifikasi
                            </span>
                        </div>
                        <input type="text" id="code" name="code" value="{{ old('code') }}"
                            placeholder="Pilih kategori di atas lebih dulu..." required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('code') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                        <p id="codeStatusText" class="text-[11px] text-slate-400 mt-1.5">
                            Awalan standar: <strong>11</strong> (Aset), <strong>21</strong> (Hutang), <strong>31</strong>
                            (Modal), <strong>41</strong> (Pendapatan), <strong>51</strong> (Beban).
                        </p>
                        @error('code')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 3. Account Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-500 uppercase mb-2">
                            3. Account Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Contoh: Beban Listrik & Air" required
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('name') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 4. Is Cash / Bank -->
                    <div class="pt-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="is_cash_bank" name="is_cash_bank" value="1"
                                {{ old('is_cash_bank') ? 'checked' : '' }} class="sr-only peer"
                                onchange="toggleSubCategoryContainer(this.checked)">
                            <div
                                class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                            </div>
                            <span class="ms-3 text-sm font-medium text-slate-700">Tandai sebagai Akun Kas atau Bank?</span>
                        </label>
                    </div>

                    <!-- 🟢 [MARK: ADDED] Field Sub Category (Dropdown Cash / Bank) -->
                    <div id="sub_category_container" class="{{ old('is_cash_bank') ? '' : 'hidden' }}">
                        <label for="sub_category" class="block text-xs font-bold text-slate-500 uppercase mb-2">
                            Cash / Bank Type <span class="text-rose-500">*</span>
                        </label>
                        <select id="sub_category" name="sub_category"
                            class="w-full px-4 py-2.5 bg-slate-50 border @error('sub_category') border-rose-500 @else border-slate-300 @enderror rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="" disabled {{ old('sub_category') ? '' : 'selected' }}>-- Select Type --
                            </option>
                            <option value="cash" {{ old('sub_category') === 'cash' ? 'selected' : '' }}>Cash
                            </option>
                            <option value="bank" {{ old('sub_category') === 'bank' ? 'selected' : '' }}>Bank</option>
                        </select>
                        @error('sub_category')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('coas.index') }}"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-xs rounded-lg transition">Cancel</a>
                    <button type="submit" id="saveCoaBtn"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        Save Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Logika Auto-Suggestion, Visibility Sub Category, & Double Submit Protection -->
    <script>
        function disableCoaSubmitButton(form) {
            const btn = form.querySelector('#saveCoaBtn');
            if (btn.dataset.submitted === "true") {
                event.preventDefault();
                return false;
            }

            btn.dataset.submitted = "true";
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.innerText = 'Saving Account...';
        }

        // 🟢 [MARK: ADDED] Toggle visibility sub_category dropdown
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

        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const codeInput = document.getElementById('code');
            const badge = document.getElementById('codeBadge');

            const existingCoas = @json($coas ?? []);
            const usedCodesSet = new Set(existingCoas.map(c => String(c.code).trim()));

            const prefixes = {
                'asset': '11',
                'liability': '21',
                'equity': '31',
                'revenue': '41',
                'expense': '51'
            };

            function generateNextUniqueCode(type) {
                const prefix = prefixes[type];
                if (!prefix) return '';

                const categoryCodes = Array.from(usedCodesSet)
                    .filter(code => code.startsWith(prefix))
                    .map(code => parseInt(code, 10))
                    .filter(num => !isNaN(num));

                let nextSequence = 1;

                if (categoryCodes.length > 0) {
                    const maxCode = Math.max(...categoryCodes);
                    const maxSeq = maxCode % 100;
                    nextSequence = maxSeq + 1;
                }

                let candidateCode = prefix + String(nextSequence).padStart(2, '0');

                while (usedCodesSet.has(candidateCode)) {
                    nextSequence++;
                    candidateCode = prefix + String(nextSequence).padStart(2, '0');
                }

                return candidateCode;
            }

            typeSelect.addEventListener('change', function() {
                const type = this.value;

                if (type && prefixes[type]) {
                    const suggestedCode = generateNextUniqueCode(type);
                    codeInput.value = suggestedCode;
                    badge.textContent = '✓ Auto Sugesti Terverifikasi';
                    badge.className =
                        'text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200';
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });

            codeInput.addEventListener('input', function() {
                const inputVal = this.value.trim();
                if (usedCodesSet.has(inputVal)) {
                    badge.textContent = 'Kode Sudah Digunakan!';
                    badge.className =
                        'text-[11px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200';
                    badge.classList.remove('hidden');
                } else if (inputVal !== '') {
                    badge.textContent = 'Kode Tersedia';
                    badge.className =
                        'text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200';
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
