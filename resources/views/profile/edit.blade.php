@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pengaturan Profile</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola informasi akun dan tim staff Anda</p>
        </div>

        <!-- Section 1: Informasi Profile Pribadi -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4">Informasi Akun</h2>
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 max-w-xl">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- Section 2: Kelola Tim Staff (Khusus Master & Super Admin) -->
        @if (auth()->check() && (auth()->user()->isMaster() || auth()->user()->isSuperAdmin()))
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                @include('profile.partials.manage-staff-form')
            </div>
        @endif
    </div>
@endsection
