<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">{{ $pemasokId ? 'Edit Pemasok' : 'Tambah Pemasok Baru' }}</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Lengkapi informasi relasi vendor supplier Anda.</p>
    </div>
</x-slot:header>

<div class="w-full">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('pemasok.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-600 dark:text-rose-500 font-bold rounded-xl transition-colors text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </div>

    <form wire:submit="simpan" class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden max-w-3xl transition-colors duration-300 ease-in-out">
        <div class="p-6 md:p-8 space-y-5">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Nama Vendor / Organisasi <span class="text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                <input type="text" wire:model="nama" placeholder="Contoh: PT. Sumber Makmur"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                @error('nama') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Alamat Lengkap</label>
                <textarea wire:model="alamat" placeholder="Jalan Raya No. 123..."
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-3" rows="3"></textarea>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Nomor Telepon / HP</label>
                    <input type="text" wire:model="telepon" placeholder="Nomor yang dapat dihubungi"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Alamat Email</label>
                    <input type="email" wire:model="email" placeholder="contoh@domain.com"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                </div>
            </div>
        </div>
        
        <div class="px-6 md:px-8 py-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3 transition-colors duration-300 ease-in-out">
            <a href="{{ route('pemasok.index') }}" class="px-5 py-2.5 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl transition-colors text-sm">Batal</a>
            <button type="submit" wire:loading.attr="disabled" wire:target="simpan" class="inline-flex justify-center items-center px-6 py-2.5 bg-[#10B981] dark:bg-blue-500 hover:bg-[#388BFD] disabled:opacity-50 text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 text-sm">
                <i data-lucide="save" class="w-4 h-4 mr-2" wire:loading.remove wire:target="simpan"></i>
                <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="simpan" style="display: none;"></i>
                <span wire:loading.remove wire:target="simpan">Simpan Data Vendor</span>
                <span wire:loading wire:target="simpan" style="display: none;">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
