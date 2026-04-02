<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2 transition-colors duration-300 ease-in-out">
            <i data-lucide="user-cog" class="w-5 h-5 text-blue-500 transition-colors duration-300 ease-in-out"></i>
            Pengaturan Profil
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Ubah identitas akun dan perbarui kata sandi Anda disini.</p>
    </div>
</x-slot:header>

<div class="max-w-4xl mx-auto pb-12">
    <!-- Header -->
    <div class="mb-6 flex justify-end">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:text-white rounded-xl transition-colors border border-slate-200 dark:border-slate-800">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Identitas Dasar -->
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden transition-colors duration-300 ease-in-out">
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#58A6FF]/5 rounded-full blur-3xl transition-colors duration-300 ease-in-out"></div>
            
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-slate-800 pb-3 flex items-center gap-2 transition-colors duration-300 ease-in-out">
                <i data-lucide="contact" class="w-5 h-5 text-slate-500 dark:text-slate-400 transition-colors duration-300 ease-in-out"></i> Informasi Pribadi
            </h2>

            <!-- Success/Error Output logic will be natively handled by the global sweet alert in app.blade.php.
                 For validation, we output the raw standard text here. -->
            
            <form wire:submit="updateProfile" class="space-y-5 relative z-10">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Nama Lengkap</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    @error('name') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Alamat Email</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    @error('email') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-800/50 transition-colors duration-300 ease-in-out">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-[#10B981] dark:bg-blue-500 hover:bg-[#388BFD] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#1F6FEB]/20 flex items-center justify-center gap-2" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updateProfile">Simpan Informasi</span>
                        <span wire:loading wire:target="updateProfile" class="flex items-center">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Menyimpan...
                        </span>
                    </button>
                    <!-- Success indicator handled by dispatch -> Sweet Alert -->
                </div>
            </form>
        </div>

        <!-- Ubah Password -->
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden transition-colors duration-300 ease-in-out">
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/5 rounded-full blur-3xl transition-colors duration-300 ease-in-out"></div>
            
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-slate-800 pb-3 flex items-center gap-2 transition-colors duration-300 ease-in-out">
                <i data-lucide="shield-alert" class="w-5 h-5 text-slate-500 dark:text-slate-400 transition-colors duration-300 ease-in-out"></i> Pembaruan Kata Sandi
            </h2>
            
            <form wire:submit="updatePassword" class="space-y-5 relative z-10">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Kata Sandi Saat Ini</label>
                    <input type="password" wire:model="current_password" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    @error('current_password') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Kata Sandi Baru</label>
                    <input type="password" wire:model="password" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    @error('password') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" wire:model="password_confirmation" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-800/50 transition-colors duration-300 ease-in-out">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-[#10B981] dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-[#10B981] text-white font-bold rounded-xl transition-all shadow-lg flex items-center justify-center gap-2" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updatePassword">Ubah Kata Sandi</span>
                        <span wire:loading wire:target="updatePassword" class="flex items-center">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Mengubah...
                        </span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
