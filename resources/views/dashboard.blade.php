@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header Dashboard -->
        <div>
            <h1 class="text-2xl font-extrabold text-[#2D3B6B] tracking-tight">Financial Dashboard</h1>
        </div>

        <!-- Top Cards (Ringkasan Saldo) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- Card 1: Saldo Kas -->
            <div class="bg-white rounded-2xl p-6 border border-blue-100/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Saldo Kas</p>
                    <h3 class="text-2xl font-extrabold text-[#2D3B6B] font-mono mt-1">
                        Rp {{ number_format($cashBalance, 2, ',', '.') }}
                    </h3>
                    <span
                        class="inline-block text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200/60 mt-2">
                        Physical Cash
                    </span>
                </div>
            </div>

            <!-- Card 2: Saldo Bank -->
            <div class="bg-white rounded-2xl p-6 border border-blue-100/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Saldo Bank</p>
                    <h3 class="text-2xl font-extrabold text-[#2D3B6B] font-mono mt-1">
                        Rp {{ number_format($bankBalance, 2, ',', '.') }}
                    </h3>
                    <span
                        class="inline-block text-[11px] font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-200/60 mt-2">
                        Bank Accounts
                    </span>
                </div>
            </div>

            <!-- Card 3: Total Likuiditas -->
            <div class="bg-white rounded-2xl p-6 border border-blue-100/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Likuiditas</p>
                    <h3 class="text-2xl font-extrabold text-[#B092EE] font-mono mt-1">
                        Rp {{ number_format($totalBalance, 2, ',', '.') }}
                    </h3>
                    <span
                        class="inline-block text-[11px] font-bold text-[#2D3B6B] bg-[#E8EEFF] px-2.5 py-0.5 rounded-md border border-indigo-200/60 mt-2">
                        Kas + Bank Total
                    </span>
                </div>
            </div>

            <!-- Card 4: Total Expense -->
            <div class="bg-white rounded-2xl p-6 border border-blue-100/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Expense</p>
                    <h3 class="text-2xl font-extrabold text-rose-500 font-mono mt-1">
                        Rp {{ number_format($expenseBalance, 2, ',', '.') }}
                    </h3>
                    <span
                        class="inline-block text-[11px] font-bold text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-md border border-rose-200/60 mt-2">
                        Total Expense
                    </span>
                </div>
            </div>

        </div>

        <!-- Section Grafik Tren Mutasi Harian dengan Filter Tanggal -->
        <div class="bg-white rounded-2xl border border-blue-100/80 shadow-xs p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-extrabold text-[#2D3B6B]">Tren Mutasi Kas & Bank Harian</h2>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">Perbandingan Pemasukan (In) dan Pengeluaran (Out)
                        Riil</p>
                </div>

                <!-- Form Filter Tanggal -->
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5 bg-[#E8EEFF] p-1.5 rounded-xl border border-blue-100/80">
                        <input type="date" name="start_date" value="{{ $trends['startDate'] }}"
                            class="text-xs px-2.5 py-1.5 bg-white border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B092EE] text-slate-700 font-medium">
                        <span class="text-xs font-bold text-slate-400 px-1">s/d</span>
                        <input type="date" name="end_date" value="{{ $trends['endDate'] }}"
                            class="text-xs px-2.5 py-1.5 bg-white border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B092EE] text-slate-700 font-medium">
                    </div>

                    <button type="submit"
                        class="px-3.5 py-2 bg-[#B092EE] hover:bg-[#9B77EA] text-white font-extrabold text-xs rounded-xl transition duration-200 shadow-xs">
                        Filter
                    </button>

                    @if (request()->has('start_date'))
                        <a href="{{ route('dashboard') }}"
                            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Indicator Legend -->
            <div class="flex items-center gap-6 text-xs font-bold mb-4 pt-2 border-t border-slate-100">
                <span class="flex items-center gap-2 text-emerald-600">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block shadow-xs"></span> Pemasukan
                </span>
                <span class="flex items-center gap-2 text-rose-500">
                    <span class="w-3 h-3 rounded-full bg-rose-500 inline-block shadow-xs"></span> Pengeluaran
                </span>
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
                                    family: 'Plus Jakarta Sans, sans-serif'
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
                                    family: 'Plus Jakarta Sans, sans-serif'
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
