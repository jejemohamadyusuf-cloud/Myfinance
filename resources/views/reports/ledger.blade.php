@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header & Filter -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">General Ledger Report</h1>
                <p class="text-sm text-slate-500 mt-0.5">Account mutation details and running balances</p>
            </div>

            <form method="GET" action="{{ route('reports.ledger') }}"
                class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Select Account</label>
                    <select name="coa_id"
                        class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Select COA --</option>
                        @foreach ($coas as $coaItem)
                            <option value="{{ $coaItem->id }}" {{ request('coa_id') == $coaItem->id ? 'selected' : '' }}>
                                {{ $coaItem->code }} - {{ $coaItem->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}"
                        class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-t')) }}"
                        class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="self-end">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs rounded-lg shadow-sm transition">
                        Show Ledger
                    </button>
                </div>
            </form>
        </div>

        @if (isset($ledger))
            <!-- Summary Cards -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Selected Account</span>
                    <div class="text-base font-bold text-slate-800 mt-1">{{ $ledger['coa']->code }} -
                        {{ $ledger['coa']->name }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Beginning Balance</span>
                    <div class="text-base font-bold text-slate-800 mt-1 font-mono">
                        Rp {{ number_format($ledger['initial_balance'], 2, ',', '.') }}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Ending Balance</span>
                    <div class="text-base font-bold text-emerald-600 mt-1 font-mono">
                        Rp {{ number_format($ledger['final_balance'], 2, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- Table Ledger -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <th class="py-3.5 px-4">DATE</th>
                                <th class="py-3.5 px-4">TRANSACTION NO</th>
                                <th class="py-3.5 px-4">DESCRIPTION</th>
                                <th class="py-3.5 px-4 text-right">DEBIT (RP)</th>
                                <th class="py-3.5 px-4 text-right">CREDIT (RP)</th>
                                <th class="py-3.5 px-4 text-right">BALANCE (RP)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            <!-- Beginning Balance -->
                            <tr class="bg-slate-50/50 font-medium italic">
                                <td class="py-3 px-4">{{ \Carbon\Carbon::parse($ledger['start_date'])->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-slate-400">-</td>
                                <td class="py-3 px-4">Beginning Balance</td>
                                <td class="py-3 px-4 text-right text-slate-400">-</td>
                                <td class="py-3 px-4 text-right text-slate-400">-</td>
                                <td class="py-3 px-4 text-right font-mono font-semibold text-slate-800">
                                    {{ number_format($ledger['initial_balance'], 2, ',', '.') }}
                                </td>
                            </tr>

                            <!-- Mutations -->
                            @forelse($ledger['mutations'] as $mutation)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($mutation['date'])->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-indigo-600 text-xs font-medium">
                                        {{ $mutation['transaction_number'] }}</td>
                                    <td class="py-3 px-4 text-slate-800">{{ $mutation['description'] ?? '-' }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-800">
                                        {{ $mutation['debit'] > 0 ? number_format($mutation['debit'], 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-800">
                                        {{ $mutation['credit'] > 0 ? number_format($mutation['credit'], 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-medium text-slate-900">
                                        {{ number_format($mutation['balance'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-400">No mutations recorded in this
                                        period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white p-12 rounded-xl border border-slate-200 text-center text-slate-400">
                Please select a COA account from the filter above to display the general ledger details.
            </div>
        @endif
    </div>
@endsection
