<div x-data="scanBarcode()" x-init="init()" class="max-w-4xl mx-auto">
    <div class="mb-6 text-center lg:text-left transition-colors duration-300 ease-in-out">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Scanner Interaktif</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 mb-4 transition-colors duration-300 ease-in-out">Arahkan kamera ke barcode/QR. Sistem akan mencari produk secara otomatis.</p>
        
        <!-- Manual Input Search (Always visible) -->
        <div class="relative group max-w-xl mx-auto lg:mx-0 z-50">
            <i data-lucide="keyboard" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 w-5 h-5 transition-colors duration-300 ease-in-out"></i>
            <input type="text" wire:model.live.debounce.300ms="barcodeTerpilih" wire:keydown.enter.prevent="cariProduk" placeholder="Ketik kode barcode..."
                class="w-full pl-12 pr-4 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-2xl text-slate-800 dark:text-slate-200 placeholder-slate-500 focus:bg-slate-50 dark:focus:bg-slate-950 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all duration-300 font-mono text-sm shadow-sm dark:shadow-none">

            <!-- Dropdown Hasil Pencarian Fuzzy -->
            @if(count($hasilPencarian) > 0)
            <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto ring-1 ring-slate-900/5">
                <ul class="py-2">
                    @foreach($hasilPencarian as $h)
                    <li>
                        <button type="button" wire:click="pilihProduk({{ $h['id'] }})" class="w-full text-left px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center justify-between border-b border-slate-100 dark:border-slate-800/50 last:border-0 group">
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $h['name'] }}</p>
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

    <!-- Result Area (Appears on top when product is found) -->
    @if($produkDitemukan)
        <div class="mb-8 flex-1 bg-gradient-to-br from-[#161B22] to-[#0D1117] border border-[#238636]/30 rounded-3xl p-6 lg:p-8 relative overflow-hidden ring-1 ring-[#238636]/50 shadow-[0_0_30px_rgba(35,134,54,0.1)] transition-all animate-in fade-in zoom-in duration-300">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-600 dark:bg-emerald-500/10 rounded-full blur-3xl transition-colors duration-300 ease-in-out"></div>
            
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
                    <a href="{{ route('opname.index') }}?product_id={{ $produkDitemukan['id'] }}" 
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
        <div class="mb-8 bg-white dark:bg-[#161B22] border border-rose-500/20 rounded-3xl p-8 flex flex-col items-center justify-center text-center animate-in shake duration-300 shadow-sm transition-colors duration-300 ease-in-out">
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

    <div class="grid grid-cols-1 gap-8 {{ $produkDitemukan ? 'hidden' : 'block' }}">
        
        <!-- Scanner Camera Area -->
        <div wire:ignore class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl relative overflow-hidden flex flex-col items-center transition-colors duration-300 ease-in-out mx-auto w-full lg:w-2/3">
            
            <div id="reader" x-ref="reader" class="w-full bg-slate-950 rounded-2xl overflow-hidden aspect-video lg:aspect-square border-2 border-dashed border-blue-500 dark:border-blue-400/30 transition-all duration-300 flex items-center justify-center">
                <!-- Html5Qrcode injects video here -->
            </div>
            
            <div class="mt-6 flex gap-3 w-full justify-center relative z-10">
                <button type="button" @click="startScan()" class="flex-1 py-3 px-4 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white font-bold rounded-xl transition-all shadow-lg flex items-center justify-center">
                    <i data-lucide="camera" class="w-4 h-4 mr-2"></i> Mulai Kamera
                </button>
                <button type="button" @click="stopScan()" class="py-3 px-5 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-500 font-bold border border-rose-200 dark:border-rose-500/20 rounded-xl transition-all flex items-center justify-center">
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
                    @this.set('barcodeTerpilih', barcode);
                    @this.call('setBarcodeDariScan', barcode);
                    
                    // Pause camera optionally to save battery, but wait UI handles hidden
                    this.stopScan(); 
                }
            };
        }
    </script>
</div>
