@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Chart of Accounts (COA)</h1>
            </div>
            <a href="{{ route('coas.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                Insert
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                        <th class="py-3.5 px-4">Code</th>
                        <th class="py-3.5 px-4">Account Name</th>
                        <th class="py-3.5 px-4">Category Type</th>
                        <th class="py-3.5 px-4 text-center">Cash/Bank?</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($coas as $coa)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-indigo-600">{{ $coa->code }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $coa->name }}</td>
                            <td class="py-3 px-4">
                                <span
                                    class="px-2.5 py-1 text-[11px] font-semibold uppercase rounded-full 
                                @if ($coa->type === 'asset') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($coa->type === 'liability') bg-amber-50 text-amber-700 border border-amber-200
                                @elseif($coa->type === 'equity') bg-purple-50 text-purple-700 border border-purple-200
                                @elseif($coa->type === 'revenue') bg-blue-50 text-blue-700 border border-blue-200
                                @else bg-rose-50 text-rose-700 border border-rose-200 @endif">
                                    {{ $coa->type }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($coa->is_cash_bank)
                                    <span
                                        class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">YES</span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('coas.edit', $coa->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Edit</a>
                                <form action="{{ route('coas.destroy', $coa->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this COA?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-rose-600 hover:text-rose-900 font-semibold text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">No Chart of Accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Links Paginasi -->
        <div class="mt-4">
            {{ $coas->links() }}
        </div>
    </div>
@endsection
