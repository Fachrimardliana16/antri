<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--text-primary);">Master Data Layanan</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-muted);">Kelola jenis layanan, kode nomor antrean, estimasi durasi, dan warna tema.</p>
        </div>
        <button type="button" wire:click="create"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                style="background-color: var(--primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Layanan
        </button>
    </div>

    {{-- Table --}}
    <div class="gov-card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="border-b" style="background-color: var(--bg-elevated); border-color: var(--border-default);">
                <tr>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Kode & Warna</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Nama Layanan</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Target SLA</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Loket</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Status</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--border-light);">
                @forelse($services as $srv)
                    <tr class="transition" style="color: var(--text-primary);"
                        onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                        onmouseout="this.style.backgroundColor=''">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white text-xs"
                                      style="background-color: {{ $srv->color }};">
                                    {{ $srv->code }}
                                </span>
                                <span class="text-xs font-mono" style="color: var(--text-muted);">{{ $srv->prefix }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold" style="color: var(--text-primary);">{{ $srv->name }}</div>
                            <div class="text-xs mt-0.5" style="color: var(--text-muted);">{{ $srv->description }}</div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm" style="color: var(--text-secondary);">{{ $srv->estimated_time_minutes }} mnt</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 rounded text-xs font-medium"
                                  style="background-color: var(--bg-hover); color: var(--text-secondary);">
                                {{ $srv->counters_count }} Loket
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <button type="button" wire:click="toggleActive({{ $srv->id }})">
                                @if($srv->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background-color: var(--bg-hover); color: var(--text-muted);">Nonaktif</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button" wire:click="edit({{ $srv->id }})"
                                    class="text-sm font-medium mr-3 transition"
                                    style="color: var(--primary);"
                                    onmouseover="this.style.opacity='0.8'"
                                    onmouseout="this.style.opacity='1'">Edit</button>
                            <button type="button" wire:confirm="Yakin ingin menghapus layanan ini?" wire:click="delete({{ $srv->id }})"
                                    class="text-sm font-medium text-red-500 hover:text-red-600 transition">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-sm" style="color: var(--text-muted);">Belum ada data layanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 animate-fade-in">
            <div class="gov-panel max-w-lg w-full p-6 space-y-4">
                <h3 class="text-base font-bold" style="color: var(--text-primary);">{{ $editingServiceId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}</h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Nama Layanan</label>
                        <input type="text" wire:model="name"
                               class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                               style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default); focus:border-color: var(--primary);"
                               placeholder="e.g. Layanan Kasir">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Kode Layanan</label>
                            <input type="text" wire:model="code"
                                   class="w-full border rounded-lg px-3.5 py-2.5 text-sm uppercase font-mono focus:outline-none focus:ring-1"
                                   style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                                   placeholder="A">
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Prefix Tiket</label>
                            <input type="text" wire:model="prefix"
                                   class="w-full border rounded-lg px-3.5 py-2.5 text-sm uppercase font-mono focus:outline-none focus:ring-1"
                                   style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                                   placeholder="A">
                            @error('prefix') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Target SLA (Menit/Orang)</label>
                            <input type="number" wire:model="estimated_time_minutes"
                                   class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                                   style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Warna Badge</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model="color" class="w-10 h-10 rounded-lg border cursor-pointer p-0.5"
                                       style="border-color: var(--border-default); background-color: var(--bg-surface);">
                                <input type="text" wire:model="color"
                                       class="flex-1 border rounded-lg px-3 py-2 text-xs font-mono focus:outline-none focus:ring-1"
                                       style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Deskripsi Layanan</label>
                        <textarea wire:model="description" rows="2"
                                  class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                                  style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                                  placeholder="Keterangan singkat layanan..."></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_active_check" wire:model="is_active"
                               class="w-4 h-4 border-gray-300 rounded focus:ring-blue-500"
                               style="accent-color: var(--primary);">
                        <label for="is_active_check" class="text-sm" style="color: var(--text-secondary);">Layanan Aktif</label>
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
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
