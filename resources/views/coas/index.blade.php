@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Chart of Accounts (COA)</h1>
            </div>
            <a href="{{ route('coas.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Insert</span>
            </a>
        </div>

        <!-- Notification Banner -->
        @if (session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-600 hover:text-emerald-900 text-base leading-none">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div
                class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-rose-600 hover:text-rose-900 text-base leading-none">&times;</button>
            </div>
        @endif

        <!-- Table Card Container -->
        <div class="bg-white rounded-2xl border border-[#E2DDD5] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-[#F8F6F0] border-b border-[#E2DDD5] text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Kode Akun</th>
                            <th class="py-3.5 px-5">Nama Akun</th>
                            <th class="py-3.5 px-5">Kategori</th>
                            <th class="py-3.5 px-5 text-center">Kas / Bank?</th>
                            <th class="py-3.5 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2DDD5] text-xs font-medium text-slate-800">
                        @forelse($coas as $coa)
                            <tr class="hover:bg-[#FBF9F5] transition">
                                <td class="py-3.5 px-5 font-mono font-bold text-indigo-700">{{ $coa->code }}</td>
                                <td class="py-3.5 px-5 font-bold text-slate-900">{{ $coa->name }}</td>
                                <td class="py-3.5 px-5">
                                    <span
                                        class="inline-block px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide rounded-md border 
                                        @if ($coa->type === 'asset') bg-emerald-50 text-emerald-700 border-emerald-200
                                        @elseif($coa->type === 'liability') bg-amber-50 text-amber-700 border-amber-200
                                        @elseif($coa->type === 'equity') bg-purple-50 text-purple-700 border-purple-200
                                        @elseif($coa->type === 'revenue') bg-blue-50 text-blue-700 border-blue-200
                                        @else bg-rose-50 text-rose-700 border-rose-200 @endif">
                                        {{ $coa->type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    @if ($coa->is_cash_bank)
                                        <span
                                            class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 uppercase">
                                            YES ({{ strtoupper($coa->sub_category ?? 'CASH') }})
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-center whitespace-nowrap space-x-1.5">
                                    <a href="{{ route('coas.edit', $coa->id) }}"
                                        class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg border border-amber-200 text-[11px] transition inline-block">Edit</a>

                                    <form action="{{ route('coas.destroy', $coa->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun COA ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg border border-rose-200 text-[11px] transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-500 font-semibold">Belum ada Chart of
                                    Accounts ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($coas, 'links'))
                <div class="p-4 border-t border-[#E2DDD5] bg-[#F8F6F0]">
                    {{ $coas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
