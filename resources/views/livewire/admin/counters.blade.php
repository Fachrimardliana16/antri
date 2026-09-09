<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Master Data Loket Pelayanan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Atur nomor loket, pemetaan layanan, dan penugasan operator.</p>
        </div>
        <button type="button" wire:click="create"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                style="background-color: var(--primary, #1a56a8);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Loket
        </button>
    </div>

    {{-- Table --}}
    <div class="gov-card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Nomor</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Nama Loket</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Layanan Terkait</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Operator</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($counters as $counter)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4 font-mono font-bold text-gray-800 text-lg">
                            #{{ $counter->number }}
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $counter->name }}</td>
                        <td class="px-5 py-4">
                            @if($counter->service)
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold text-white"
                                      style="background-color: {{ $counter->service->color }};">
                                    {{ $counter->service->code }} - {{ $counter->service->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Semua Layanan</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            {{ $counter->currentOperator ? $counter->currentOperator->name : '—' }}
                        </td>
                        <td class="px-5 py-4">
                            @if($counter->status === 'active')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                            @elseif($counter->status === 'break')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Istirahat</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Tutup</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button" wire:click="edit({{ $counter->id }})"
                                    class="text-blue-600 hover:text-blue-700 text-sm font-medium mr-3">Edit</button>
                            <button type="button" wire:confirm="Hapus loket ini?" wire:click="delete({{ $counter->id }})"
                                    class="text-red-500 hover:text-red-600 text-sm font-medium">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400 text-sm">Belum ada data loket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 animate-fade-in">
            <div class="gov-panel max-w-md w-full p-6 space-y-4">
                <h3 class="text-base font-bold text-gray-900">{{ $editingCounterId ? 'Edit Loket' : 'Tambah Loket Baru' }}</h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Loket</label>
                        <input type="text" wire:model="name"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               placeholder="Loket 1">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Urut</label>
                            <input type="number" wire:model="number"
                                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Awal</label>
                            <select wire:model="status"
                                    class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="active">Aktif</option>
                                <option value="break">Istirahat</option>
                                <option value="closed">Tutup</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Layanan yang Ditangani</label>
                        <select wire:model="service_id"
                                class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $srv)
                                <option value="{{ $srv->id }}">{{ $srv->code }} - {{ $srv->name }}</option>
                            @endforeach
                        </select>
                        @error('service_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Operator Bertugas (Opsional)</label>
                        <select wire:model="current_operator_id"
                                class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($operators as $op)
                                <option value="{{ $op->id }}">{{ $op->name }} ({{ $op->email }})</option>
                            @endforeach
                        </select>
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
