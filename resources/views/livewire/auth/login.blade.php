{{-- Login Form --}}
<div class="space-y-5">
    <form wire:submit.prevent="login" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
            <input type="email" wire:model="email"
                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                   placeholder="nama@instansi.go.id" autofocus>
            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <input type="password" wire:model="password"
                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                   placeholder="••••••••">
            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="remember" wire:model="remember"
                   class="w-4 h-4 border-gray-300 rounded text-blue-600 focus:ring-blue-500">
            <label for="remember" class="text-sm text-gray-600 cursor-pointer">Ingat Saya</label>
        </div>

        <button type="submit"
                class="w-full py-2.5 rounded-lg text-sm font-semibold text-white transition hover:opacity-90 active:scale-98"
                style="background-color: var(--primary);">
            Masuk ke Sistem
        </button>
    </form>

    {{-- Demo Accounts --}}
    <div class="pt-5 border-t border-gray-100">
        <p class="text-xs text-gray-500 mb-2 font-medium">Akun Demo (Password: <span class="font-mono">password</span>):</p>
        <div class="grid grid-cols-3 gap-2">
            <button type="button"
                    wire:click="$set('email', 'superadmin@antri.local'); $set('password', 'password')"
                    class="p-2 rounded-lg border border-gray-200 text-xs text-purple-700 bg-purple-50 hover:bg-purple-100 transition text-center font-medium">
                Super Admin
            </button>
            <button type="button"
                    wire:click="$set('email', 'admin@antri.local'); $set('password', 'password')"
                    class="p-2 rounded-lg border border-gray-200 text-xs text-blue-700 bg-blue-50 hover:bg-blue-100 transition text-center font-medium">
                Supervisor
            </button>
            <button type="button"
                    wire:click="$set('email', 'operator1@antri.local'); $set('password', 'password')"
                    class="p-2 rounded-lg border border-gray-200 text-xs text-green-700 bg-green-50 hover:bg-green-100 transition text-center font-medium">
                Operator 1
            </button>
        </div>
    </div>
</div>
