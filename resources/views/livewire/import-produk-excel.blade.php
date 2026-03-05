<div class="w-full">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Import Produk (Excel)</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Tambahkan data produk sekaligus melalui file spreadsheet.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('produk.template') }}" class="inline-flex justify-center items-center px-4 py-2 bg-[#21262D] border border-[#30363D] hover:bg-[#30363D] text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                <i data-lucide="download" class="w-4 h-4 mr-2 text-[#3FB950]"></i> Download Template
            </a>
            <a href="{{ route('produk.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-500 font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="bg-[#161B22] border border-[#30363D] rounded-2xl shadow-xl overflow-hidden max-w-3xl">
        <div class="p-6 md:p-8 space-y-6">
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 flex gap-3">
                <i data-lucide="info" class="w-5 h-5 text-blue-400 shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-sm border-none font-bold text-blue-400 mb-1">Panduan Import</h4>
                    <p class="text-xs text-blue-200/70 leading-relaxed">
                        Gunakan tombol <strong>Download Template</strong> di atas untuk mendapatkan format Excel yang benar.
                        Kolom yang wajib dan opsional sudah tertera di sana. Jangan ubah nama header (baris pertama) pada template agar sistem dapat membaca data dengan benar.
                    </p>
                </div>
            </div>

            <form wire:submit="simpan">
                <div class="mb-6">
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" 
                        class="w-full text-sm text-slate-700 dark:text-slate-300 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#1F6FEB] file:text-white hover:file:bg-[#388BFD] cursor-pointer bg-[#0D1117] border border-[#30363D] rounded-xl outline-none">
                    @error('file') <span class="text-rose-500 text-xs mt-2 block font-medium flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</span> @enderror
                </div>
                
                <div class="flex justify-end pt-4 border-t border-[#30363D]">
                    <button type="submit" wire:loading.attr="disabled" wire:target="simpan" class="inline-flex justify-center items-center px-6 py-2.5 bg-[#238636] hover:bg-[#2EA043] disabled:opacity-50 text-slate-900 dark:text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#238636]/20 text-sm">
                        <i data-lucide="upload-cloud" class="w-4 h-4 mr-2" wire:loading.remove wire:target="simpan"></i> 
                        <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="simpan" style="display: none;"></i>
                        <span wire:loading.remove wire:target="simpan">Mulai Import</span>
                        <span wire:loading wire:target="simpan" style="display: none;">Mengunggah...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(count($barisGagal) > 0)
        <div class="mt-6 bg-[#161B22] border border-rose-500/30 rounded-2xl shadow-xl overflow-hidden max-w-3xl relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-rose-500"></div>
            <div class="p-6">
                <h2 class="font-bold text-rose-400 flex items-center gap-2 mb-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    Ada {{ count($barisGagal) }} Baris yang Gagal Diimport
                </h2>
                <div class="bg-[#0D1117] border border-[#30363D] rounded-xl max-h-64 overflow-y-auto overflow-x-auto p-4">
                    <ul class="space-y-2 text-sm">
                        @foreach(array_slice($barisGagal, 0, 50) as $g)
                            <li class="flex gap-3 text-slate-700 dark:text-slate-300">
                                <span class="font-mono text-rose-400 shrink-0">Baris {{ $g['baris'] }}:</span> 
                                <span>{{ $g['alasan'] }}</span>
                            </li>
                        @endforeach
                        @if(count($barisGagal) > 50)
                            <li class="pt-2 text-slate-500 text-xs text-center border-t border-[#30363D] mt-2">
                                ... dan {{ count($barisGagal) - 50 }} error lainnya tidak ditampilkan.
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
