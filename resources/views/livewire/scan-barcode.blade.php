<div x-data="scanBarcode()" x-init="init()" class="max-w-4xl mx-auto">
    <div class="mb-6 text-center lg:text-left transition-colors duration-300 ease-in-out">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Scanner Interaktif</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 transition-colors duration-300 ease-in-out">Arahkan kamera ke barcode/QR. Sistem akan mencari produk secara otomatis.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Scanner Camera Area -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl relative overflow-hidden flex flex-col items-center transition-colors duration-300 ease-in-out">
            
            <div id="reader" class="w-full bg-black rounded-2xl overflow-hidden aspect-square border-2 border-dashed border-blue-500 dark:border-blue-400/30 {{ $produkDitemukan ? 'opacity-50 grayscale' : '' }} transition-all duration-300 flex items-center justify-center">
                <!-- Html5Qrcode injects video here -->
            </div>
            
            <div class="mt-6 flex gap-3 w-full justify-center relative z-10">
                <button type="button" @click="startScan()" class="flex-1 py-3 px-4 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white font-bold rounded-xl transition-all shadow-lg flex items-center justify-center">
                    <i data-lucide="camera" class="w-4 h-4 mr-2"></i> Mulai Kamera
                </button>
                <button type="button" @click="stopScan()" class="py-3 px-5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-500 font-bold border border-rose-500/20 rounded-xl transition-all flex items-center justify-center">
                    <i data-lucide="power-off" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Result / Manual Input Area -->
        <div class="space-y-6 flex flex-col">
            
            <!-- Manual Input Search -->
            <div class="relative group">
                <i data-lucide="keyboard" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 w-5 h-5 transition-colors duration-300 ease-in-out"></i>
                <input type="text" wire:model.live.debounce.300ms="barcodeTerpilih" placeholder="Atau ketik Barcode / SKU secara manual..."
                    class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-2xl text-slate-800 dark:text-slate-200 placeholder-slate-500 focus:bg-white dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all duration-300 font-mono text-sm">
            </div>

            <!-- Pre-loading or Idle State (hidden if found/error) -->
            @if(!$produkDitemukan && $barcodeTerpilih === '')
                <div class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl p-10 text-center opacity-50 transition-colors duration-300 ease-in-out">
                    <i data-lucide="scan-line" class="w-16 h-16 text-slate-500 mb-4 transition-colors duration-300 ease-in-out"></i>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400 tracking-wider uppercase transition-colors duration-300 ease-in-out">Menunggu Hasil Scan...</p>
                </div>
            @endif

            <!-- Result: Product Found -->
            @if($produkDitemukan)
                <div class="flex-1 bg-gradient-to-br from-[#161B22] to-[#0D1117] border border-[#238636]/30 rounded-3xl p-8 relative overflow-hidden ring-1 ring-[#238636]/50 shadow-[0_0_30px_rgba(35,134,54,0.1)] transition-all animate-in fade-in zoom-in duration-300">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-600 dark:bg-emerald-500/10 rounded-full blur-3xl transition-colors duration-300 ease-in-out"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4 text-[#3FB950] font-bold text-xs uppercase tracking-widest transition-colors duration-300 ease-in-out">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i> Produk Dikenali
                        </div>
                        
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white leading-tight mb-2 transition-colors duration-300 ease-in-out">{{ $produkDitemukan['name'] }}</h2>
                        <div class="flex gap-4 text-xs font-mono text-slate-500 dark:text-slate-400 mb-8 border-b border-slate-200 dark:border-slate-800 pb-6 transition-colors duration-300 ease-in-out">
                            <span>SKU: <span class="text-slate-900 dark:text-white transition-colors duration-300 ease-in-out">{{ $produkDitemukan['barcode'] }}</span></span>
                            <span>&bull;</span>
                            <span>Stok Valid: <span class="{{ $produkDitemukan['current_stock'] > 0 ? 'text-blue-500' : 'text-rose-600 dark:text-rose-400' }} text-sm font-black transition-colors duration-300 ease-in-out">{{ $produkDitemukan['current_stock'] }}</span></span>
                        </div>

                        <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider mb-4 transition-colors duration-300 ease-in-out">Pilih Jenis Transaksi</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Tombol Barang Masuk Seamless -->
                            <a href="{{ route('barang-masuk.index') }}?product_id={{ $produkDitemukan['id'] }}" 
                               class="flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-[#238636] hover:bg-emerald-600 dark:bg-emerald-500/10 rounded-2xl group transition-all text-center">
                                <div class="w-10 h-10 rounded-full bg-emerald-600 dark:bg-emerald-500/20 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i data-lucide="arrow-down-left" class="w-5 h-5 text-[#3FB950] transition-colors duration-300 ease-in-out"></i>
                                </div>
                                <span class="text-slate-900 dark:text-white font-bold text-sm transition-colors duration-300 ease-in-out">Masuk</span>
                                <span class="text-[10px] text-slate-500 mt-1 transition-colors duration-300 ease-in-out">Tambah Stok</span>
                            </a>

                            <!-- Tombol Barang Keluar Seamless -->
                            <a href="{{ route('barang-keluar.index') }}?product_id={{ $produkDitemukan['id'] }}" 
                               class="flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-orange-500/50 hover:bg-orange-500/10 rounded-2xl group transition-all text-center">
                                <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i data-lucide="arrow-up-right" class="w-5 h-5 text-orange-400 transition-colors duration-300 ease-in-out"></i>
                                </div>
                                <span class="text-slate-900 dark:text-white font-bold text-sm transition-colors duration-300 ease-in-out">Keluar</span>
                                <span class="text-[10px] text-slate-500 mt-1 transition-colors duration-300 ease-in-out">Kurangi Stok</span>
                            </a>
                        </div>
                    </div>
                </div>
                
            <!-- Result: Product Not Found -->
            @elseif($barcodeTerpilih !== '')
                <div class="flex-1 bg-rose-500/5 border border-rose-500/20 rounded-3xl p-8 flex flex-col items-center justify-center text-center animate-in shake duration-300 transition-colors duration-300 ease-in-out">
                    <div class="w-16 h-16 rounded-2xl bg-rose-500/10 flex items-center justify-center mb-4 transition-colors duration-300 ease-in-out">
                        <i data-lucide="file-x-2" class="w-8 h-8 text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out"></i>
                    </div>
                    <h3 class="text-lg font-bold text-rose-600 dark:text-rose-400 mb-1 transition-colors duration-300 ease-in-out">Produk Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors duration-300 ease-in-out">Barcode/SKU <span class="font-mono text-slate-900 dark:text-white bg-black/20 px-1 rounded transition-colors duration-300 ease-in-out">{{ $barcodeTerpilih }}</span> tidak terdaftar di sistem.</p>
                </div>
            @endif
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
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
                            html5Qr.start({ facingMode: 'environment' }, { fps: 10, qrbox: 250 }, (decodedText) => {
                                this.onScan(decodedText);
                            }, () => {});
                        }
                    }).catch(() => {
                        Swal.fire({
                            title: 'Kamera Tidak Ditemukan',
                            text: 'Pastikan izin kamera diberikan pada browser browser.',
                            icon: 'error',
                            background: '#161B22', color: '#c9d1d9'
                        });
                    });
                },
                stopScan() {
                    if (html5Qr && html5Qr.isScanning) html5Qr.stop();
                },
                onScan(barcode) {
                    @this.set('barcodeTerpilih', barcode);
                    @this.call('setBarcodeDariScan', barcode);
                    // Optional: play a beep sound here
                }
            };
        }
    </script>
</div>
