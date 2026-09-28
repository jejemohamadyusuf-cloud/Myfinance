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

<body
    class="bg-[#C5D3F8] text-slate-800 antialiased min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden">

    <!-- Background Wave Accent -->
    <div class="fixed -top-24 -left-24 w-96 h-96 bg-blue-300/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-24 -right-24 w-96 h-96 bg-purple-300/40 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Card Container -->
    <div
        class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden relative z-10 border border-white/60 my-6">

        <!-- TOP PANEL: Custom Vector Illustration & Branding -->
        <div class="bg-[#EBF1FF] p-6 text-center relative border-b border-blue-100/60">
            <!-- Header Bar (Logo & Register Button) -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-[#2D3B6B] flex items-center justify-center font-extrabold text-white text-xs shadow-sm">
                        MF
                    </div>
                    <span class="text-base font-extrabold tracking-wider text-[#2D3B6B]">MYFINANCE</span>
                </div>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="px-3.5 py-1.5 bg-[#B092EE] hover:bg-[#9B77EA] text-white text-xs font-bold rounded-lg transition shadow-sm">
                        Buat Akun
                    </a>
                @endif
            </div>

            <!-- 🟢 Pemanggilan Component Blade Laptop Animasi 🟢 -->
            <x-animated-laptop />

            <p class="text-[11px] text-slate-500 font-medium max-w-xs mx-auto">
                Sistem Pengelolaan Keuangan Bisnis Presisi, Aman & Terstruktur.
            </p>
        </div>

        <!-- BOTTOM PANEL: Form Section -->
        <div class="p-6 sm:p-8">
            <div class="text-center mb-6">
                <h2 class="text-xl font-extrabold text-[#2D3B6B] uppercase tracking-wider">LOGIN PENGGUNA</h2>
            </div>

            @if (session('status'))
                <div
                    class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-semibold text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" autocomplete="off"
                onsubmit="disableLoginSubmitButton(this)" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="Email atau Username"
                        class="w-full pl-10 pr-4 py-2.5 bg-[#E8EEFF] border border-transparent focus:border-[#B092EE] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none transition shadow-inner">
                    @error('email')
                        <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" id="password" name="password" required placeholder="Kata Sandi"
                        class="w-full pl-10 pr-4 py-2.5 bg-[#E8EEFF] border border-transparent focus:border-[#B092EE] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none transition shadow-inner">
                    @error('password')
                        <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between text-[11px] font-semibold pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="rounded border-slate-300 text-[#B092EE] focus:ring-[#B092EE] w-3.5 h-3.5">
                        <span class="ms-1.5 text-slate-600">Ingat Saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[#2D3B6B] hover:underline font-bold">
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" id="loginBtn"
                        class="w-full py-2.5 px-4 bg-[#B092EE] hover:bg-[#9B77EA] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition duration-200">
                        LOGIN
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        function disableLoginSubmitButton(form) {
            const btn = form.querySelector('#loginBtn');
            if (btn.dataset.submitted === "true") return false;
            btn.dataset.submitted = "true";
            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');
            btn.innerText = 'MEMPROSES...';
        }
    </script>
</body>

</html>
