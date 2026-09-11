<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--text-primary);">Master Data Loket Pelayanan</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-muted);">Atur nomor loket, pemetaan layanan, dan penugasan operator.</p>
        </div>
        <button type="button" wire:click="create"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                style="background-color: var(--primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Loket
        </button>
    </div>

    {{-- Table --}}
    <div class="gov-card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="border-b" style="background-color: var(--bg-elevated); border-color: var(--border-default);">
                <tr>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Nomor</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Nama Loket</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Layanan Terkait</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Operator</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Status</th>
                    <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--border-light);">
                @forelse($counters as $counter)
                    <tr class="transition"
                        onmouseover="this.style.backgroundColor='var(--bg-hover)'"
                        onmouseout="this.style.backgroundColor=''">
                        <td class="px-5 py-4 font-mono font-bold text-lg" style="color: var(--text-primary);">
                            #{{ $counter->number }}
                        </td>
                        <td class="px-5 py-4 font-semibold" style="color: var(--text-primary);">{{ $counter->name }}</td>
                        <td class="px-5 py-4">
                            @if($counter->service)
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold text-white"
                                      style="background-color: {{ $counter->service->color }};">
                                    {{ $counter->service->code }} - {{ $counter->service->name }}
                                </span>
                            @else
                                <span class="text-xs" style="color: var(--text-muted);">Semua Layanan</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm" style="color: var(--text-secondary);">
                            {{ $counter->currentOperator ? $counter->currentOperator->name : '—' }}
                        </td>
                        <td class="px-5 py-4">
                            @if($counter->status === 'active')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                            @elseif($counter->status === 'break')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Istirahat</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background-color: var(--bg-hover); color: var(--text-muted);">Tutup</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button" wire:click="edit({{ $counter->id }})"
                                    class="text-sm font-medium mr-3 transition"
                                    style="color: var(--primary);"
                                    onmouseover="this.style.opacity='0.8'"
                                    onmouseout="this.style.opacity='1'">Edit</button>
                            <button type="button" wire:confirm="Hapus loket ini?" wire:click="delete({{ $counter->id }})"
                                    class="text-sm font-medium text-red-500 hover:text-red-600 transition">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-sm" style="color: var(--text-muted);">Belum ada data loket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 animate-fade-in">
            <div class="gov-panel max-w-md w-full p-6 space-y-4">
                <h3 class="text-base font-bold" style="color: var(--text-primary);">{{ $editingCounterId ? 'Edit Loket' : 'Tambah Loket Baru' }}</h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Nama Loket</label>
                        <input type="text" wire:model="name"
                               class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                               style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                               placeholder="Loket 1">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Nomor Urut</label>
                            <input type="number" wire:model="number"
                                   class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                                   style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                            @error('number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Status Awal</label>
                            <select wire:model="status"
                                    class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                                <option value="active">Aktif</option>
                                <option value="break">Istirahat</option>
                                <option value="closed">Tutup</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Layanan yang Ditangani</label>
                        <select wire:model="service_id"
                                class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                                style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $srv)
                                <option value="{{ $srv->id }}">{{ $srv->code }} - {{ $srv->name }}</option>
                            @endforeach
                        </select>
                        @error('service_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">Operator Bertugas (Opsional)</label>
                        <select wire:model="current_operator_id"
                                class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-1"
                                style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($operators as $op)
                                <option value="{{ $op->id }}">{{ $op->name }} ({{ $op->email }})</option>
                            @endforeach
                        </select>
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
