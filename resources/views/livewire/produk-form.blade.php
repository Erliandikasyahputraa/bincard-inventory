<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">{{ $produkId ? 'Edit Produk' : 'Tambah Produk Baru' }}</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Lengkapi informasi detail mengenai produk.</p>
    </div>
</x-slot:header>

<div class="w-full">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('produk.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-600 dark:text-rose-500 font-bold rounded-xl transition-colors text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </div>

    <form wire:submit="simpan" class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden transition-colors duration-300 ease-in-out">
        <div class="p-6 md:p-8 space-y-6">
            
            <!-- Section: Identifikasi -->
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4 pb-2 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2 transition-colors duration-300 ease-in-out">
                    <i data-lucide="tag" class="w-4 h-4 text-blue-500 transition-colors duration-300 ease-in-out"></i> Identifikasi Produk
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="barcode" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Barcode <span class="text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                        <input type="text" id="barcode" wire:model="barcode" placeholder="Scan atau ketik kode..."
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                        @error('barcode') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="sku" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">SKU (Opsional)</label>
                        <input type="text" id="sku" wire:model="sku" placeholder="Contoh: ITM-001"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                    </div>
                </div>
                
                <div class="mt-5">
                    <label for="name" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Nama Produk <span class="text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out">*</span></label>
                    <input type="text" id="name" wire:model="name" placeholder="Masukkan nama lengkap produk..."
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                    @error('name') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                </div>

                <div class="mt-5">
                    <label for="uom" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Unit of Measure (UoM) <span class="text-rose-600 dark:text-rose-500">*</span></label>
                    <div class="relative">
                        <select id="uom" wire:model="uom"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5 appearance-none">
                            @foreach($daftarUom as $kode => $label)
                                <option value="{{ $kode }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                    @error('uom') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                </div>

                <div class="mt-5">
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Foto Produk (Maks 5MB)</label>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-20 h-20 shrink-0 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center bg-slate-50 dark:bg-slate-900/50 overflow-hidden relative">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif ($existingImagePath)
                                <img src="{{ asset('storage/' . $existingImagePath) }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="image" class="w-6 h-6 text-slate-400"></i>
                            @endif
                            <div wire:loading wire:target="image" class="absolute inset-0 bg-white/70 dark:bg-slate-900/70 flex items-center justify-center">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-blue-500"></i>
                            </div>
                        </div>

                        <div class="flex-1">
                            <input type="file" wire:model="image" accept="image/*"
                                class="block w-full text-sm text-slate-600 dark:text-slate-300 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#10B981] dark:bg-emerald-500/20 dark:file:text-emerald-400 file:text-white hover:file:bg-emerald-600 cursor-pointer bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl outline-none transition-colors duration-300 ease-in-out">
                            <p class="text-[10px] text-slate-500 mt-1">Sistem akan secara otomatis memperkecil ukuran foto agar web tetap ringan.</p>
                            @error('image') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Inventory & Lokasi -->
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4 pb-2 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2 mt-8 transition-colors duration-300 ease-in-out">
                    <i data-lucide="layers" class="w-4 h-4 text-blue-500 transition-colors duration-300 ease-in-out"></i> Pengaturan Stok & Relasi
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="min_stock" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Batas Stok Minimum</label>
                        <input type="number" id="min_stock" wire:model="min_stock" min="0" placeholder="0"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                        @error('min_stock') <span class="text-rose-600 dark:text-rose-500 text-xs mt-1 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                        <p class="text-[10px] text-slate-500 mt-1 transition-colors duration-300 ease-in-out">Akan muncul notifikasi peringatan bila stok di bawah angka ini.</p>
                    </div>
                    <div>
                        <label for="max_stock" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Batas Stok Maksimum</label>
                        <input type="number" id="max_stock" wire:model="max_stock" min="0" placeholder="0"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label for="location" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Penempatan Lokasi</label>
                        <input type="text" id="location" wire:model="location" placeholder="Misal: Rak A-1"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5">
                    </div>
                    <div>
                        <label for="supplier_id" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Pemasok Default</label>
                        <div class="relative">
                            <select id="supplier_id" wire:model="supplier_id"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-4 py-2.5 appearance-none">
                                <option value="">-- Pilih Vendor Pemasok --</option>
                                @foreach($pemasok as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none transition-colors duration-300 ease-in-out"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="px-6 md:px-8 py-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3 transition-colors duration-300 ease-in-out">
            <a href="{{ route('produk.index') }}" class="px-5 py-2.5 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl transition-colors text-sm">Batal</a>
            <button type="submit" wire:loading.attr="disabled" wire:target="simpan, image" class="inline-flex justify-center items-center px-6 py-2.5 bg-[#10B981] dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-[#10B981] disabled:opacity-50 text-white font-bold rounded-xl transition-colors shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 text-sm">
                <i data-lucide="save" class="w-4 h-4 mr-2" wire:loading.remove wire:target="simpan, image"></i>
                <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="simpan, image" style="display: none;"></i>
                <span wire:loading.remove wire:target="simpan, image">Simpan Data</span>
                <span wire:loading wire:target="simpan" style="display: none;">Menyimpan...</span>
                <span wire:loading wire:target="image" style="display: none;">Mengunggah Foto...</span>
            </button>
        </div>
    </form>
</div>
