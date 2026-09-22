<x-guest-layout>
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex lg:hidden items-center gap-3 mb-6">
            <div
                class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white text-sm shadow-md">
                MF
            </div>
            <span class="text-xl font-extrabold text-slate-900 tracking-tight">MyFinance</span>
        </div>

        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Baru</h2>
        <p class="text-xs font-semibold text-slate-600 mt-2">Buat akun untuk mulai mengelola keuangan bisnis Anda</p>
    </div>

    <form method="POST" action="{{ route('register') }}" autocomplete="off" class="space-y-5">
        @csrf

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                placeholder="John Doe" autocomplete="name"
                class="w-full px-4 py-3 bg-[#FBF9F5] border @error('name') border-rose-500 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 font-semibold placeholder-slate-400 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition shadow-sm">
            @error('name')
                <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                placeholder="nama@perusahaan.com" autocomplete="username"
                class="w-full px-4 py-3 bg-[#FBF9F5] border @error('email') border-rose-500 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 font-semibold placeholder-slate-400 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition shadow-sm">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <input type="password" id="password" name="password" required placeholder="••••••••"
                autocomplete="new-password"
                class="w-full px-4 py-3 bg-[#FBF9F5] border @error('password') border-rose-500 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 font-semibold placeholder-slate-400 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition shadow-sm">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation"
                class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                placeholder="••••••••" autocomplete="new-password"
                class="w-full px-4 py-3 bg-[#FBF9F5] border @error('password_confirmation') border-rose-500 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 font-semibold placeholder-slate-400 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition shadow-sm">
            @error('password_confirmation')
                <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Register & Login Redirect -->
        <div class="flex items-center justify-between pt-2">
            <a class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition" href="{{ route('login') }}">
                Sudah punya akun? <span class="text-indigo-600 font-bold hover:underline">Masuk</span>
            </a>

            <button type="submit"
                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md shadow-indigo-600/20 transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Daftar Sekarang
            </button>
        </div>
    </form>
</x-guest-layout>
