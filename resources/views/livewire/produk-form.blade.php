<div class="w-full">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">{{ $produkId ? 'Edit Produk' : 'Tambah Produk Baru' }}</h1>
            <p class="text-slate-400 text-sm mt-1">Lengkapi informasi detail mengenai produk.</p>
        </div>
        <a href="{{ route('produk.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-500 font-bold rounded-xl transition-colors text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </div>

    <form wire:submit="simpan" class="bg-[#161B22] border border-[#30363D] rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 md:p-8 space-y-6">
            
            <!-- Section: Identifikasi -->
            <div>
                <h3 class="text-sm font-bold text-slate-200 mb-4 pb-2 border-b border-[#30363D] flex items-center gap-2">
                    <i data-lucide="tag" class="w-4 h-4 text-[#58A6FF]"></i> Identifikasi Produk
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="barcode" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Barcode <span class="text-rose-500">*</span></label>
                        <input type="text" id="barcode" wire:model="barcode" placeholder="Scan atau ketik kode..."
                            class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5">
                        @error('barcode') <span class="text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="sku" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">SKU (Opsional)</label>
                        <input type="text" id="sku" wire:model="sku" placeholder="Contoh: ITM-001"
                            class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5">
                    </div>
                </div>
                
                <div class="mt-5">
                    <label for="name" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" wire:model="name" placeholder="Masukkan nama lengkap produk..."
                        class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5">
                    @error('name') <span class="text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section: Inventory & Lokasi -->
            <div>
                <h3 class="text-sm font-bold text-slate-200 mb-4 pb-2 border-b border-[#30363D] flex items-center gap-2 mt-8">
                    <i data-lucide="layers" class="w-4 h-4 text-[#58A6FF]"></i> Pengaturan Stok & Relasi
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="min_stock" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Batas Stok Minimum</label>
                        <input type="number" id="min_stock" wire:model="min_stock" min="0" placeholder="0"
                            class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5">
                        @error('min_stock') <span class="text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                        <p class="text-[10px] text-slate-500 mt-1">Akan muncul notifikasi peringatan bila stok di bawah angka ini.</p>
                    </div>
                    <div>
                        <label for="max_stock" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Batas Stok Maksimum</label>
                        <input type="number" id="max_stock" wire:model="max_stock" min="0" placeholder="0"
                            class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label for="location" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Penempatan Lokasi</label>
                        <input type="text" id="location" wire:model="location" placeholder="Misal: Rak A-1"
                            class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-600 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5">
                    </div>
                    <div>
                        <label for="supplier_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pemasok Default</label>
                        <div class="relative">
                            <select id="supplier_id" wire:model="supplier_id"
                                class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5 appearance-none">
                                <option value="">-- Pilih Vendor Pemasok --</option>
                                @foreach($pemasok as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="px-6 md:px-8 py-4 bg-[#0D1117] border-t border-[#30363D] flex justify-end gap-3">
            <a href="{{ route('produk.index') }}" class="px-5 py-2.5 border border-[#30363D] hover:bg-[#30363D] text-slate-300 font-bold rounded-xl transition-colors text-sm">Batal</a>
            <button type="submit" wire:loading.attr="disabled" wire:target="simpan" class="inline-flex justify-center items-center px-6 py-2.5 bg-[#238636] hover:bg-[#2EA043] disabled:opacity-50 text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#238636]/20 text-sm">
                <i data-lucide="save" class="w-4 h-4 mr-2" wire:loading.remove wire:target="simpan"></i>
                <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="simpan" style="display: none;"></i>
                <span wire:loading.remove wire:target="simpan">Simpan Data</span>
                <span wire:loading wire:target="simpan" style="display: none;">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
