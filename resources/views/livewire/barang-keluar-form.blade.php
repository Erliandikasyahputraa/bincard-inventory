<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2 transition-colors duration-300 ease-in-out">
            <i data-lucide="arrow-up-right" class="w-5 h-5 text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out"></i> Barang Keluar
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Catat pengeluaran stok untuk pelanggan atau divisi lain.</p>
    </div>
</x-slot:header>

<div class="w-full">

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden max-w-4xl transition-colors duration-300 ease-in-out">
        <form wire:submit="simpan" class="p-6 md:p-8 space-y-6">
            
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 mb-2 transition-colors duration-300 ease-in-out relative">
                <label class="block text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Pencarian Cepat (Barcode)</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="scan-line" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 transition-colors duration-300 ease-in-out"></i>
                        <input type="text" enterkeyhint="search" x-data x-on:keydown.enter.prevent="$el.blur(); $wire.pilihProdukDariBarcode()" wire:model="barcodeTerpilih" id="barcode"
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 rounded-xl focus:bg-white dark:focus:bg-slate-950 focus:ring-1 focus:ring-blue-500 font-mono text-lg tracking-wider transition-all duration-300 shadow-inner"
                            placeholder="Ketik kode atau scan QR..."
                            autofocus>
                    </div>
                    <button type="button" wire:click="pilihProdukDariBarcode" 
                        class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
                        Cari
                    </button>
                </div>
                
                <!-- Dropdown Hasil Pencarian Fuzzy -->
                @if(count($hasilPencarian) > 0)
                <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-2xl z-50 max-h-60 overflow-y-auto">
                    <ul class="py-2">
                        @foreach($hasilPencarian as $h)
                        <li>
                            <button type="button" wire:click="pilihProduk({{ $h['id'] }})" class="w-full text-left px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between border-b border-slate-100 dark:border-slate-800/50 last:border-0">
                                <div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $h['name'] }}</p>
                                    <p class="text-[11px] text-slate-500 font-mono">{{ $h['barcode'] }}</p>
                                </div>
                                <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs px-2 py-1 rounded-lg font-bold">Stok: {{ $h['current_stock'] }}</span>
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Pilih Produk <span class="text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                        <div class="relative">
                            <select wire:model="product_id" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5 appearance-none text-sm">
                                <option value="">-- Pilih Produk Data Master --</option>
                                @foreach($daftarProduk as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode }}) - Stok Tersedia: {{ $p->current_stock }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none transition-colors duration-300 ease-in-out"></i>
                        </div>
                        @error('product_id') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Jumlah Dikeluarkan <span class="text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                        <input type="number" wire:model="jumlah" min="1" 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-rose-600 dark:text-rose-400 font-bold text-lg focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                        @error('jumlah') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Tanggal Transaksi <span class="text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                        <div class="relative">
                            <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none transition-colors duration-300 ease-in-out"></i>
                            <input type="datetime-local" wire:model="tanggal" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all pl-10 pr-4 py-2.5 [color-scheme:light] dark:[color-scheme:dark] text-sm">
                        </div>
                        @error('tanggal') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Data Pelanggan Tujuan</label>
                        <div class="relative">
                            <select wire:model="customer_id" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5 appearance-none text-sm">
                                <option value="">-- Pilih Pelanggan (Opsional) --</option>
                                @foreach($pelanggan as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none transition-colors duration-300 ease-in-out"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Catatan / No. Surat Jalan</label>
                        <textarea wire:model="catatan" 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-3 text-sm" 
                            rows="3" placeholder="Nomor faktur, keterangan proyek, dsb..."></textarea>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end transition-colors duration-300 ease-in-out">
                <button type="submit" wire:loading.attr="disabled" wire:target="simpan" class="w-full sm:w-auto px-6 py-3 bg-blue-600 dark:bg-blue-500 hover:bg-[#388BFD] text-white disabled:opacity-50 font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 flex justify-center items-center text-sm">
                    <i data-lucide="minus-square" class="w-4 h-4 mr-2" wire:loading.remove wire:target="simpan"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="simpan" style="display: none;"></i>
                    <span wire:loading.remove wire:target="simpan">Proses Pengeluaran Stok</span>
                    <span wire:loading wire:target="simpan" style="display: none;">Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>
