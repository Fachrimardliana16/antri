<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Master Data Loket Pelayanan</h1>
            <p class="text-sm text-slate-400 mt-1">Atur nomor loket, pemetaan layanan, dan penugasan operator.</p>
        </div>

        <button type="button" wire:click="create" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Loket</span>
        </button>
    </div>

    <!-- Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden shadow-2xl">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-900/80 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                <tr>
                    <th class="px-6 py-4">Nomor</th>
                    <th class="px-6 py-4">Nama Loket</th>
                    <th class="px-6 py-4">Layanan Terkait</th>
                    <th class="px-6 py-4">Operator Aktif</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
                @forelse($counters as $counter)
                    <tr class="hover:bg-slate-900/40 transition">
                        <td class="px-6 py-4 font-mono font-black text-white text-lg">
                            #{{ $counter->number }}
                        </td>
                        <td class="px-6 py-4 font-bold text-white">
                            {{ $counter->name }}
                        </td>
                        <td class="px-6 py-4">
                            @if($counter->service)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold text-white" style="background: {{ $counter->service->color }};">
                                    {{ $counter->service->code }} - {{ $counter->service->name }}
                                </span>
                            @else
                                <span class="text-xs text-slate-500">Semua Layanan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-slate-300">
                                {{ $counter->currentOperator ? $counter->currentOperator->name : 'Belum Ditugaskan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($counter->status === 'active')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Aktif Melayani</span>
                            @elseif($counter->status === 'break')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Istirahat</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-500">Tutup</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" wire:click="edit({{ $counter->id }})" class="text-blue-400 hover:text-blue-300 font-semibold text-xs">Edit</button>
                            <button type="button" wire:confirm="Hapus loket ini?" wire:click="delete({{ $counter->id }})" class="text-rose-400 hover:text-rose-300 font-semibold text-xs">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-500">Belum ada data loket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="glass-panel max-w-md w-full rounded-3xl p-6 sm:p-8 border border-slate-700 shadow-2xl space-y-4">
                <h3 class="text-xl font-bold text-white">{{ $editingCounterId ? 'Edit Loket' : 'Tambah Loket Baru' }}</h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Loket</label>
                        <input type="text" wire:model="name" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="Loket 1">
                        @error('name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Urut Loket</label>
                            <input type="number" wire:model="number" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                            @error('number') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Status Awal</label>
                            <select wire:model="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                                <option value="active">Aktif</option>
                                <option value="break">Istirahat</option>
                                <option value="closed">Tutup</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Layanan yang Ditangani</label>
                        <select wire:model="service_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $srv)
                                <option value="{{ $srv->id }}">{{ $srv->code }} - {{ $srv->name }}</option>
                            @endforeach
                        </select>
                        @error('service_id') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Operator Bertugas (Opsional)</label>
                        <select wire:model="current_operator_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($operators as $op)
                                <option value="{{ $op->id }}">{{ $op->name }} ({{ $op->email }})</option>
                            @endforeach
                        </select>
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
