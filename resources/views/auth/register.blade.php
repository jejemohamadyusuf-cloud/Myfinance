<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MyFinance') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- 🟢 [KING COMMAND]: Tailwind CSS CDN Langsung Supaya Halaman Register & Login Pasti Ter-load CSS-nya -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8F6F0] text-slate-900 antialiased min-h-screen">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

        <!-- LEFT PANEL: Warm Beige Section (#F2EFE9) -->
        <div
            class="hidden lg:flex lg:col-span-6 xl:col-span-7 bg-[#F2EFE9] border-r border-[#E2DDD5] p-12 flex-col justify-between relative overflow-hidden">

            <div
                class="absolute inset-0 bg-[radial-gradient(#D5CFC4_1px,transparent_1px)] [background-size:24px_24px] opacity-40">
            </div>

            <!-- Brand Header -->
            <div class="relative z-10 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white text-base shadow-md shadow-indigo-600/20">
                    MF
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-slate-900">MyFinance</span>
            </div>

            <!-- Hero Feature -->
            <div class="relative z-10 max-w-xl my-auto py-12">
                <div
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold mb-6">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                    Financial Operations & Accounting System
                </div>

                <h2 class="text-4xl xl:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                    Kelola Keuangan Bisnis <br>
                    <span class="text-indigo-600">Presisi, Aman & Terstruktur.</span>
                </h2>

                <p class="text-slate-600 text-sm leading-relaxed mb-8 font-medium">
                    Sistem pencatatan double-entry otomatis, pengelolaan Kas & Bank terpisah, serta laporan jurnal
                    realtime yang dirancang profesional.
                </p>

                <!-- Mini Stat Badges -->
                <div class="grid grid-cols-3 gap-4 border-t border-[#E2DDD5] pt-6">
                    <div>
                        <div class="text-xl font-bold text-slate-900 font-mono">100%</div>
                        <div class="text-[11px] text-slate-600 font-bold uppercase tracking-wider mt-0.5">Double-Entry
                        </div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-slate-900 font-mono">Realtime</div>
                        <div class="text-[11px] text-slate-600 font-bold uppercase tracking-wider mt-0.5">Ledger Report
                        </div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-emerald-600 font-mono">Secure</div>
                        <div class="text-[11px] text-slate-600 font-bold uppercase tracking-wider mt-0.5">Multi Tenant
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="relative z-10 text-xs text-slate-600 font-semibold flex items-center justify-between border-t border-[#E2DDD5] pt-4">
                <span>&copy; {{ date('Y') }} MyFinance System.</span>
                <span>Enterprise Edition v2.0</span>
            </div>
        </div>

        <!-- RIGHT PANEL: Pure White Section (#FFFFFF) - Form Register / Login Slot -->
        <div class="lg:col-span-6 xl:col-span-5 flex items-center justify-center p-6 sm:p-12 bg-white relative">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>

    </div>

</body>

</html>
