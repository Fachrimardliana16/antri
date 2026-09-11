<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--text-primary);">Manajemen Akun Pengguna</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-muted);">Kelola akun Super Admin, Admin Supervisor, dan Operator Loket.</p>
        </div>
        <button type="button" wire:click="create"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                style="background-color: var(--primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Tambah Pengguna
        </button>
    </div>

    {{-- Table --}}
    <div class="gov-card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="border-b" style="background-color: var(--bg-elevated); border-color: var(--border-default);">
                <tr>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Nama Lengkap</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Email</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Peran (Role)</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Loket</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--border-light);">
                @foreach($users as $u)
                    <tr class="transition"
                        onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                        onmouseout="this.style.backgroundColor=''">
                        <td class="px-5 py-4 font-semibold" style="color: var(--text-primary);">{{ $u->name }}</td>
                        <td class="px-5 py-4 font-mono text-xs" style="color: var(--text-muted);">{{ $u->email }}</td>
                        <td class="px-5 py-4">
                            @if($u->role === 'super_admin')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Super Admin</span>
                            @elseif($u->role === 'admin')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Admin Supervisor</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Operator</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm" style="color: var(--text-secondary);">
                            {{ $u->assignedCounter ? $u->assignedCounter->name : '—' }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button" wire:click="edit({{ $u->id }})"
                                    class="text-sm font-medium mr-3 transition"
                                    style="color: var(--primary);"
                                    onmouseover="this.style.opacity='0.8'"
                                    onmouseout="this.style.opacity='1'">Edit</button>
                            @if($u->id !== auth()->id())
                                <button type="button" wire:confirm="Hapus akun pengguna ini?" wire:click="delete({{ $u->id }})"
                                        class="text-sm font-medium text-red-500 hover:text-red-600 transition">Hapus</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 animate-fade-in">
            <div class="gov-panel max-w-md w-full p-6 space-y-4">
                <h3 class="text-base font-bold" style="color: var(--text-primary);">{{ $editingUserId ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru' }}</h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Nama Lengkap</label>
                        <input type="text" wire:model="name"
                               class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                               style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                               placeholder="e.g. Budi Santoso">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Alamat Email</label>
                        <input type="email" wire:model="email"
                               class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                               style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                               placeholder="budi@instansi.go.id">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Peran (Role)</label>
                            <select wire:model="role"
                                    class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                                <option value="operator">Operator Loket</option>
                                <option value="admin">Admin Supervisor</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Loket Utama</label>
                            <select wire:model="assigned_counter_id"
                                    class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                                <option value="">-- Bebas --</option>
                                @foreach($counters as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">
                            Password {{ $editingUserId ? '(Kosongkan jika tidak diubah)' : '' }}
                        </label>
                        <input type="password" wire:model="password"
                               class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                               style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                               placeholder="••••••••">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t" style="border-color: var(--border-light);">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition"
                                style="color: var(--text-secondary);"
                                onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                                onmouseout="this.style.backgroundColor=''">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                                style="background-color: var(--primary);">
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
