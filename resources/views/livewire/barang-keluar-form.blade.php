<div class="w-full">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#0A1931] dark:text-white tracking-tight flex items-center gap-2 transition-colors duration-300 ease-in-out">
            <i data-lucide="arrow-up-right" class="w-6 h-6 text-rose-500 transition-colors duration-300 ease-in-out"></i> Barang Keluar
        </h1>
        <p class="text-[#4A7FA7]/70 dark:text-slate-400 text-sm mt-1 transition-colors duration-300 ease-in-out">Catat pengeluaran stok untuk pelanggan atau divisi lain.</p>
    </div>

    <div class="bg-white dark:bg-[#161B22] border border-[#B3CFE5]/30 dark:border-[#30363D] rounded-2xl shadow-xl overflow-hidden max-w-4xl transition-colors duration-300 ease-in-out">
        <form wire:submit="simpan" class="p-6 md:p-8 space-y-6">
            
            <div class="bg-[#F6FAFD] dark:bg-[#21262D] border border-[#B3CFE5]/30 dark:border-[#30363D] rounded-xl p-4 mb-2 transition-colors duration-300 ease-in-out">
                <label class="block text-[11px] font-bold text-[#4A7FA7]/70 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Pencarian Cepat (Barcode)</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="scan-line" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 transition-colors duration-300 ease-in-out"></i>
                        <input type="text" wire:model="barcodeTerpilih" 
                            class="w-full bg-[#F6FAFD] dark:bg-[#0D1117] border border-[#B3CFE5]/30 dark:border-[#30363D] hover:border-[#8B949E] rounded-xl text-[#1A3D63] dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all pl-10 pr-4 py-2.5 text-sm" 
                            placeholder="Arahkan kursor & gunakan alat scanner barcode...">
                    </div>
                    <button type="button" wire:click="pilihProdukDariBarcode" 
                        class="px-4 py-2.5 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-500 font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
                        Cari
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-[#4A7FA7]/70 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Pilih Produk <span class="text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                        <div class="relative">
                            <select wire:model="product_id" 
                                class="w-full bg-[#F6FAFD] dark:bg-[#0D1117] border border-[#B3CFE5]/30 dark:border-[#30363D] hover:border-[#8B949E] rounded-xl text-[#1A3D63] dark:text-slate-200 focus:bg-white dark:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5 appearance-none text-sm">
                                <option value="">-- Pilih Produk Data Master --</option>
                                @foreach($daftarProduk as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode }}) - Stok Tersedia: {{ $p->current_stock }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none transition-colors duration-300 ease-in-out"></i>
                        </div>
                        @error('product_id') <span class="text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#4A7FA7]/70 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Jumlah Dikeluarkan <span class="text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                        <input type="number" wire:model="jumlah" min="1" 
                            class="w-full bg-[#F6FAFD] dark:bg-[#0D1117] border border-[#B3CFE5]/30 dark:border-[#30363D] hover:border-[#8B949E] rounded-xl text-rose-400 font-bold text-lg focus:bg-white dark:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5">
                        @error('jumlah') <span class="text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#4A7FA7]/70 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Tanggal Transaksi <span class="text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                        <input type="datetime-local" wire:model="tanggal" 
                            class="w-full bg-[#F6FAFD] dark:bg-[#0D1117] border border-[#B3CFE5]/30 dark:border-[#30363D] hover:border-[#8B949E] rounded-xl text-[#1A3D63] dark:text-slate-200 focus:bg-white dark:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5 [color-scheme:dark] text-sm">
                        @error('tanggal') <span class="text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-[#4A7FA7]/70 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Data Pelanggan Tujuan</label>
                        <div class="relative">
                            <select wire:model="customer_id" 
                                class="w-full bg-[#F6FAFD] dark:bg-[#0D1117] border border-[#B3CFE5]/30 dark:border-[#30363D] hover:border-[#8B949E] rounded-xl text-[#1A3D63] dark:text-slate-200 focus:bg-white dark:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-2.5 appearance-none text-sm">
                                <option value="">-- Pilih Pelanggan (Opsional) --</option>
                                @foreach($pelanggan as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none transition-colors duration-300 ease-in-out"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#4A7FA7]/70 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Catatan / No. Surat Jalan</label>
                        <textarea wire:model="catatan" 
                            class="w-full bg-[#F6FAFD] dark:bg-[#0D1117] border border-[#B3CFE5]/30 dark:border-[#30363D] hover:border-[#8B949E] rounded-xl text-[#1A3D63] dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-4 py-3 text-sm" 
                            rows="3" placeholder="Nomor faktur, keterangan proyek, dsb..."></textarea>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-[#B3CFE5]/30 dark:border-[#30363D] flex justify-end transition-colors duration-300 ease-in-out">
                <button type="submit" wire:loading.attr="disabled" wire:target="simpan" class="w-full sm:w-auto px-6 py-3 bg-[#1F6FEB] hover:bg-[#388BFD] text-white disabled:opacity-50 font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 flex justify-center items-center text-sm">
                    <i data-lucide="minus-square" class="w-4 h-4 mr-2" wire:loading.remove wire:target="simpan"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="simpan" style="display: none;"></i>
                    <span wire:loading.remove wire:target="simpan">Proses Pengeluaran Stok</span>
                    <span wire:loading wire:target="simpan" style="display: none;">Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>
