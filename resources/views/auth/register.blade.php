<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - MyFinance</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body
    class="bg-[#C5D3F8] text-slate-800 antialiased min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden">

    <div
        class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden relative z-10 border border-white/60 my-6">

        <!-- TOP PANEL -->
        <div class="bg-[#EBF1FF] p-6 text-center border-b border-blue-100/60">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-[#2D3B6B] flex items-center justify-center font-extrabold text-white text-xs shadow-sm">
                        MF
                    </div>
                    <span class="text-base font-extrabold tracking-wider text-[#2D3B6B]">MYFINANCE</span>
                </div>

                <a href="{{ route('login') }}"
                    class="px-3.5 py-1.5 bg-[#B092EE] hover:bg-[#9B77EA] text-white text-xs font-bold rounded-lg transition shadow-sm">
                    Sudah Ada Akun
                </a>
            </div>
        </div>

        <!-- FORM PANEL -->
        <div class="p-6 sm:p-8">
            <div class="text-center mb-6">
                <h2 class="text-xl font-extrabold text-[#2D3B6B] uppercase tracking-wider">BUAT AKUN BARU</h2>
                <p class="text-xs font-semibold text-slate-400 mt-1">Daftarkan bisnis Anda di MyFinance</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Name Input -->
                <div>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Nama Lengkap"
                        class="w-full px-4 py-2.5 bg-[#E8EEFF] border border-transparent focus:border-[#B092EE] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none transition shadow-inner">
                    @error('name')
                        <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Input -->
                <div>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Alamat Email"
                        class="w-full px-4 py-2.5 bg-[#E8EEFF] border border-transparent focus:border-[#B092EE] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none transition shadow-inner">
                    @error('email')
                        <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <input type="password" name="password" required placeholder="Kata Sandi"
                        class="w-full px-4 py-2.5 bg-[#E8EEFF] border border-transparent focus:border-[#B092EE] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none transition shadow-inner">
                    @error('password')
                        <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <input type="password" name="password_confirmation" required placeholder="Konfirmasi Kata Sandi"
                        class="w-full px-4 py-2.5 bg-[#E8EEFF] border border-transparent focus:border-[#B092EE] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none transition shadow-inner">
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-[#B092EE] hover:bg-[#9B77EA] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition duration-200">
                        DAFTAR SEKARANG
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>

</html>
