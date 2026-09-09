<div class="space-y-6">
    <form wire:submit.prevent="login" class="space-y-5">
        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email</label>
            <input type="email" wire:model="email" class="w-full bg-slate-900/90 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="nama@antri.local" autofocus>
            @error('email') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password</label>
            <input type="password" wire:model="password" class="w-full bg-slate-900/90 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="••••••••">
            @error('password') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center space-x-2 text-xs text-slate-300 cursor-pointer">
                <input type="checkbox" wire:model="remember" class="rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-0">
                <span>Ingat Saya</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold text-sm shadow-xl shadow-blue-600/30 hover:brightness-110 active:scale-98 transition">
            Masuk ke Sistem
        </button>
    </form>

    <!-- Quick Fill Demo Accounts -->
    <div class="pt-5 border-t border-slate-800/80">
        <div class="text-xs font-semibold text-slate-400 mb-2">Akun Demo Cepat (Password: password):</div>
        <div class="grid grid-cols-3 gap-2 text-[11px]">
            <button type="button" wire:click="$set('email', 'superadmin@antri.local'); $set('password', 'password')" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-purple-300 hover:bg-slate-800 transition text-center">
                Super Admin
            </button>
            <button type="button" wire:click="$set('email', 'admin@antri.local'); $set('password', 'password')" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-blue-300 hover:bg-slate-800 transition text-center">
                Supervisor
            </button>
            <button type="button" wire:click="$set('email', 'operator1@antri.local'); $set('password', 'password')" class="p-2 rounded-lg bg-slate-900 border border-slate-800 text-emerald-300 hover:bg-slate-800 transition text-center">
                Operator 1
            </button>
        </div>
    </div>
</div>
