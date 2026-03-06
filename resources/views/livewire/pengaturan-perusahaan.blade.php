<div class="max-w-4xl mx-auto pb-12">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3 transition-colors duration-300 ease-in-out">
                <i data-lucide="building-2" class="w-8 h-8 text-blue-500 transition-colors duration-300 ease-in-out"></i>
                Pengaturan Perusahaan
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 transition-colors duration-300 ease-in-out">Atur identitas utama perusahaan yang akan tampil di header laporan & Surat Jalan.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden transition-colors duration-300 ease-in-out">
        <div class="absolute top-0 right-0 w-32 h-32 bg-[#58A6FF]/5 rounded-full blur-3xl transition-colors duration-300 ease-in-out"></div>
        
        <form wire:submit="simpan" class="space-y-6 relative z-10">
            <div class="flex flex-col md:flex-row gap-8 items-start">
                <!-- Logo Upload Box -->
                <div class="w-full md:w-1/3 flex flex-col gap-4">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">Logo Perusahaan</label>
                    <div class="relative w-full aspect-square max-w-[200px] border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-blue-500 dark:hover:border-blue-500 rounded-3xl overflow-hidden bg-slate-50 dark:bg-slate-950 flex flex-col items-center justify-center cursor-pointer transition-all group">
                        
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-contain p-4 z-10 bg-white dark:bg-slate-900 border-2 border-blue-500 rounded-3xl">
                        @elseif ($logo_path)
                            <img src="{{ asset('storage/' . $logo_path) }}" class="absolute inset-0 w-full h-full object-contain p-4 z-10 bg-white dark:bg-slate-900 border-2 border-transparent rounded-3xl group-hover:opacity-50 transition-opacity">
                        @endif

                        <div class="flex flex-col items-center justify-center p-4 text-center z-0 group-hover:text-blue-500 {{ ($logo || $logo_path) ? 'opacity-0 group-hover:opacity-100 z-20 absolute inset-0 bg-black/50 text-white group-hover:text-white' : 'text-slate-400 dark:text-slate-500' }}">
                            <i data-lucide="{{ $logo || $logo_path ? 'refresh-cw' : 'upload-cloud' }}" class="w-8 h-8 mb-2"></i>
                            <span class="text-xs font-bold">{{ $logo || $logo_path ? 'Ganti Logo' : 'Upload Logo (PNG/JPG)' }}</span>
                        </div>

                        <input type="file" wire:model="logo" wire:key="logo-upload-{{ $uploadIteration }}" id="logo-upload-{{ $uploadIteration }}" accept="image/png, image/jpeg, image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30">
                    </div>
                    @error('logo') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="logo" class="text-[11px] text-blue-500 font-bold animate-pulse">Mengunggah logo...</div>
                </div>

                <!-- Input Dasar -->
                <div class="flex-1 space-y-6 w-full">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Nama Perusahaan <span class="text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                        <input type="text" wire:model="nama_perusahaan" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                        @error('nama_perusahaan') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Alamat Lengkap</label>
                        <textarea wire:model="alamat" rows="3" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all"></textarea>
                    </div>
                </div>
            </div>
            

            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Pusat Bantuan / Telepon</label>
                    <input type="text" wire:model="telepon" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Email Perusahaan</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    @error('email') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800/50 flex justify-end transition-colors duration-300 ease-in-out">
                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-blue-600 dark:bg-blue-500 hover:bg-[#388BFD] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#1F6FEB]/20 flex items-center justify-center gap-2" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="simpan" class="flex items-center gap-2"><i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan</span>
                    <span wire:loading wire:target="simpan" class="flex items-center">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

