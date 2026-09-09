<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Master Data Layanan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola jenis layanan, kode nomor antrean, estimasi durasi, dan warna tema.</p>
        </div>
        <button type="button" wire:click="create"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                style="background-color: var(--primary, #1a56a8);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Layanan
        </button>
    </div>

    {{-- Table --}}
    <div class="gov-card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Kode & Warna</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Nama Layanan</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Target SLA</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Loket</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($services as $srv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white text-xs"
                                      style="background-color: {{ $srv->color }};">
                                    {{ $srv->code }}
                                </span>
                                <span class="text-xs text-gray-400 font-mono">{{ $srv->prefix }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-900">{{ $srv->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $srv->description }}</div>
                        </td>
                        <td class="px-5 py-4 text-gray-700 font-mono text-sm">{{ $srv->estimated_time_minutes }} mnt</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $srv->counters_count }} Loket</span>
                        </td>
                        <td class="px-5 py-4">
                            <button type="button" wire:click="toggleActive({{ $srv->id }})">
                                @if($srv->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Nonaktif</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button" wire:click="edit({{ $srv->id }})"
                                    class="text-blue-600 hover:text-blue-700 text-sm font-medium mr-3">Edit</button>
                            <button type="button" wire:confirm="Yakin ingin menghapus layanan ini?" wire:click="delete({{ $srv->id }})"
                                    class="text-red-500 hover:text-red-600 text-sm font-medium">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400 text-sm">Belum ada data layanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 animate-fade-in">
            <div class="gov-panel max-w-lg w-full p-6 space-y-4">
                <h3 class="text-base font-bold text-gray-900">{{ $editingServiceId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}</h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Layanan</label>
                        <input type="text" wire:model="name"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               placeholder="e.g. Layanan Kasir">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Layanan</label>
                            <input type="text" wire:model="code"
                                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 uppercase font-mono focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                   placeholder="A">
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Prefix Tiket</label>
                            <input type="text" wire:model="prefix"
                                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 uppercase font-mono focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                   placeholder="A">
                            @error('prefix') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Target SLA (Menit/Orang)</label>
                            <input type="number" wire:model="estimated_time_minutes"
                                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Warna Badge</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model="color" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                                <input type="text" wire:model="color"
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-xs text-gray-900 font-mono focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Layanan</label>
                        <textarea wire:model="description" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                  placeholder="Keterangan singkat layanan..."></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_active_check" wire:model="is_active"
                               class="w-4 h-4 border-gray-300 rounded text-blue-600 focus:ring-blue-500">
                        <label for="is_active_check" class="text-sm text-gray-700">Layanan Aktif</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                                style="background-color: var(--primary, #1a56a8);">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
