<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Import Produk (Excel)</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Tambahkan data produk sekaligus melalui file spreadsheet.</p>
    </div>
</x-slot:header>

<div class="w-full">
    <div class="mb-6 flex justify-end gap-3">
        <a href="{{ route('produk.template') }}" class="inline-flex justify-center items-center px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
            <i data-lucide="download" class="w-4 h-4 mr-2 text-[#3FB950] transition-colors duration-300 ease-in-out"></i> Download Template
        </a>
        <a href="{{ route('produk.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-600 dark:text-rose-500 font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden max-w-3xl transition-colors duration-300 ease-in-out">
        <div class="p-6 md:p-8 space-y-6">
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 flex gap-3 transition-colors duration-300 ease-in-out">
                <i data-lucide="info" class="w-5 h-5 text-[#10B981] dark:text-blue-400 shrink-0 mt-0.5 transition-colors duration-300 ease-in-out"></i>
                <div>
                    <h4 class="text-sm border-none font-bold text-[#10B981] dark:text-blue-400 mb-1 transition-colors duration-300 ease-in-out">Panduan Import</h4>
                    <p class="text-xs text-slate-600 dark:text-blue-200/80 leading-relaxed transition-colors duration-300 ease-in-out">
                        Gunakan tombol <strong>Download Template</strong> di atas untuk mendapatkan format Excel yang benar.
                        Kolom yang wajib dan opsional sudah tertera di sana. Jangan ubah nama header (baris pertama) pada template agar sistem dapat membaca data dengan benar.
                        Jika data dengan <strong>barcode + nama</strong> yang sama sudah ada, sistem akan meminta konfirmasi apakah ingin <strong>Skip</strong> atau <strong>Overwrite</strong>.
                    </p>
                </div>
            </div>

            <form wire:submit="simpan">
                <div class="mb-6">
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" wire:model.live="file" accept=".xlsx,.xls,.csv" 
                        class="w-full text-sm text-slate-600 dark:text-slate-300 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#10B981] dark:bg-blue-500 file:text-white hover:file:bg-[#388BFD] cursor-pointer bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl outline-none transition-colors duration-300 ease-in-out">
                    
                    <div wire:loading wire:target="file" class="mt-2 text-sm text-blue-500 font-medium flex items-center">
                        <i data-lucide="loader-2" class="w-4 h-4 mr-1.5 animate-spin"></i> File sedang diunggah ke memori...
                    </div>
                    @error('file') <span class="text-rose-600 dark:text-rose-500 text-xs mt-2 block font-medium flex items-center gap-1 transition-colors duration-300 ease-in-out"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                </div>
                
                <div class="flex flex-col sm:flex-row items-center justify-end pt-4 gap-3 border-t border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out">
                    @if(!$file)
                        <span class="text-xs text-rose-500 italic mr-auto">Harap unggah file Excel Anda terlebih dahulu sebelum memproses.*</span>
                    @endif
                    <button type="submit" wire:loading.attr="disabled" {{ !$file ? 'disabled' : '' }} class="inline-flex justify-center items-center px-6 py-2.5 bg-[#10B981] dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-[#10B981] disabled:opacity-50 disabled:cursor-not-allowed text-slate-900 dark:text-white font-bold rounded-xl transition-colors shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 text-sm w-full sm:w-auto">
                        <i data-lucide="upload-cloud" class="w-4 h-4 mr-2" wire:loading.remove wire:target="simpan"></i> 
                        <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="simpan" style="display: none;"></i>
                        <span wire:loading.remove wire:target="simpan">Mulai Import</span>
                        <span wire:loading wire:target="simpan" style="display: none;">Memproses file...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div wire:loading wire:target="simpan,pilihTindakanDuplikat" class="mt-4 max-w-3xl">
        <div class="animate-pulse bg-slate-100 dark:bg-slate-800 rounded-xl h-24"></div>
    </div>

    @if($menungguKonfirmasi && count($duplikatDitemukan) > 0)
    <div class="mt-6 max-w-3xl bg-white dark:bg-slate-900 border border-[#D1D5DB] dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden transition-colors duration-300 ease-in-out">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center shrink-0">
                    <i data-lucide="triangle-alert" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-900 dark:text-white text-sm">Ditemukan {{ $totalDuplikat }} data duplikat</p>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Barcode + nama sudah ada. Pilih tindakan untuk semua.</p>
                </div>
            </div>
            <span class="shrink-0 text-xs font-semibold font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
                {{ min(count($duplikatPreview), 30) }} / {{ $totalDuplikat }}
            </span>
        </div>

        {{-- List --}}
        <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto">
            @foreach(array_slice($duplikatPreview, 0, 30) as $d)
                @php $isAkanDitimpa = $d['status'] === 'akan_ditimpa'; @endphp
                <div class="px-6 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors duration-150">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[11px] font-mono font-semibold text-slate-500 dark:text-slate-400">Baris {{ $d['baris'] }}</span>
                        <span class="font-mono font-semibold text-sm text-slate-800 dark:text-slate-200">{{ $d['barcode'] }}</span>
                        <span class="text-slate-300 dark:text-slate-600">—</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300 flex-1 min-w-0 truncate">{{ $d['name'] }}</span>
                        @if($isAkanDitimpa)
                            <span class="ml-auto shrink-0 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-amber-500/10 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                Akan Ditimpa
                            </span>
                        @else
                            <span class="ml-auto shrink-0 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                Tidak Berubah
                            </span>
                        @endif
                    </div>
                    @if(count($d['changes']) > 0)
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1">
                            @foreach($d['changes'] as $change)
                                @php
                                    $oldValue = ($change['old'] === null || $change['old'] === '') ? '(kosong)' : $change['old'];
                                    $newValue = ($change['new'] === null || $change['new'] === '') ? '(kosong)' : $change['new'];
                                @endphp
                                <div class="text-xs flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                    <span class="font-medium text-slate-600 dark:text-slate-300">{{ $change['label'] }}:</span>
                                    <span class="line-through text-rose-500 dark:text-rose-400">{{ $oldValue }}</span>
                                    <span class="text-slate-400 dark:text-slate-600">→</span>
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $newValue }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 justify-end">
            <p class="text-xs text-slate-500 dark:text-slate-400 italic mr-auto hidden sm:block">
                Berlaku untuk semua <strong class="text-slate-700 dark:text-slate-200 font-semibold">{{ $totalDuplikat }}</strong> data duplikat.
            </p>
            <button type="button"
                wire:click="pilihTindakanDuplikat('skip')"
                wire:loading.attr="disabled"
                wire:target="pilihTindakanDuplikat"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Abaikan (Skip)
            </button>
            <button type="button"
                wire:click="pilihTindakanDuplikat('overwrite')"
                wire:loading.attr="disabled"
                wire:target="pilihTindakanDuplikat"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-[#10B981] dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-[#10B981] text-white shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i data-lucide="save" class="w-4 h-4"></i> Timpa Data Lama
            </button>
        </div>
    </div>
    @endif

    @if(count($barisGagal) > 0)
        <div class="mt-6 bg-white dark:bg-slate-900 border border-rose-500/30 rounded-2xl shadow-xl overflow-hidden max-w-3xl relative transition-colors duration-300 ease-in-out">
            <div class="absolute top-0 left-0 w-1 h-full bg-rose-500 transition-colors duration-300 ease-in-out"></div>
            <div class="p-6">
                <h2 class="font-bold text-rose-600 dark:text-rose-400 flex items-center gap-2 mb-3 transition-colors duration-300 ease-in-out">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    Ada {{ $totalGagal }} Baris yang Gagal Diimport
                </h2>
                <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl max-h-64 overflow-y-auto overflow-x-auto p-4 transition-colors duration-300 ease-in-out">
                    <ul class="space-y-2 text-sm transition-colors duration-300 ease-in-out">
                        @foreach(array_slice($barisGagal, 0, 50) as $g)
                            <li class="flex gap-3 text-slate-600 dark:text-slate-300 transition-colors duration-300 ease-in-out">
                                <span class="font-mono text-rose-600 dark:text-rose-400 shrink-0 transition-colors duration-300 ease-in-out">Baris {{ $g['baris'] }}:</span> 
                                <span>{{ $g['alasan'] }}</span>
                            </li>
                        @endforeach
                        @if($totalGagal > 50)
                            <li class="pt-2 text-slate-500 text-xs text-center border-t border-slate-200 dark:border-slate-800 mt-2 transition-colors duration-300 ease-in-out">
                                ... dan {{ $totalGagal - 50 }} error lainnya tidak ditampilkan.
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
