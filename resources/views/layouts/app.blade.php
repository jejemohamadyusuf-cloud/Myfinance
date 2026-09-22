<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MyFinance' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Vite Assets -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8F6F0] text-slate-900 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- OVERLAY MOBILE SIDEBAR -->
    <div id="sidebarBackdrop" onclick="toggleMobileSidebar()"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden md:hidden transition-opacity"></div>

    <!-- LEFT SIDEBAR (Soft Beige Card #F2EFE9 & Border #E2DDD5) -->
    <aside id="sidebarMenu"
        class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-[#F2EFE9] border-r border-[#E2DDD5] flex flex-col min-h-screen flex-shrink-0 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-sm">

        <!-- Logo / Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-[#E2DDD5]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white text-xs shadow-md shadow-indigo-600/20">
                    MF
                </div>
                <span class="font-extrabold text-lg tracking-tight text-slate-900">MyFinance</span>
            </a>
            <button onclick="toggleMobileSidebar()" class="md:hidden text-slate-500 hover:text-slate-900 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-grow p-4 space-y-1 overflow-y-auto">
            <div class="px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Main Menu
            </div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition 
                {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-700 hover:bg-white hover:text-indigo-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Master COA -->
            <a href="{{ route('coas.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition 
                {{ request()->routeIs('coas.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-700 hover:bg-white hover:text-indigo-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Master COA</span>
            </a>

            <!-- Cash Management -->
            <div class="pt-5 px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Cash Management
            </div>

            <a href="{{ route('cash.in.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition 
                {{ request()->routeIs('cash.in.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-700 hover:bg-white hover:text-indigo-600' }}">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span>Cash In</span>
            </a>

            <a href="{{ route('cash.out.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition 
                {{ request()->routeIs('cash.out.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-700 hover:bg-white hover:text-indigo-600' }}">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Cash Out</span>
            </a>

            <!-- Bank Management -->
            <div class="pt-5 px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Bank Management
            </div>

            <a href="{{ route('bank.in.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition 
                {{ request()->routeIs('bank.in.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-700 hover:bg-white hover:text-indigo-600' }}">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                </svg>
                <span>Bank In</span>
            </a>

            <a href="{{ route('bank.out.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition 
                {{ request()->routeIs('bank.out.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-700 hover:bg-white hover:text-indigo-600' }}">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Bank Out</span>
            </a>

            <!-- Reports -->
            <div class="pt-5 px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Financial Reports
            </div>

            <a href="{{ route('reports.journal') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition 
                {{ request()->routeIs('reports.journal') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-700 hover:bg-white hover:text-indigo-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>General Journal</span>
            </a>

            <a href="{{ route('reports.ledger') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition 
                {{ request()->routeIs('reports.ledger') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-700 hover:bg-white hover:text-indigo-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>General Ledger</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form" class="pt-6">
                @csrf
                <button type="button" onclick="handleLogout(this, 'sidebar-logout-form')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-rose-600 hover:bg-rose-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-grow flex flex-col min-h-screen overflow-x-hidden">

        <!-- Top Header Bar (Pure White) -->
        <header
            class="h-16 bg-white border-b border-[#E2DDD5] px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3">
                <button onclick="toggleMobileSidebar()"
                    class="md:hidden p-2 rounded-xl bg-[#F2EFE9] text-slate-700 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="text-sm font-extrabold text-slate-900 tracking-wide">
                    {{ $title ?? 'Financial System' }}
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="text-xs px-2.5 py-1 rounded-lg bg-[#F8F6F0] border border-[#E2DDD5] text-slate-600 font-mono hidden sm:inline-block font-semibold">
                    Tenant ID: {{ auth()->user()->id }}
                </span>
                <div
                    class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

        <footer
            class="bg-white border-t border-[#E2DDD5] py-4 px-6 text-xs text-slate-500 font-semibold flex justify-between items-center">
            <span>&copy; {{ date('Y') }} MyFinance System</span>
            <span class="font-mono text-[10px] text-slate-400">Light Theme Enterprise</span>
        </footer>

    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        function handleLogout(element, formId) {
            if (element.getAttribute('data-submitting') === 'true') return false;
            element.setAttribute('data-submitting', 'true');
            element.style.pointerEvents = 'none';
            element.style.opacity = '0.5';
            element.innerText = 'Logging out...';
            document.getElementById(formId).submit();
        }
    </script>

</body>

</html>
