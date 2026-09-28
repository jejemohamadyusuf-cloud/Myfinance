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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#C5D3F8] text-slate-800 antialiased min-h-screen">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

        <!-- PANEL KIRI: Soft Periwinkle Panel (#EBF1FF) -->
        <div
            class="hidden lg:flex lg:col-span-6 xl:col-span-7 bg-[#EBF1FF] border-r border-[#D0DEFF] p-12 flex-col justify-between relative overflow-hidden">

            <!-- Radial Grid Accent -->
            <div
                class="absolute inset-0 bg-[radial-gradient(#C5D3F8_1px,transparent_1px)] [background-size:24px_24px] opacity-60">
            </div>

            <!-- Brand Header -->
            <div class="relative z-10 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-[#2D3B6B] flex items-center justify-center font-extrabold text-white text-base shadow-md">
                    MF
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-[#2D3B6B]">MyFinance</span>
            </div>

            <!-- Hero Feature -->
            <div class="relative z-10 max-w-xl my-auto py-12">
                <div
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/80 border border-blue-200/60 text-[#2D3B6B] text-xs font-bold mb-6 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#B092EE] animate-pulse"></span>
                    Financial Operations & Accounting System
                </div>

                <h2 class="text-4xl xl:text-5xl font-extrabold text-[#2D3B6B] tracking-tight leading-tight mb-4">
                    Kelola Keuangan Bisnis <br>
                    <span class="text-[#B092EE]">Presisi, Aman & Terstruktur.</span>
                </h2>

                <p class="text-slate-600 text-sm leading-relaxed mb-8 font-medium">
                    Sistem pencatatan double-entry otomatis, pengelolaan Kas & Bank terpisah, serta laporan jurnal
                    realtime yang dirancang profesional.
                </p>

                <!-- Mini Stat Badges -->
                <div class="grid grid-cols-3 gap-4 border-t border-[#D0DEFF] pt-6">
                    <div>
                        <div class="text-xl font-bold text-[#2D3B6B] font-mono">100%</div>
                        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">Double-Entry
                        </div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-[#2D3B6B] font-mono">Realtime</div>
                        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">Ledger Report
                        </div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-emerald-600 font-mono">Secure</div>
                        <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">Multi Tenant
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="relative z-10 text-xs text-slate-500 font-semibold flex items-center justify-between border-t border-[#D0DEFF] pt-4">
                <span>&copy; {{ date('Y') }} MyFinance System.</span>
                <span>Enterprise Edition v2.0</span>
            </div>
        </div>

        <!-- PANEL KANAN: Form Slot Container (#FFFFFF) -->
        <div class="lg:col-span-6 xl:col-span-5 flex items-center justify-center p-6 sm:p-12 bg-white relative">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>

    </div>

</body>

</html>
