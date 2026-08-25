@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-slate-600 text-sm">
                Role Anda: <span class="font-semibold uppercase text-indigo-600">{{ auth()->user()->role }}</span>
            </p>

            <!-- Container Konten Dashboard (Kosong Dulu) -->
            <div class="mt-8 border-2 border-dashed border-slate-200 rounded-lg p-12 text-center text-slate-400">
                Konten Dashboard Akuntansi akan ditampilkan di sini.
            </div>
        </div>
    </div>
@endsection
