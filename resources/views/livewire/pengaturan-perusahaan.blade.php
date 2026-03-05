<div class="max-w-4xl mx-auto pb-12">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <i data-lucide="building-2" class="w-8 h-8 text-[#58A6FF]"></i>
                Pengaturan Perusahaan
            </h1>
            <p class="text-slate-400 text-sm mt-1">Atur identitas utama perusahaan yang akan tampil di header laporan & Surat Jalan.</p>
        </div>
    </div>

    <div class="bg-[#161B22] border border-[#30363D] rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-[#58A6FF]/5 rounded-full blur-3xl"></div>
        
        <form wire:submit="simpan" class="space-y-6 relative z-10">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Perusahaan <span class="text-rose-500">*</span></label>
                <input type="text" wire:model="nama_perusahaan" class="w-full px-4 py-3 bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all">
                @error('nama_perusahaan') <span class="text-rose-500 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Alamat Lengkap</label>
                <textarea wire:model="alamat" rows="3" class="w-full px-4 py-3 bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all"></textarea>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pusat Bantuan / Telepon</label>
                    <input type="text" wire:model="telepon" class="w-full px-4 py-3 bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email Perusahaan</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-3 bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all">
                    @error('email') <span class="text-rose-500 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="pt-6 border-t border-[#30363D]/50 flex justify-end">
                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#1F6FEB] hover:bg-[#388BFD] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#1F6FEB]/20 flex items-center justify-center gap-2" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="simpan" class="flex items-center gap-2"><i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan</span>
                    <span wire:loading wire:target="simpan" class="flex items-center">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

