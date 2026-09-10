<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Pengumuman TV Monitor</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola informasi yang ditampilkan di sidebar TV monitor</p>
        </div>
        <button wire:click="openCreateModal"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengumuman
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Announcements List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Konten</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Warna</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($announcements as $announcement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-gray-900">{{ $announcement->order }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $announcement->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 line-clamp-2">{{ $announcement->content }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                                {{ $announcement->icon_color === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $announcement->icon_color === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $announcement->icon_color === 'amber' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $announcement->icon_color === 'red' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $announcement->icon_color === 'purple' ? 'bg-purple-100 text-purple-700' : '' }}">
                                <span class="w-2 h-2 rounded-full
                                    {{ $announcement->icon_color === 'blue' ? 'bg-blue-500' : '' }}
                                    {{ $announcement->icon_color === 'green' ? 'bg-green-500' : '' }}
                                    {{ $announcement->icon_color === 'amber' ? 'bg-amber-500' : '' }}
                                    {{ $announcement->icon_color === 'red' ? 'bg-red-500' : '' }}
                                    {{ $announcement->icon_color === 'purple' ? 'bg-purple-500' : '' }}"></span>
                                {{ ucfirst($announcement->icon_color) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleActive({{ $announcement->id }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition
                                    {{ $announcement->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $announcement->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $announcement->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEditModal({{ $announcement->id }})"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $announcement->id }})"
                                        onclick="return confirm('Yakin hapus pengumuman ini?')"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-sm font-semibold">Belum ada pengumuman</p>
                                <p class="text-xs mt-1">Klik tombol "Tambah Pengumuman" untuk membuat yang baru</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click="closeModal">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6" @click.stop>
                <h3 class="text-xl font-bold text-gray-900 mb-4">
                    {{ $editingId ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
                        <input type="text" wire:model="title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: Perhatian">
                        @error('title') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Content --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Konten</label>
                        <textarea wire:model="content" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Isi pengumuman..."></textarea>
                        @error('content') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Icon Color --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Warna Icon</label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="icon_color" value="blue" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-sm transition peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 hover:bg-gray-50">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 mx-auto mb-1"></div>
                                    Blue
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="icon_color" value="green" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-sm transition peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 hover:bg-gray-50">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mx-auto mb-1"></div>
                                    Green
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="icon_color" value="amber" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-sm transition peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 hover:bg-gray-50">
                                    <div class="w-3 h-3 rounded-full bg-amber-500 mx-auto mb-1"></div>
                                    Amber
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="icon_color" value="red" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-sm transition peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 hover:bg-gray-50">
                                    <div class="w-3 h-3 rounded-full bg-red-500 mx-auto mb-1"></div>
                                    Red
                                </div>
                            </label>
                        </div>
                        @error('icon_color') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Order --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan</label>
                        <input type="number" wire:model="order" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Angka lebih kecil akan tampil lebih atas</p>
                        @error('order') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Active Status --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_active" id="is_active"
                               class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <label for="is_active" class="text-sm font-semibold text-gray-700">Aktifkan pengumuman</label>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                                class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                            {{ $editingId ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
