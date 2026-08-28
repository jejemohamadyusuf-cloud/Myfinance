<section class="space-y-6">
    <header class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Kelola Tim Staff</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tambah dan kelola hak akses akun Staff Anda</p>
        </div>
        <button type="button" onclick="document.getElementById('add-staff-form').classList.toggle('hidden')"
            class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
            + Tambah Staff Baru
        </button>
    </header>

    <!-- Form Tambah Staff -->
    <div id="add-staff-form" class="hidden p-4 bg-slate-50 rounded-xl border border-slate-200">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Pendaftaran Staff Baru</h3>
        <form action="{{ route('profile.staff.store') }}" method="POST" class="space-y-3">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required
                        class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                    <input type="email" name="email" required
                        class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-staff-form').classList.add('hidden')"
                    class="px-3 py-1.5 bg-slate-200 text-slate-700 text-xs rounded-lg font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg font-semibold hover:bg-indigo-700">
                    Simpan Staff
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Staff -->
    <div class="overflow-x-auto border border-slate-200 rounded-xl">
        <table class="w-full text-left text-sm">
            <thead
                class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200 uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-4 py-3">Nama Staff</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-slate-700">
                @forelse(auth()->user()->staffMembers as $staff)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $staff->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $staff->email }}</td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('profile.staff.toggle', $staff->id) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('PATCH')
                                @if ((bool) $staff->is_active)
                                    <button type="submit"
                                        onclick="return confirm('Are you sure deactivate this account ?')"
                                        class="text-xs text-rose-600 hover:text-rose-800 font-semibold">
                                        Deactivate
                                    </button>
                                @else
                                    <button type="submit"
                                        class="px-2 py-1 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded text-xs font-semibold">Active</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400 text-xs">
                            Belum ada akun staff terdaftar di bawah akun Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
