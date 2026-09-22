<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MyFinance</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS Direct CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8F6F0] text-slate-900 antialiased min-h-screen">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

        <!-- PANEL KIRI: Warm Beige Section (#F2EFE9) -->
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
                <!-- 🟢 [COLOR FIX]: Hitam Pekat Slate-900 -->
                <span class="text-2xl font-extrabold tracking-tight text-slate-900">MyFinance</span>
            </div>

            <!-- Hero Feature -->
            <div class="relative z-10 max-w-xl my-auto py-12">
                <div
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold mb-6">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                    Financial Operations & Accounting System
                </div>

                <!-- 🟢 [COLOR FIX]: Judul Teks Slate-900 & Highlight Indigo-600 -->
                <h2 class="text-4xl xl:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                    Kelola Keuangan Bisnis <br>
                    <span class="text-indigo-600">Presisi, Aman & Terstruktur.</span>
                </h2>

                <!-- 🟢 [COLOR FIX]: Deskripsi Slate-600 (Kontras Pas di Atas Beige) -->
                <p class="text-slate-600 text-sm leading-relaxed mb-8 font-medium">
                    Sistem pencatatan double-entry otomatis, pengelolaan Kas & Bank terpisah, serta laporan jurnal
                    realtime yang dirancang profesional.
                </p>

                <!-- Mini Stat Badges -->
                <div class="grid grid-cols-3 gap-4 border-t border-[#E2DDD5] pt-6">
                    <div>
                        <!-- 🟢 [COLOR FIX]: Angka Hitam Tegas Slate-900 -->
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

        <!-- PANEL KANAN: Pure White Section (#FFFFFF) -->
        <div class="lg:col-span-6 xl:col-span-5 flex items-center justify-center p-6 sm:p-12 bg-white relative">
            <div class="w-full max-w-md">

                <!-- Header Form -->
                <div class="mb-8">
                    <div class="flex lg:hidden items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white text-sm shadow-md">
                            MF
                        </div>
                        <span class="text-xl font-extrabold text-slate-900 tracking-tight">MyFinance</span>
                    </div>

                    <!-- 🟢 [COLOR FIX]: Judul Teks Hitam Slate-900 -->
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang</h2>
                    <!-- 🟢 [COLOR FIX]: Sub-Teks Slate-600 -->
                    <p class="text-xs font-semibold text-slate-600 mt-2">Silakan masuk ke akun Anda untuk melanjutkan
                    </p>
                </div>

                <!-- Alert Status Session -->
                @if (session('status'))
                    <div
                        class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-semibold">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" autocomplete="off"
                    onsubmit="disableLoginSubmitButton(this)" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <!-- 🟢 [COLOR FIX]: Label Teks Slate-800 Bold -->
                        <label for="email"
                            class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <!-- 🟢 [COLOR FIX]: Teks Input Slate-900 & Placeholder Slate-500 -->
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autofocus placeholder="nama@perusahaan.com" autocomplete="username"
                            class="w-full px-4 py-3 bg-[#FBF9F5] border @error('email') border-rose-500 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 font-semibold placeholder-slate-500 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition shadow-sm">
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password"
                                class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                                    Lupa sandi?
                                </a>
                            @endif
                        </div>

                        <input type="password" id="password" name="password" required placeholder="••••••••"
                            autocomplete="current-password"
                            class="w-full px-4 py-3 bg-[#FBF9F5] border @error('password') border-rose-500 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 font-semibold placeholder-slate-500 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition shadow-sm">
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Register Redirect -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-slate-300 bg-slate-50 text-indigo-600 focus:ring-indigo-500/30 w-4 h-4">
                            <span
                                class="ms-2 text-xs text-slate-700 group-hover:text-slate-900 font-semibold transition">Ingat
                                perangkat ini</span>
                        </label>

                        @if (Route::has('register'))
                            <a class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition"
                                href="{{ route('register') }}">
                                Belum punya akun? <span class="text-indigo-600 font-bold hover:underline">Daftar</span>
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" id="loginBtn"
                            class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md shadow-indigo-600/20 transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Masuk ke Aplikasi
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        function disableLoginSubmitButton(form) {
            const btn = form.querySelector('#loginBtn');
            if (btn.dataset.submitted === "true") {
                event.preventDefault();
                return false;
            }

            btn.dataset.submitted = "true";
            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');
            btn.innerText = 'Memproses Masuk...';
        }
    </script>
</body>

</html>
