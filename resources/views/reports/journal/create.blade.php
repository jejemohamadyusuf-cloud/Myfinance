@extends('layouts.app')

@section('title', 'Tambah Jurnal Umum')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f4f6fc; min-height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- Header Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4"
                    style="background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1" style="color: #4338ca;">Input Jurnal Umum</h4>
                            <p class="text-muted mb-0 small">Catat transaksi double-entry secara presisi dan terstruktur.</p>
                        </div>
                        <a href="{{ route('journals.index') }}"
                            class="btn btn-outline-primary rounded-pill px-4 shadow-sm bg-white"
                            style="border-color: #818cf8; color: #4f46e5;">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>

                {{-- Alert Error --}}
                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center"
                        style="background-color: #fef2f2; color: #991b1b;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4"
                        style="background-color: #fef2f2; color: #991b1b;">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Input --}}
                <form action="{{ route('journals.store') }}" method="POST" id="journalForm">
                    @csrf

                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary small">TANGGAL TRANSAKSI <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="transaction_date"
                                        class="form-control form-control-lg rounded-3 border-light-subtle shadow-none"
                                        value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-secondary small">KETERANGAN / MEMO
                                        HEADER</label>
                                    <input type="text" name="description"
                                        class="form-control form-control-lg rounded-3 border-light-subtle shadow-none"
                                        placeholder="Contoh: Penyesuaian Beban Sewa Bulan September"
                                        value="{{ old('description') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Table Detail --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                        <div
                            class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="color: #3730a3;">Rincian Baris Akun (Min. 2 Baris)</h6>
                            <button type="button" class="btn btn-sm rounded-pill px-3 shadow-sm text-white" id="addRowBtn"
                                style="background-color: #6366f1;">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Baris
                            </button>
                        </div>

                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle" id="journalTable">
                                    <thead>
                                        <tr class="text-secondary border-bottom small text-uppercase">
                                            <th style="width: 35%;">Akun COA <span class="text-danger">*</span></th>
                                            <th style="width: 20%;">Debet (Rp)</th>
                                            <th style="width: 20%;">Kredit (Rp)</th>
                                            <th style="width: 20%;">Catatan Baris</th>
                                            <th style="width: 5%; text-align: center;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="journalRows">
                                        {{-- Row 1 --}}
                                        <tr class="journal-row">
                                            <td>
                                                <select name="details[0][coa_id]"
                                                    class="form-select border-light-subtle rounded-3 shadow-none coa-select"
                                                    required>
                                                    <option value="" disabled selected>-- Pilih Akun --</option>
                                                    @foreach ($coas as $coa)
                                                        <option value="{{ $coa->id }}">{{ $coa->code }} -
                                                            {{ $coa->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="details[0][debit]"
                                                    class="form-control border-light-subtle rounded-3 shadow-none debit-input"
                                                    placeholder="0">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                    name="details[0][credit]"
                                                    class="form-control border-light-subtle rounded-3 shadow-none credit-input"
                                                    placeholder="0">
                                            </td>
                                            <td>
                                                <input type="text" name="details[0][memo]"
                                                    class="form-control border-light-subtle rounded-3 shadow-none"
                                                    placeholder="Opsional">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-link text-danger p-0 remove-row-btn"
                                                    style="text-decoration: none;">
                                                    <i class="bi bi-trash fs-5"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Row 2 --}}
                                        <tr class="journal-row">
                                            <td>
                                                <select name="details[1][coa_id]"
                                                    class="form-select border-light-subtle rounded-3 shadow-none coa-select"
                                                    required>
                                                    <option value="" disabled selected>-- Pilih Akun --</option>
                                                    @foreach ($coas as $coa)
                                                        <option value="{{ $coa->id }}">{{ $coa->code }} -
                                                            {{ $coa->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="details[1][debit]"
                                                    class="form-control border-light-subtle rounded-3 shadow-none debit-input"
                                                    placeholder="0">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                    name="details[1][credit]"
                                                    class="form-control border-light-subtle rounded-3 shadow-none credit-input"
                                                    placeholder="0">
                                            </td>
                                            <td>
                                                <input type="text" name="details[1][memo]"
                                                    class="form-control border-light-subtle rounded-3 shadow-none"
                                                    placeholder="Opsional">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-link text-danger p-0 remove-row-btn"
                                                    style="text-decoration: none;">
                                                    <i class="bi bi-trash fs-5"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Footer Total & Balance Indicator --}}
                            <div class="row mt-4 pt-3 border-top align-middle">
                                <div class="col-md-6 d-flex align-items-center">
                                    <div id="balanceStatus" class="badge px-3 py-2 rounded-pill fs-6 fw-normal"
                                        style="background-color: #fee2e2; color: #991b1b;">
                                        <i class="bi bi-x-circle me-1"></i> Unbalanced (Selisih: Rp <span
                                            id="balanceDiff">0</span>)
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="mb-1 text-secondary small">Total Debet: <span class="fw-bold text-dark"
                                            id="totalDebitText">Rp 0</span></div>
                                    <div class="mb-1 text-secondary small">Total Kredit: <span class="fw-bold text-dark"
                                            id="totalCreditText">Rp 0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journals.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn rounded-pill px-5 text-white shadow" id="submitBtn"
                            style="background-color: #4f46e5;" disabled>
                            <i class="bi bi-check-circle me-1"></i> Simpan Jurnal
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Script Dynamic Row & Balance Calculation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let rowIndex = 2;
            const journalRows = document.getElementById('journalRows');
            const addRowBtn = document.getElementById('addRowBtn');
            const totalDebitText = document.getElementById('totalDebitText');
            const totalCreditText = document.getElementById('totalCreditText');
            const balanceStatus = document.getElementById('balanceStatus');
            const balanceDiff = document.getElementById('balanceDiff');
            const submitBtn = document.getElementById('submitBtn');

            // Template Baris Baru
            function getRowTemplate(index) {
                return `
            <tr class="journal-row">
                <td>
                    <select name="details[${index}][coa_id]" class="form-select border-light-subtle rounded-3 shadow-none coa-select" required>
                        <option value="" disabled selected>-- Pilih Akun --</option>
                        @foreach ($coas as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="details[${index}][debit]" class="form-control border-light-subtle rounded-3 shadow-none debit-input" placeholder="0">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="details[${index}][credit]" class="form-control border-light-subtle rounded-3 shadow-none credit-input" placeholder="0">
                </td>
                <td>
                    <input type="text" name="details[${index}][memo]" class="form-control border-light-subtle rounded-3 shadow-none" placeholder="Opsional">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger p-0 remove-row-btn" style="text-decoration: none;">
                        <i class="bi bi-trash fs-5"></i>
                    </button>
                </td>
            </tr>
        `;
            }

            // Tambah Baris
            addRowBtn.addEventListener('click', function() {
                journalRows.insertAdjacentHTML('beforeend', getRowTemplate(rowIndex));
                rowIndex++;
                calculateTotals();
            });

            // Hapus Baris
            journalRows.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row-btn')) {
                    const rows = document.querySelectorAll('.journal-row');
                    if (rows.length > 2) {
                        e.target.closest('.journal-row').remove();
                        calculateTotals();
                    } else {
                        alert('Jurnal minimal harus memiliki 2 baris akun!');
                    }
                }
            });

            // Event Listener Input Angka
            journalRows.addEventListener('input', function(e) {
                if (e.target.classList.contains('debit-input') || e.target.classList.contains(
                        'credit-input')) {
                    const row = e.target.closest('.journal-row');
                    const debitInput = row.querySelector('.debit-input');
                    const creditInput = row.querySelector('.credit-input');

                    // Mencegah isi debet dan kredit bersamaan dalam 1 baris
                    if (e.target.classList.contains('debit-input') && parseFloat(debitInput.value) > 0) {
                        creditInput.value = '';
                    } else if (e.target.classList.contains('credit-input') && parseFloat(creditInput
                        .value) > 0) {
                        debitInput.value = '';
                    }

                    calculateTotals();
                }
            });

            // Kalkulasi Debet, Kredit & Status Balance
            function calculateTotals() {
                let totalDebit = 0;
                let totalCredit = 0;

                document.querySelectorAll('.debit-input').forEach(input => {
                    totalDebit += parseFloat(input.value) || 0;
                });

                document.querySelectorAll('.credit-input').forEach(input => {
                    totalCredit += parseFloat(input.value) || 0;
                });

                const diff = Math.abs(totalDebit - totalCredit);

                totalDebitText.textContent = 'Rp ' + totalDebit.toLocaleString('id-ID');
                totalCreditText.textContent = 'Rp ' + totalCredit.toLocaleString('id-ID');

                if (totalDebit > 0 && diff === 0) {
                    balanceStatus.className = 'badge px-3 py-2 rounded-pill fs-6 fw-normal';
                    balanceStatus.style.backgroundColor = '#dcfce7';
                    balanceStatus.style.color = '#15803d';
                    balanceStatus.innerHTML = '<i class="bi bi-check-circle me-1"></i> Balance';
                    submitBtn.disabled = false;
                } else {
                    balanceStatus.className = 'badge px-3 py-2 rounded-pill fs-6 fw-normal';
                    balanceStatus.style.backgroundColor = '#fee2e2';
                    balanceStatus.style.color = '#991b1b';
                    balanceStatus.innerHTML =
                        `<i class="bi bi-x-circle me-1"></i> Unbalanced (Selisih: Rp ${diff.toLocaleString('id-ID')})`;
                    submitBtn.disabled = true;
                }
            }
        });
    </script>
@endsection
