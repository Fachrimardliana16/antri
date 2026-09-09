<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Akun Pengguna</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola akun Super Admin, Admin Supervisor, dan Operator Loket.</p>
        </div>

        <button type="button" wire:click="create" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden shadow-2xl">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-900/80 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                <tr>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Peran (Role)</th>
                    <th class="px-6 py-4">Loket Ditugaskan</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
                @foreach($users as $u)
                    <tr class="hover:bg-slate-900/40 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-white">{{ $u->name }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono text-slate-400">
                            {{ $u->email }}
                        </td>
                        <td class="px-6 py-4">
                            @if($u->role === 'super_admin')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">Super Admin</span>
                            @elseif($u->role === 'admin')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Admin Supervisor</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Operator Loket</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($u->assignedCounter)
                                <span class="font-semibold text-cyan-400">{{ $u->assignedCounter->name }}</span>
                            @else
                                <span class="text-xs text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" wire:click="edit({{ $u->id }})" class="text-blue-400 hover:text-blue-300 font-semibold text-xs">Edit</button>
                            @if($u->id !== auth()->id())
                                <button type="button" wire:confirm="Hapus akun pengguna ini?" wire:click="delete({{ $u->id }})" class="text-rose-400 hover:text-rose-300 font-semibold text-xs">Hapus</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="glass-panel max-w-md w-full rounded-3xl p-6 sm:p-8 border border-slate-700 shadow-2xl space-y-4">
                <h3 class="text-xl font-bold text-white">{{ $editingUserId ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru' }}</h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="e.g. Budi Santoso">
                        @error('name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Email</label>
                        <input type="email" wire:model="email" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="budi@antri.local">
                        @error('email') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Peran (Role)</label>
                            <select wire:model="role" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                                <option value="operator">Operator Loket</option>
                                <option value="admin">Admin Supervisor</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Loket Utama</label>
                            <select wire:model="assigned_counter_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                                <option value="">-- Bebas --</option>
                                @foreach($counters as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Password {{ $editingUserId ? '(Kosongkan jika tidak diubah)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="••••••••">
                        @error('password') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg transition">
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
