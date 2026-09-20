<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MyFinance' }}</title>

    <!-- CDN Tailwind Bawaan -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex">

    <!-- LEFT SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col min-h-screen border-r border-slate-800 flex-shrink-0">
        <!-- Logo / Brand Header -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div
                    class="w-8 h-8 bg-indigo-700 rounded-lg flex items-center justify-center font-bold text-white text-sm shadow-sm">
                    JH
                </div>
                <span class="font-semibold text-lg tracking-wide text-white">MyFinance</span>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-grow p-4 space-y-1">
            <div class="px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Main Menu
            </div>

            <!-- Master Coa -->
            <a href="{{ route('coas.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('coas.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <span>Master COA</span>
            </a>

            <div class="pt-6 px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Cash Management
            </div>

            <a href="{{ route('cash.in.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('cash.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <span>Cash In</span>
            </a>

            <a href="{{ route('cash.out.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('cash.out.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <span>Cash Out</span>
            </a>

            <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Bank Management
            </div>

            <a href="{{ route('bank.in.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('bank.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <span>Bank In</span>
            </a>

            <a href="{{ route('bank.out.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('bank.out.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <span>Bank Out</span>
            </a>

            <!-- Financial Reports Section -->
            <div class="pt-6 px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Financial Reports
            </div>

            <!-- General Journal -->
            <a href="{{ route('reports.journal') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('reports.journal') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <span>General Journal</span>
            </a>

            <!-- General Ledger -->
            <a href="{{ route('reports.ledger') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('reports.ledger') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <span>General Ledger</span>
            </a>

            <div class="pt-6 px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                System & Settings
            </div>

            @if (auth()->check() && (auth()->user()->isMaster() || auth()->user()->isSuperAdmin()))
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                    {{ request()->routeIs('profile.edit') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span>Profile & Tim Staff</span>
                </a>
            @else
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition 
                    {{ request()->routeIs('profile.edit') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span>Profile Settings</span>
                </a>
            @endif

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form" class="pt-2">
                @csrf
                <button type="button" onclick="handleLogout(this, 'sidebar-logout-form')"
                    class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium text-rose-500 hover:bg-slate-800 hover:text-rose-400 transition">
                    Logout ({{ auth()->user()->name }})
                </button>
            </form>
        </nav>

        <!-- API Endpoint Badge Shortcut -->
        <div class="p-4 border-t border-slate-800">
            <a href="{{ url('/api/v1/transactions') }}" target="_blank"
                class="w-full px-3 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-indigo-300 border border-slate-700/60 transition flex items-center justify-between text-xs font-mono">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-semibold">API Endpoint</span>
                </div>
                <span
                    class="text-[10px] bg-indigo-900/80 text-indigo-200 px-1.5 py-0.5 rounded border border-indigo-700">JSON</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-grow flex flex-col min-h-screen overflow-x-hidden">

        <!-- Header Info -->
        <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between">
            <div class="text-sm font-medium text-slate-500">
                Financial Management System
            </div>
            <div class="text-xs text-slate-400 font-mono">
                Env: {{ app()->environment() }}
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-grow p-8">
            @if (session('success'))
                <div
                    class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium flex justify-between items-center shadow-sm">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm shadow-sm space-y-2">
                    <div class="flex items-center gap-2 font-bold text-rose-900">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Terjadi kesalahan pada input data:</span>
                    </div>
                    <ul class="list-disc pl-9 text-xs text-rose-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-8 text-xs text-slate-400 flex justify-between">
            <span>&copy; {{ date('Y') }} MyFinance. All rights reserved.</span>
            <span>Laravel v{{ app()->version() }}</span>
        </footer>

    </div>

    <!-- Global Handler Logout -->
    <script>
        function handleLogout(element, formId) {
            if (element.getAttribute('data-submitting') === 'true') {
                return false;
            }

            element.setAttribute('data-submitting', 'true');
            element.style.pointerEvents = 'none';
            element.style.opacity = '0.5';
            element.innerText = 'Logging out...';

            document.getElementById(formId).submit();
        }
    </script>

</body>

</html>
