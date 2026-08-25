@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Header Dashboard -->
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Financial Dashboard</h1>
            <p class="text-sm text-slate-500 mt-0.5">Real-time overview of cash, bank balances, and daily transaction trends
            </p>
        </div>

        <!-- Top Cards (Ringkasan Saldo) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Saldo Kas -->
            <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Saldo Kas</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 font-mono mt-1">
                        Rp {{ number_format($cashBalance, 2, ',', '.') }}
                    </h3>
                    <span
                        class="inline-block text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 mt-2">
                        Physical Cash
                    </span>
                </div>
            </div>

            <!-- Card 2: Saldo Bank -->
            <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Saldo Bank</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 font-mono mt-1">
                        Rp {{ number_format($bankBalance, 2, ',', '.') }}
                    </h3>
                    <span
                        class="inline-block text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 mt-2">
                        Bank Accounts
                    </span>
                </div>
            </div>

            <!-- Card 3: Total Likuiditas -->
            <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Likuiditas</p>
                    <h3 class="text-2xl font-extrabold text-indigo-600 font-mono mt-1">
                        Rp {{ number_format($totalBalance, 2, ',', '.') }}
                    </h3>
                    <span
                        class="inline-block text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200 mt-2">
                        Kas + Bank Total
                    </span>
                </div>
            </div>
        </div>

        <!-- Section Grafik Tren Mutasi Harian -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Tren Mutasi Kas & Bank Harian</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Perbandingan Pemasukan (In) dan Pengeluaran (Out) 7 Hari
                        Terakhir</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-emerald-600">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Pemasukan
                    </span>
                    <span class="flex items-center gap-1.5 text-rose-600">
                        <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span> Pengeluaran
                    </span>
                </div>
            </div>

            <!-- Container Chart -->
            <div class="relative w-full h-80">
                <canvas id="cashBankTrendChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('cashBankTrendChart').getContext('2d');

            const labels = @json($trends['labels']);
            const incomeData = @json($trends['incomeData']);
            const expenseData = @json($trends['expenseData']);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Pemasukan (Rp)',
                            data: incomeData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#10b981',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Pengeluaran (Rp)',
                            data: expenseData,
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#f43f5e',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw || 0;
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat(
                                        'id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    family: 'sans-serif'
                                },
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    family: 'sans-serif'
                                },
                                color: '#64748b',
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
