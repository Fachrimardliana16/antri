<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold" style="color: var(--text-primary);">Pengumuman TV Monitor</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Kelola informasi yang ditampilkan di sidebar TV monitor</p>
        </div>
        <button wire:click="openCreateModal"
                class="px-4 py-2 text-white rounded-lg font-semibold transition flex items-center gap-2 hover:opacity-90"
                style="background-color: var(--primary);">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengumuman
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="px-4 py-3 rounded-lg text-sm border-l-4"
             style="background-color: #dcfce7; border-color: #16a34a; color: #166534;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Announcements List --}}
    <div class="gov-card overflow-hidden">
        <table class="w-full">
            <thead class="border-b" style="background-color: var(--bg-elevated); border-color: var(--border-default);">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color: var(--text-muted);">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color: var(--text-muted);">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color: var(--text-muted);">Konten</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color: var(--text-muted);">Warna</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase" style="color: var(--text-muted);">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase" style="color: var(--text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--border-light);">
                @forelse($announcements as $announcement)
                    <tr class="transition"
                        onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                        onmouseout="this.style.backgroundColor=''">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold" style="color: var(--text-primary);">{{ $announcement->order }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold" style="color: var(--text-primary);">{{ $announcement->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm line-clamp-2" style="color: var(--text-secondary);">{{ $announcement->content }}</div>
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
                                        class="p-2 rounded-lg transition"
                                        style="color: var(--primary);"
                                        onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                                        onmouseout="this.style.backgroundColor=''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $announcement->id }})"
                                        onclick="return confirm('Yakin hapus pengumuman ini?')"
                                        class="p-2 text-red-600 rounded-lg transition hover:bg-red-50">
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
                            <div style="color: var(--text-muted);">
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
            <div class="gov-panel max-w-lg w-full p-6" @click.stop>
                <h3 class="text-xl font-bold mb-4" style="color: var(--text-primary);">
                    {{ $editingId ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1" style="color: var(--text-secondary);">Judul</label>
                        <input type="text" wire:model="title"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:outline-none"
                               style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                               placeholder="Contoh: Perhatian">
                        @error('title') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Content --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1" style="color: var(--text-secondary);">Konten</label>
                        <textarea wire:model="content" rows="3"
                                  class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:outline-none"
                                  style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                                  placeholder="Isi pengumuman..."></textarea>
                        @error('content') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Media Type --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-secondary);">Tipe Media</label>
                        <div class="grid grid-cols-4 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="media_type" value="text" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-xs transition peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700"
                                     style="border-color: var(--border-default); color: var(--text-secondary);">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                    Text
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="media_type" value="image" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-xs transition peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700"
                                     style="border-color: var(--border-default); color: var(--text-secondary);">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Image
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="media_type" value="video" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-xs transition peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700"
                                     style="border-color: var(--border-default); color: var(--text-secondary);">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Video
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="media_type" value="youtube" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-xs transition peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700"
                                     style="border-color: var(--border-default); color: var(--text-secondary);">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    YouTube
                                </div>
                            </label>
                        </div>
                        @error('media_type') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Image/Video Upload --}}
                    @if(in_array($media_type, ['image', 'video']))
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color: var(--text-secondary);">
                                Upload {{ $media_type === 'image' ? 'Gambar' : 'Video' }}
                                @if($editingId)
                                    <span class="text-xs font-normal" style="color: var(--text-muted);">(Kosongkan jika tidak ingin mengganti)</span>
                                @endif
                            </label>
                            <input type="file" wire:model="media_file"
                                   accept="{{ $media_type === 'image' ? 'image/*' : 'video/mp4,video/webm,video/ogg' }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:outline-none"
                                   style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                            <p class="text-xs mt-1" style="color: var(--text-muted);">
                                @if($media_type === 'image')
                                    Format: JPG, PNG, GIF. Maksimal 5MB
                                @else
                                    Format: MP4, WebM, OGG. Maksimal 50MB
                                @endif
                            </p>
                            @error('media_file') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror

                            @if($media_file)
                                <div class="mt-2 p-2 rounded-lg border" style="border-color: var(--border-default); background-color: var(--bg-elevated);">
                                    <p class="text-xs font-semibold" style="color: var(--text-secondary);">Preview:</p>
                                    @if($media_type === 'image')
                                        <img src="{{ $media_file->temporaryUrl() }}" class="mt-1 max-h-32 rounded">
                                    @endif
                                </div>
                            @elseif($editingId && $announcements->where('id', $editingId)->first()?->media_path)
                                <div class="mt-2 p-2 rounded-lg border" style="border-color: var(--border-default); background-color: var(--bg-elevated);">
                                    <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);">Current:</p>
                                    @if($media_type === 'image')
                                        <img src="{{ asset('storage/' . $announcements->where('id', $editingId)->first()->media_path) }}" class="max-h-32 rounded">
                                    @else
                                        <p class="text-xs" style="color: var(--text-muted);">{{ basename($announcements->where('id', $editingId)->first()->media_path) }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- YouTube URL --}}
                    @if($media_type === 'youtube')
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color: var(--text-secondary);">YouTube URL</label>
                            <input type="url" wire:model="youtube_url"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:outline-none"
                                   style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <p class="text-xs mt-1" style="color: var(--text-muted);">Paste full YouTube video URL</p>
                            @error('youtube_url') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- Icon Color --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-secondary);">Warna Icon</label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="icon_color" value="blue" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-sm transition peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700"
                                     style="border-color: var(--border-default);"
                                     onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                                     onmouseout="if (!this.classList.contains('peer-checked:bg-blue-50')) this.style.backgroundColor=''">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 mx-auto mb-1"></div>
                                    Blue
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="icon_color" value="green" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-sm transition peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700"
                                     style="border-color: var(--border-default);"
                                     onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                                     onmouseout="if (!this.classList.contains('peer-checked:bg-green-50')) this.style.backgroundColor=''">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mx-auto mb-1"></div>
                                    Green
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="icon_color" value="amber" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-sm transition peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700"
                                     style="border-color: var(--border-default);"
                                     onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                                     onmouseout="if (!this.classList.contains('peer-checked:bg-amber-50')) this.style.backgroundColor=''">
                                    <div class="w-3 h-3 rounded-full bg-amber-500 mx-auto mb-1"></div>
                                    Amber
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="icon_color" value="red" class="peer sr-only">
                                <div class="px-3 py-2 border-2 rounded-lg text-center font-semibold text-sm transition peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700"
                                     style="border-color: var(--border-default);"
                                     onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                                     onmouseout="if (!this.classList.contains('peer-checked:bg-red-50')) this.style.backgroundColor=''">
                                    <div class="w-3 h-3 rounded-full bg-red-500 mx-auto mb-1"></div>
                                    Red
                                </div>
                            </label>
                        </div>
                        @error('icon_color') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Order --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1" style="color: var(--text-secondary);">Urutan</label>
                        <input type="number" wire:model="order" min="0"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:outline-none"
                               style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                        <p class="text-xs mt-1" style="color: var(--text-muted);">Angka lebih kecil akan tampil lebih atas</p>
                        @error('order') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Active Status --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_active" id="is_active"
                               class="w-4 h-4 rounded focus:ring-2"
                               style="accent-color: var(--primary);">
                        <label for="is_active" class="text-sm font-semibold" style="color: var(--text-secondary);">Aktifkan pengumuman</label>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                                class="flex-1 py-2 rounded-lg font-semibold transition"
                                style="background-color: var(--bg-hover); color: var(--text-secondary);"
                                onmouseover="this.style.opacity='0.8'"
                                onmouseout="this.style.opacity='1'">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2 text-white rounded-lg font-semibold transition hover:opacity-90"
                                style="background-color: var(--primary);">
                            {{ $editingId ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
