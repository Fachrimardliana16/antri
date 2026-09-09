<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Master Data Layanan</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola jenis layanan, kode nomor antrean, estimasi durasi, dan warna tema.</p>
        </div>

        <button type="button" wire:click="create" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Layanan</span>
        </button>
    </div>

    <!-- Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden shadow-2xl">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-900/80 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                <tr>
                    <th class="px-6 py-4">Kode & Warna</th>
                    <th class="px-6 py-4">Nama Layanan</th>
                    <th class="px-6 py-4">Target SLA</th>
                    <th class="px-6 py-4">Jumlah Loket</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
                @forelse($services as $srv)
                    <tr class="hover:bg-slate-900/40 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-white text-xs shadow-md" style="background: {{ $srv->color }};">
                                    {{ $srv->code }}
                                </span>
                                <span class="font-mono text-xs text-slate-400">Prefix: {{ $srv->prefix }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-white">{{ $srv->name }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $srv->description }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono">
                            {{ $srv->estimated_time_minutes }} Menit
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300">
                                {{ $srv->counters_count }} Loket
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button type="button" wire:click="toggleActive({{ $srv->id }})" class="cursor-pointer">
                                @if($srv->is_active)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Aktif</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-500">Nonaktif</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" wire:click="edit({{ $srv->id }})" class="text-blue-400 hover:text-blue-300 font-semibold text-xs">Edit</button>
                            <button type="button" wire:confirm="Yakin ingin menghapus layanan ini?" wire:click="delete({{ $srv->id }})" class="text-rose-400 hover:text-rose-300 font-semibold text-xs">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-500">Belum ada data layanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="glass-panel max-w-lg w-full rounded-3xl p-6 sm:p-8 border border-slate-700 shadow-2xl space-y-4">
                <h3 class="text-xl font-bold text-white">{{ $editingServiceId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}</h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Layanan</label>
                        <input type="text" wire:model="name" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="e.g. Layanan Kasir">
                        @error('name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Layanan</label>
                            <input type="text" wire:model="code" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white uppercase font-mono focus:outline-none focus:border-blue-500" placeholder="A">
                            @error('code') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Prefix Tiket</label>
                            <input type="text" wire:model="prefix" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white uppercase font-mono focus:outline-none focus:border-blue-500" placeholder="A">
                            @error('prefix') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Target SLA (Menit/Orang)</label>
                            <input type="number" wire:model="estimated_time_minutes" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Warna Badge</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" wire:model="color" class="w-10 h-10 rounded-lg bg-transparent border-0 cursor-pointer p-0">
                                <input type="text" wire:model="color" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi Layanan</label>
                        <textarea wire:model="description" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="Keterangan singkat layanan..."></textarea>
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" id="is_active_check" wire:model="is_active" class="rounded bg-slate-900 border-slate-700 text-blue-600">
                        <label for="is_active_check" class="text-xs font-semibold text-slate-300">Layanan Aktif</label>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg transition">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
