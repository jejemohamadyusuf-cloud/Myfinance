@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header Page -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Tambah Akun COA</h1>
            </div>
            <a href="{{ route('coas.index') }}"
                class="px-4 py-2.5 bg-[#F2EFE9] hover:bg-[#E2DDD5] text-slate-800 border border-[#D5CFC4] text-xs font-semibold rounded-xl transition shadow-sm">
                &larr; Back
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 mb-6">
                <p class="font-extrabold text-sm mb-1">Gagal Menyimpan COA:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card Container -->
        <form action="{{ route('coas.store') }}" method="POST" onsubmit="disableCoaSubmitButton(this, event)"
            class="bg-white p-6 sm:p-8 rounded-2xl border border-[#E2DDD5] shadow-sm space-y-6">
            @csrf

            <!-- 1. Category Type -->
            <div>
                <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    1. Pilih Kategori Akun <span class="text-rose-500">*</span>
                </label>
                <select id="type" name="type" required
                    class="w-full px-4 py-3 bg-[#FBF9F5] border @error('type') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="asset" {{ old('type') === 'asset' ? 'selected' : '' }}>Asset (Aset / Harta / Kas / Bank)
                    </option>
                    <option value="liability" {{ old('type') === 'liability' ? 'selected' : '' }}>Liability (Hutang /
                        Kewajiban)</option>
                    <option value="equity" {{ old('type') === 'equity' ? 'selected' : '' }}>Equity (Modal)</option>
                    <option value="revenue" {{ old('type') === 'revenue' ? 'selected' : '' }}>Revenue (Pendapatan)</option>
                    <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Expense (Beban / Biaya
                        Operasional)</option>
                </select>
                @error('type')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Account Code -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        2. Kode Akun <span class="text-rose-500">*</span>
                    </label>
                    <span id="codeBadge"
                        class="hidden text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                        ✓ Auto Sugesti Terverifikasi
                    </span>
                </div>
                <input type="text" id="code" name="code" value="{{ old('code') }}"
                    placeholder="Pilih kategori di atas lebih dulu..." required
                    class="w-full px-4 py-3 bg-[#FBF9F5] border @error('code') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-mono font-bold text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">

                <p id="codeStatusText" class="text-[11px] font-medium text-slate-500 mt-1.5">
                    Awalan standar: <strong>11</strong> (Aset), <strong>21</strong> (Hutang), <strong>31</strong> (Modal),
                    <strong>41</strong> (Pendapatan), <strong>51</strong> (Beban).
                </p>
                @error('code')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Account Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    3. Nama Akun Perkiraan <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    placeholder="Contoh: Beban Listrik & Air" required
                    class="w-full px-4 py-3 bg-[#FBF9F5] border @error('name') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Panel Checkbox & Type Cash/Bank -->
            <div class="p-5 rounded-xl bg-[#F2EFE9] border border-[#E2DDD5] space-y-4">
                <div>
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="is_cash_bank" name="is_cash_bank" value="1"
                            {{ old('is_cash_bank') ? 'checked' : '' }} class="sr-only peer"
                            onchange="toggleSubCategoryContainer(this.checked)">
                        <div
                            class="relative w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                        </div>
                        <span class="ms-3 text-xs font-bold text-slate-800 uppercase tracking-wider">Tandai sebagai Akun Kas
                            atau Bank?</span>
                    </label>
                </div>

                <div id="sub_category_container" class="{{ old('is_cash_bank') ? '' : 'hidden' }}">
                    <label for="sub_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tipe Kas / Bank <span class="text-rose-500">*</span>
                    </label>
                    <select id="sub_category" name="sub_category"
                        class="w-full px-4 py-3 bg-white border @error('sub_category') border-rose-500 @else border-[#E2DDD5] @enderror rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-indigo-600 transition">
                        <option value="" disabled {{ old('sub_category') ? '' : 'selected' }}>-- Pilih Tipe --
                        </option>
                        <option value="cash" {{ old('sub_category') === 'cash' ? 'selected' : '' }}>Cash (Kas Fisik / Kas
                            Kecil)</option>
                        <option value="bank" {{ old('sub_category') === 'bank' ? 'selected' : '' }}>Bank (Rekening Bank)
                        </option>
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
                    Cancel
                </a>
                <button type="submit" id="saveCoaBtn"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition">
                    Save
                </button>
            </div>
        </form>
    </div>

    <script>
        // 🟢 FIX JAVASCRIPT SUBMIT: Menggunakan setTimeout 50ms agar request terkirim lancar
        function disableCoaSubmitButton(form, e) {
            const btn = form.querySelector('#saveCoaBtn');
            if (btn.dataset.submitted === "true") {
                e.preventDefault();
                return false;
            }

            btn.dataset.submitted = "true";
            setTimeout(() => {
                btn.disabled = true;
                btn.classList.add('opacity-60', 'cursor-not-allowed');
                btn.innerText = 'Menyimpan...';
            }, 50);
        }

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
                        'text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200';
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
                        'text-[10px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200';
                    badge.classList.remove('hidden');
                } else if (inputVal !== '') {
                    badge.textContent = 'Kode Tersedia';
                    badge.className =
                        'text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200';
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
