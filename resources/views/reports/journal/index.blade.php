@extends('layouts.app')

@section('title', 'Daftar Jurnal Umum')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f4f6fc; min-height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-lg-11">

                {{-- Header Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4"
                    style="background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1" style="color: #4338ca;">Jurnal Umum</h4>
                            <p class="text-muted mb-0 small">Daftar riwayat seluruh transaksi jurnal akuntansi double-entry.
                            </p>
                        </div>
                        <a href="{{ route('journals.create') }}" class="btn rounded-pill px-4 text-white shadow-sm"
                            style="background-color: #4f46e5;">
                            <i class="bi bi-plus-lg me-1"></i> Buat Jurnal Baru
                        </a>
                    </div>
                </div>

                {{-- Alert Success --}}
                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center"
                        style="background-color: #dcfce7; color: #15803d;">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                {{-- Table List --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8fafc;">
                                    <tr class="text-secondary small text-uppercase">
                                        <th class="ps-4 py-3">No. Jurnal & Tanggal</th>
                                        <th class="py-3">Keterangan</th>
                                        <th class="py-3">Rincian Akun (Debet / Kredit)</th>
                                        <th class="pe-4 py-3 text-end">Total Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($journals as $journal)
                                        <tr class="border-bottom">
                                            <td class="ps-4 align-top py-3" style="width: 22%;">
                                                <div class="fw-bold text-primary mb-1">{{ $journal->journal_number }}</div>
                                                <div class="text-muted small"><i
                                                        class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($journal->transaction_date)->format('d M Y') }}
                                                </div>
                                            </td>
                                            <td class="align-top py-3" style="width: 28%;">
                                                <div class="text-dark">{{ $journal->description ?? '-' }}</div>
                                                <span
                                                    class="badge bg-light text-secondary rounded-pill border mt-1 fw-normal"
                                                    style="font-size: 0.75rem;">
                                                    {{ strtoupper($journal->source_type) }}
                                                </span>
                                            </td>
                                            <td class="align-top py-3" style="width: 35%;">
                                                <table class="table table-sm table-borderless mb-0">
                                                    @foreach ($journal->details as $detail)
                                                        <tr>
                                                            <td class="p-0 text-secondary" style="width: 60%;">
                                                                <span
                                                                    class="{{ $detail->credit > 0 ? 'ps-3 text-muted' : 'fw-semibold text-dark' }}">
                                                                    {{ $detail->coa->code }} - {{ $detail->coa->name }}
                                                                </span>
                                                            </td>
                                                            <td class="p-0 text-end" style="width: 40%;">
                                                                @if ($detail->debit > 0)
                                                                    <span class="text-success small fw-semibold">Rp
                                                                        {{ number_format($detail->debit, 0, ',', '.') }}</span>
                                                                @else
                                                                    <span class="text-secondary small">Rp
                                                                        {{ number_format($detail->credit, 0, ',', '.') }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                            <td class="pe-4 align-top py-3 text-end fw-bold text-dark" style="width: 15%;">
                                                Rp {{ number_format($journal->total_debit, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                                Belum ada data transaksi Jurnal Umum.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if ($journals->hasPages())
                        <div class="card-footer bg-transparent border-0 p-4">
                            {{ $journals->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
