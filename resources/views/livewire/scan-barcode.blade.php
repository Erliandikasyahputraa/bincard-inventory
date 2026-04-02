<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2 transition-colors duration-300 ease-in-out">
            <i data-lucide="scan-line" class="w-5 h-5 text-emerald-500 transition-colors duration-300 ease-in-out"></i> Scanner Interaktif
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Arahkan kamera ke barcode/QR. Sistem akan mencari otomatis.</p>
    </div>
</x-slot:header>

<div x-data="scanBarcode()" x-init="init()" class="max-w-4xl mx-auto">
    <!-- Result Area (Appears on top when product is found or explicitly not found) -->
    @if($produkDitemukan)
        <div class="mb-8 flex-1 bg-gradient-to-br from-[#161B22] to-[#0D1117] border border-[#238636]/30 rounded-3xl p-6 lg:p-8 relative overflow-hidden ring-1 ring-[#238636]/50 shadow-[0_0_30px_rgba(35,134,54,0.1)] transition-all animate-in fade-in zoom-in duration-300">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-[#16A34A] dark:bg-emerald-500/10 rounded-full blur-3xl transition-colors duration-300 ease-in-out"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4 mb-6 pb-6 border-b border-slate-700/50">
                    <div>
                        <div class="flex items-center gap-2 mb-2 text-[#3FB950] font-bold text-xs uppercase tracking-widest transition-colors duration-300 ease-in-out">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i> Produk Dikenali
                        </div>
                        <h2 class="text-2xl font-black text-white leading-tight mb-2 transition-colors duration-300 ease-in-out">{{ $produkDitemukan['name'] }}</h2>
                        <div class="flex gap-4 text-xs font-mono text-slate-400 transition-colors duration-300 ease-in-out">
                            <span>SKU/Barcode: <span class="text-white transition-colors duration-300 ease-in-out">{{ $produkDitemukan['barcode'] }}</span></span>
                            <span>&bull;</span>
                            <span>Stok Valid: <span class="{{ $produkDitemukan['current_stock'] > 0 ? 'text-[#79C0FF]' : 'text-rose-400' }} text-sm font-black transition-colors duration-300 ease-in-out">{{ $produkDitemukan['current_stock'] }}</span></span>
                        </div>
                    </div>
                    
                    <button wire:click="resetScan" class="py-2.5 px-5 bg-white/10 hover:bg-white/20 text-white font-bold border border-white/20 rounded-xl transition-all flex items-center justify-center shrink-0 w-full lg:w-auto">
                        <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i> Scan Ulang
                    </button>
                </div>

                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-4 transition-colors duration-300 ease-in-out">Pilih Tujuan</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Tombol Barang Masuk -->
                    <a href="{{ route('barang-masuk.index') }}?product_id={{ $produkDitemukan['id'] }}" 
                       class="flex flex-col items-center justify-center p-5 bg-[#0D1117] border border-slate-800 hover:border-[#238636] hover:bg-[#238636]/10 rounded-2xl group transition-all text-center relative overflow-hidden">
                        <div class="w-12 h-12 rounded-full bg-[#238636]/20 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i data-lucide="arrow-down-left" class="w-6 h-6 text-[#3FB950] transition-colors duration-300 ease-in-out"></i>
                        </div>
                        <span class="text-white font-bold text-base transition-colors duration-300 ease-in-out">Barang Masuk</span>
                        <span class="text-xs text-slate-400 mt-1 transition-colors duration-300 ease-in-out">Tambah Stok Fisik</span>
                    </a>

                    <!-- Tombol Barang Keluar -->
                    <a href="{{ route('barang-keluar.index') }}?product_id={{ $produkDitemukan['id'] }}" 
                       class="flex flex-col items-center justify-center p-5 bg-[#0D1117] border border-slate-800 hover:border-orange-500/50 hover:bg-orange-500/10 rounded-2xl group transition-all text-center relative overflow-hidden">
                        <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i data-lucide="arrow-up-right" class="w-6 h-6 text-orange-400 transition-colors duration-300 ease-in-out"></i>
                        </div>
                        <span class="text-white font-bold text-base transition-colors duration-300 ease-in-out">Barang Keluar</span>
                        <span class="text-xs text-slate-400 mt-1 transition-colors duration-300 ease-in-out">Kurangi Stok & Surat Jalan</span>
                    </a>
                    
                    <!-- Tombol Stock Opname -->
                    <a href="{{ route('opname.index') }}?auto_create=1&cari_barcode={{ $produkDitemukan['barcode'] }}" 
                       class="flex flex-col items-center justify-center p-5 bg-[#0D1117] border border-slate-800 hover:border-[#79C0FF] hover:bg-[#79C0FF]/10 rounded-2xl group transition-all text-center relative overflow-hidden">
                        <div class="w-12 h-12 rounded-full bg-[#79C0FF]/20 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i data-lucide="check-square" class="w-6 h-6 text-[#79C0FF] transition-colors duration-300 ease-in-out"></i>
                        </div>
                        <span class="text-white font-bold text-base transition-colors duration-300 ease-in-out">Stock Opname</span>
                        <span class="text-xs text-slate-400 mt-1 transition-colors duration-300 ease-in-out">Audit & Sesuaikan Stok</span>
                    </a>
                </div>
            </div>
        </div>
    @elseif($barcodeTerpilih !== '' && count($hasilPencarian) === 0 && strlen($barcodeTerpilih) > 2)
        <!-- Result: Product Not Found -->
        <div class="mb-6 lg:mb-8 bg-white dark:bg-[#161B22] border border-rose-500/20 rounded-3xl p-8 flex flex-col items-center justify-center text-center animate-in shake duration-300 shadow-sm transition-colors duration-300 ease-in-out">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center mb-4 transition-colors duration-300 ease-in-out">
                <i data-lucide="file-x-2" class="w-8 h-8 text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out"></i>
            </div>
            <h3 class="text-lg font-bold text-rose-600 dark:text-rose-400 mb-1 transition-colors duration-300 ease-in-out">Produk Tidak Ditemukan</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 transition-colors duration-300 ease-in-out">Kata Kunci <span class="font-mono text-slate-900 dark:text-white bg-slate-100 dark:bg-[#0D1117] px-2 py-0.5 rounded transition-colors duration-300 ease-in-out">{{ $barcodeTerpilih }}</span> tidak terdaftar di sistem.</p>
            <button wire:click="resetScan" class="py-2.5 px-6 bg-slate-100 dark:bg-[#0D1117] hover:bg-slate-200 dark:hover:bg-[#21262D] text-slate-700 dark:text-slate-300 font-bold border border-slate-300 dark:border-slate-800 rounded-xl transition-all flex items-center justify-center shrink-0">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Bersihkan
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 xl:gap-8 {{ $produkDitemukan ? 'hidden' : 'grid' }}">
        
        <!-- Manual Input Area (Top / Left) -->
        <div class="relative group z-30 w-full mx-auto">
            <div class="flex flex-col h-full bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-3xl p-5 lg:p-6 lg:px-8 shadow-xl transition-colors duration-300 ease-in-out items-center justify-center text-center pb-8 border-t-4 border-t-blue-500">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center mb-4 transition-colors p-3 mt-2">
                    <i data-lucide="keyboard" class="w-full h-full text-blue-500 transition-colors duration-300 ease-in-out"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-200 mb-2 transition-colors duration-300 ease-in-out">Ketik Manual / Barcode Scanner Fisik</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6 transition-colors duration-300 ease-in-out px-4">Ketik kata kunci nama produk, atau klik Text Box lalu scan menggunakan Alat Barcode Scanner Fisik. Data akan otomatis tertembak.</p>

                <div class="relative group w-full mt-auto">
                    <i data-lucide="search" wire:loading.remove wire:target="barcodeTerpilih" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5 transition-colors duration-300 ease-in-out pointer-events-none"></i>
                    <i data-lucide="loader-2" wire:loading wire:target="barcodeTerpilih" class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 w-5 h-5 animate-spin"></i>
                    <input type="text" enterkeyhint="search" x-data x-on:keydown.enter.prevent="$el.blur()" wire:model.live.debounce.300ms="barcodeTerpilih" placeholder="Scan Barcode / SKU..."
                        class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-[#0D1117] focus:border-blue-500 dark:border-blue-500 focus:ring-2 focus:ring-blue-500/30 outline-none rounded-2xl font-mono text-base shadow-inner dark:shadow-none font-bold tracking-wider transition-all duration-300">

                    <!-- Dropdown Hasil Pencarian Fuzzy -->
                    @if(count($hasilPencarian) > 0)
                    <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto ring-1 ring-slate-900/5 z-50">
                        <ul class="py-2">
                            @foreach($hasilPencarian as $h)
                            <li>
                                <button type="button" wire:click="pilihProduk({{ $h['id'] }})" class="w-full text-left px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between border-b border-slate-100 dark:border-slate-800/50 last:border-0 group">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-[#3B82F6] dark:group-hover:text-blue-400 transition-colors">{{ $h['name'] }}</p>
                                        <p class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $h['barcode'] }}</p>
                                    </div>
                                    <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs px-3 py-1.5 rounded-xl font-bold flex items-center shadow-inner">
                                        <i data-lucide="package" class="w-3 h-3 mr-1.5 opacity-50"></i> {{ $h['current_stock'] }}
                                    </span>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Scanner Camera Area (Bottom / Right) -->
        <div wire:ignore class="bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-3xl p-4 lg:p-6 lg:px-8 shadow-xl relative overflow-hidden flex flex-col items-center transition-colors duration-300 ease-in-out w-full mx-auto border-t-4 border-t-emerald-500">
            <div class="mb-5 text-center mt-2">
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-200 flex items-center justify-center gap-2 transition-colors duration-300 ease-in-out">
                    <i data-lucide="scan-line" class="w-5 h-5 text-emerald-500"></i>
                    Kamera HP / Perangkat
                </h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300 ease-in-out px-4">Jika Anda hanya memiliki HP, tekan Mulai Kamera untuk menggunakan lensa smartphone Anda sebagai pengganti Barcode Scanner.</p>
            </div>
            
            <div id="reader" x-ref="reader" class="w-full bg-slate-100 dark:bg-slate-950 rounded-2xl overflow-hidden aspect-square lg:aspect-[16/9] border-2 border-slate-200 dark:border-slate-800 transition-all duration-300 flex items-center justify-center max-w-2xl mx-auto shadow-inner">
                <!-- Html5Qrcode injects video here -->
            </div>
            
            <div class="mt-6 flex gap-3 w-full max-w-md justify-center relative z-10 mb-2">
                <button type="button" @click="startScan()" class="flex-1 py-3.5 px-4 bg-[#16A34A] dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-[#16A34A] text-white font-bold rounded-xl transition-all shadow-lg flex items-center justify-center text-sm ring-1 ring-emerald-500/50">
                    <i data-lucide="camera" class="w-4 h-4 mr-2"></i> Mulai Kamera
                </button>
                <button type="button" @click="stopScan()" class="py-3 px-6 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-500 font-bold border border-rose-200 dark:border-rose-500/20 rounded-xl transition-all flex items-center justify-center">
                    <i data-lucide="power-off" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('scan-reset', () => {
                // When scan resets, make sure Alpine component knows about it
            });
        });

        function scanBarcode() {
            let html5Qr = null;
            return {
                init() {
                    if (typeof Html5Qrcode === 'undefined') return;
                    html5Qr = new Html5Qrcode('reader');
                },
                startScan() {
                    if (!html5Qr) return;
                    Html5Qrcode.getCameras().then(cameras => {
                        if (cameras.length) {
                            html5Qr.start({ facingMode: 'environment' }, { fps: 10, qrbox: { width: 250, height: 250 } }, (decodedText) => {
                                this.onScan(decodedText);
                            }, () => {});
                        }
                    }).catch(() => {
                        Swal.fire({
                            title: 'Kamera Tidak Ditemukan',
                            text: 'Pastikan izin kamera diberikan pada browser.',
                            icon: 'error',
                            background: document.documentElement.classList.contains('dark') ? '#161B22' : '#ffffff', 
                            color: document.documentElement.classList.contains('dark') ? '#c9d1d9' : '#1e293b'
                        });
                    });
                },
                stopScan() {
                    if (html5Qr && html5Qr.isScanning) html5Qr.stop();
                },
                onScan(barcode) {
                    // Logic to extract barcode if a full BINGO URL is scanned via camera
                    let decodedBarcode = barcode;
                    try {
                        if (barcode.startsWith('http')) {
                            let url = new URL(barcode);
                            if (url.searchParams.has('barcode')) {
                                decodedBarcode = url.searchParams.get('barcode');
                            }
                        }
                    } catch (e) {
                        // Tolerate
                    }
                    
                    @this.set('barcodeTerpilih', decodedBarcode);
                    @this.call('setBarcodeDariScan', decodedBarcode);
                    
                    // Pause camera optionally to save battery, but wait UI handles hidden
                    this.stopScan(); 
                }
            };
        }
    </script>
</div>

