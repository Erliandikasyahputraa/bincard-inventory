<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Label — {{ $product->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .mono { font-family: 'JetBrains Mono', 'Courier New', monospace; }

        /* ── Label Size Targets ── */
        .label-a4      { width: 21cm;   height: 29.7cm; }
        .label-10x7    { width: 10cm;   height: 7cm; }
        .label-3x10    { width: 10.5cm; height: 3cm; }
        .label-5x5     { width: 5cm;    height: 5cm; }

        @media print {
            @page { margin: 0; }
            body  { margin: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; background: transparent !important; }
            body > *:not(#printWrapper):not(#tempPrintArea) { display: none !important; }
            .no-print { display: none !important; }
            .print-label { box-shadow: none !important; border-radius: 0 !important; transform: none !important; margin: 0 !important; }
            .print-wrapper { transform: none !important; height: auto !important; margin: 0 !important; padding: 0 !important; }

            /* Grid layout for multiple copies on A4 */
            .bulk-container { padding: 0; gap: 0; display: block; text-align: center; }
            .print-item { 
                page-break-inside: avoid; 
                break-inside: avoid;
                display: inline-block !important;
                vertical-align: top;
            }
            .print-item[data-size="3x10.5"] { margin: 0.15cm !important; }
            .print-item[data-size="5x5"] { margin: 0.4cm !important; }
            .print-item[data-size="10x7"] { margin: 0.3cm !important; }
            .print-item[data-size="a4"] { margin: 0 auto !important; display: block !important; }

            .print-multiple .label-3x10 { width: 9.1cm !important; height: auto !important; }
            .print-multiple .label-3x10 .w-\[3cm\] { width: 2.7cm !important; }
            .print-multiple .label-3x10 .w-\[4cm\] { width: 3.1cm !important; }
        }

        /* barcode striped lines (purely CSS decorative) */
        .barcode-lines {
            height: 36px;
            display: flex;
            align-items: flex-end;
            gap: 1.5px;
        }
        .barcode-lines span {
            display: inline-block;
            background: #111;
            border-radius: 1px;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-start p-6 print:bg-white print:p-0 print:block">

    {{-- ───── CONTROLS (hidden on print) ───── --}}
    <div class="no-print w-full max-w-2xl mb-6 bg-white rounded-2xl shadow-lg border border-slate-200 p-5">
        <h2 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Pengaturan Cetak
        </h2>

        {{-- Size selector --}}
        <div class="mb-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Ukuran Label</p>
            <div class="flex gap-3 flex-wrap" id="sizeButtons">
                <button onclick="setSize('a4')" data-size="a4"
                    class="size-btn px-4 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors border-slate-300 text-slate-700 bg-white">
                    📄 A4 Penuh
                </button>
                <button onclick="setSize('10x7')" data-size="10x7"
                    class="size-btn px-4 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors border-slate-300 text-slate-700 bg-white">
                    🏷️ Label 10×7 cm
                </button>
                <button onclick="setSize('3x10')" data-size="3x10"
                    class="size-btn px-4 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors border-slate-300 text-slate-700 bg-white">
                    📏 Label 3×10.5 cm
                </button>
                <button onclick="setSize('5x5')" data-size="5x5"
                    class="size-btn px-4 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors border-slate-300 text-slate-700 bg-white">
                    🔖 Label 5×5 cm
                </button>
            </div>
        </div>

        {{-- Copy count --}}
        <div class="mb-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Jumlah Cetak (Perbanyak)</p>
            <div class="flex items-center gap-3">
                <input type="number" id="copyCount" value="1" min="1" max="100" class="w-24 px-4 py-2 border-2 border-slate-300 rounded-xl text-center font-bold text-slate-700 outline-none focus:border-blue-500 transition-colors">
                <span class="text-xs font-medium text-slate-500 leading-tight">Lembar label<br/>(Isi lebih dari 1 untuk cetak massal di HVS)</span>
            </div>
        </div>

        <div class="flex gap-3">
            <button onclick="triggerPrint()"
                class="flex-1 py-2.5 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-colors shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Sekarang
            </button>
            <button onclick="window.close()"
                class="py-2.5 px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-colors">
                Tutup
            </button>
        </div>
    </div>

    {{-- ───── PRINTABLE LABEL ───── --}}
    <div class="print-wrapper w-full flex justify-center transition-transform origin-top" id="printWrapper">
        @php
            $systemName = config('app.name', 'BINGO');
        @endphp

        {{-- A4 layout --}}
        <div id="label-a4" class="print-label label-a4 bg-white shadow-2xl rounded-xl flex flex-col items-center justify-center p-12 text-center">
            {{-- System name --}}
            <p class="mono text-xs font-bold uppercase tracking-[4px] text-slate-400 mb-6">{{ strtoupper($systemName) }}</p>

            {{-- Images: QR & Photo --}}
            <div class="flex items-center gap-6 mb-6">
                @if($product->image_path)
                    <div class="bg-white p-2 border-4 border-slate-200 rounded-xl shadow-sm">
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="Foto" class="w-[320px] h-[320px] object-cover rounded-lg" />
                    </div>
                @else
                    <div class="bg-white p-2 border-4 border-slate-200 rounded-xl shadow-sm flex items-center justify-center w-[328px] h-[328px]">
                        <svg class="text-slate-300 w-28 h-28" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 7.703 4.734a2 2 0 0 0 1.994 0L20.7 7"/><path d="m7.5 4.27 9 5.15"/></svg>
                    </div>
                @endif
                <div class="bg-white p-3 border-4 border-slate-900 rounded-xl shadow-sm">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=600x600&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                         alt="QR Code" class="w-[320px] h-[320px] object-contain" />
                </div>
            </div>

            {{-- Product name --}}
            <h1 class="text-5xl font-black text-gray-900 leading-tight uppercase tracking-tight max-w-lg mb-2">
                {{ $product->name }}
            </h1>

            {{-- SKU --}}
            <p class="mono text-2xl text-slate-600 tracking-widest font-semibold mb-4">{{ $product->sku }}</p>

            {{-- Barcode visual + text --}}
            <div class="flex flex-col items-center mt-2 mb-4">
                <div class="barcode-lines" id="barcodeA4"></div>
                <p class="mono text-lg font-bold tracking-[6px] text-slate-800 mt-2">{{ $product->barcode }}</p>
            </div>

            {{-- Info row --}}
            <div class="flex items-center gap-6 mt-4 border-t border-slate-200 pt-4 text-sm text-slate-500">
                @if($product->location)
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Rak: <strong class="text-slate-800">{{ $product->location }}</strong>
                </span>
                @endif
                @if($product->uom)
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    Satuan: <strong class="text-slate-800">{{ $product->uom }}</strong>
                </span>
                @endif
            </div>
        </div>

        {{-- 10×7 cm layout: structured grid --}}
        <div id="label-10x7" class="print-label label-10x7 bg-white shadow-2xl rounded-lg hidden overflow-hidden">
            <div class="flex flex-col h-full w-full p-2 box-border">
                {{-- Brand --}}
                <p class="mono text-[8px] font-bold uppercase tracking-[3px] text-slate-400 mb-1 text-center">{{ strtoupper($systemName) }}</p>
                
                {{-- Images Row: Photo & QR side by side, same size --}}
                <div class="flex items-center justify-center gap-4 mb-2">
                    <div class="bg-white border-2 border-slate-100 rounded-md overflow-hidden flex items-center justify-center shrink-0" style="width:3.2cm; height:3.2cm;">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="Foto" class="w-full h-full object-cover" />
                        @else
                            <i data-lucide="package" class="w-10 h-10 text-slate-200"></i>
                        @endif
                    </div>
                    <div class="bg-white border-2 border-slate-900 rounded-md overflow-hidden flex items-center justify-center shrink-0" style="width:3.2cm; height:3.2cm;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                             alt="QR" class="w-full h-full object-contain p-1" />
                    </div>
                </div>

                {{-- Product Info Section --}}
                <div class="flex-1 flex flex-col items-center justify-center text-center">
                    <h2 class="text-[11px] font-black text-gray-900 uppercase leading-tight line-clamp-2 w-full px-2 mb-1">
                        {{ $product->name }}
                    </h2>
                    <p class="mono text-[8px] font-bold text-slate-500 tracking-wider mb-1">{{ $product->sku }}</p>
                    
                    {{-- Barcode --}}
                    <div class="flex flex-col items-center">
                        <div class="barcode-lines" id="barcode10x7" style="height:14px; gap:1px; justify-content:center;"></div>
                        <p class="mono text-[8px] font-bold tracking-[2px] text-slate-800 mt-0.5">{{ $product->barcode }}</p>
                    </div>

                    @if($product->location || $product->uom)
                    <p class="text-[7px] text-slate-400 mt-1">
                        @if($product->location)Rak: <strong class="text-slate-600">{{ $product->location }}</strong>@endif
                        @if($product->location && $product->uom) · @endif
                        @if($product->uom)Satuan: <strong class="text-slate-600">{{ $product->uom }}</strong>@endif
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 5×5 cm layout: balanced grid --}}
        <div id="label-5x5" class="print-label label-5x5 bg-white shadow-2xl rounded-lg hidden overflow-hidden">
            <div class="flex flex-col h-full w-full p-1.5 box-border text-center">
                {{-- Brand --}}
                <p class="mono text-[6px] font-bold uppercase tracking-[2px] text-slate-400 mb-1">{{ strtoupper($systemName) }}</p>

                {{-- Row 1: Small Photo & Large QR side by side --}}
                <div class="flex items-center justify-center gap-2 mb-1.5">
                    <div class="bg-white border border-slate-100 rounded flex items-center justify-center shrink-0" style="width:2cm; height:2cm;">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="Foto" class="w-full h-full object-cover" />
                        @else
                            <i data-lucide="package" class="w-6 h-6 text-slate-100"></i>
                        @endif
                    </div>
                    <div class="bg-white border-2 border-slate-900 rounded flex items-center justify-center shrink-0" style="width:2cm; height:2cm;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                             alt="QR" class="w-full h-full object-contain p-0.5" />
                    </div>
                </div>

                {{-- Product Info --}}
                <div class="flex-1 flex flex-col justify-center gap-0.5">
                    <h2 class="text-[8px] font-black text-gray-950 uppercase leading-none line-clamp-1 w-full">{{ $product->name }}</h2>
                    <p class="mono text-[6px] text-slate-500 font-bold tracking-tight">{{ $product->sku }}</p>
                    
                    <div class="flex flex-col items-center">
                        <div class="barcode-lines" id="barcode5x5" style="height:10px; gap:1px; justify-content:center;"></div>
                        <p class="mono text-[6px] font-bold text-slate-800 tracking-wider">{{ $product->barcode }}</p>
                    </div>

                    @if($product->uom || $product->location)
                    <p class="text-[5px] text-slate-500 font-medium">
                        {{ $product->location }} @if($product->location && $product->uom)·@endif {{ $product->uom }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 3×10.5 cm layout: industrial --}}
        <div id="label-3x10" class="print-label label-3x10 bg-white shadow-2xl border-2 border-slate-900 hidden overflow-hidden">
            <div class="flex h-full w-full">
                <!-- Left: QR & Photo -->
                <div class="w-[3cm] h-[3cm] border-r-2 border-slate-900 flex items-center justify-center p-1.5 gap-1.5 bg-white">
                    @if($product->image_path)
                        <div class="w-[1.25cm] h-[1.25cm] border border-slate-300 rounded-[4px] p-0.5 bg-slate-50 flex items-center justify-center">
                            <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover rounded-sm" />
                        </div>
                    @endif
                    <div class="border border-slate-300 rounded-[4px] p-0.5 bg-slate-50 flex items-center justify-center {{ $product->image_path ? 'w-[1.25cm] h-[1.25cm]' : 'w-9/12 h-9/12' }}">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0" 
                             class="w-full h-full object-contain mix-blend-multiply" />
                    </div>
                </div>
                <!-- Right: Info -->
                <div class="flex-1 flex flex-col">
                    <!-- Product Name -->
                    <div class="border-b-2 border-slate-900 p-1.5 px-3 h-[1.35cm] flex flex-col justify-center">
                        <p class="text-[8px] font-bold text-slate-500 uppercase leading-none mb-1">NAMA BARANG:</p>
                        <h2 class="text-[12px] font-black text-slate-900 uppercase leading-tight line-clamp-2">{{ $product->name }}</h2>
                    </div>
                    <!-- Barcode & Location -->
                    <div class="flex flex-1">
                        <div class="flex-1 border-r-2 border-slate-900 p-1.5 px-3 flex flex-col justify-center">
                            <p class="text-[8px] font-bold text-slate-500 uppercase leading-none mb-1">KODE BARANG:</p>
                            <p class="mono text-[10px] font-bold text-slate-900 tracking-wider">{{ $product->barcode }}</p>
                        </div>
                        <div class="w-[4cm] p-1.5 px-3 flex flex-col justify-center bg-slate-50">
                            <p class="text-[8px] font-bold text-slate-500 uppercase leading-none mb-1">KODE RAK:</p>
                            <p class="mono text-[11px] font-black text-slate-900 truncate">{{ $product->location ?? '---' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    // Active size button styling
    const currentSize = localStorage.getItem('qr_label_size') || '3x10';
    const allLabels = { 'a4': 'a4', '10x7': '10x7', '3x10': '3x10', '5x5': '5x5' };

    function setSize(size) {
        localStorage.setItem('qr_label_size', size);
        // Hide all labels
        Object.keys(allLabels).forEach(k => {
            const el = document.getElementById('label-' + k);
            if(el) {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }
        });
        // Show selected
        const target = document.getElementById('label-' + size);
        if(target) {
            target.classList.remove('hidden');
            target.classList.add('flex');
        }

        // Update button styles
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('border-blue-600', 'bg-blue-600', 'text-white', 'active');
            btn.classList.add('border-slate-300', 'text-slate-700', 'bg-white');
        });
        const active = document.querySelector(`[data-size="${size}"]`);
        if (active) {
            active.classList.remove('border-slate-300', 'text-slate-700', 'bg-white');
            active.classList.add('border-blue-600', 'bg-blue-600', 'text-white', 'active');
        }

        // Apply scale on screen
        applyScale();
    }

    function applyScale() {
        const wrapper = document.getElementById('printWrapper');
        const activeLabel = document.querySelector('.print-label:not(.hidden)');
        if (!wrapper || !activeLabel) return;
        
        // Reset scale first
        wrapper.style.transform = 'none';
        wrapper.style.marginBottom = '0';
        
        const winHeight = window.innerHeight;
        // Available height is window height minus top controls (~200px)
        const availableHeight = winHeight - 200;
        const rect = activeLabel.getBoundingClientRect();
        
        if (rect.height > availableHeight) {
            const scale = availableHeight / rect.height;
            wrapper.style.transform = `scale(${scale})`;
            // Fix margin bottom so the document doesn't scroll unnecessarily
            wrapper.style.marginBottom = `${-(rect.height * (1 - scale))}px`;
        }
    }
    
    window.addEventListener('resize', applyScale);

    // Generate decorative barcode bars (CSS only, not real barcode)
    function makeBarcode(containerId, count, maxH) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const widths = [2, 1, 3, 1, 2, 1, 1, 2, 3, 1, 2, 1, 2, 1, 3, 2, 1, 2, 1, 1, 2, 3, 1, 2, 1];
        for (let i = 0; i < count; i++) {
            const bar = document.createElement('span');
            bar.style.width = (widths[i % widths.length]) + 'px';
            bar.style.height = (maxH * (0.5 + 0.5 * (i % 3 === 0 ? 1 : 0.7))) + 'px';
            container.appendChild(bar);
        }
    }
    makeBarcode('barcodeA4', 40, 36);
    makeBarcode('barcode10x7', 28, 22);
    makeBarcode('barcode5x5', 22, 14);

    // Init size
    setSize(currentSize);

    // @media print: set correct @page size and handle DOM cloning for Ctrl+P
    window.addEventListener('beforeprint', function() {
        const size = localStorage.getItem('qr_label_size') || 'a4';
        const copies = parseInt(document.getElementById('copyCount').value) || 1;
        const sizeMap = { 'a4': 'A4', '10x7': '100mm 70mm', '3x10': '105mm 30mm', '5x5': '50mm 50mm' };
        
        if (copies > 1) {
            document.body.classList.add('print-multiple');
            
            // Generate multiple labels for Ctrl+P if not already generated by triggerPrint
            const activeLabel = document.querySelector('.print-label:not(.hidden)');
            if (activeLabel && !document.getElementById('tempPrintArea')) {
                const printArea = document.createElement('div');
                printArea.className = 'bulk-container';
                printArea.id = 'tempPrintArea';
                
                for (let i = 0; i < copies; i++) {
                    const clone = activeLabel.cloneNode(true);
                    clone.id = ''; 
                    clone.style.transform = 'none'; 
                    clone.style.margin = '0';
                    
                    const wrapper = document.createElement('div');
                    wrapper.className = 'print-item';
                    wrapper.setAttribute('data-size', size === '3x10' ? '3x10.5' : size);
                    wrapper.appendChild(clone);
                    
                    printArea.appendChild(wrapper);
                }
                document.getElementById('printWrapper').style.display = 'none';
                document.body.appendChild(printArea);
            }
        } else {
            document.body.classList.remove('print-multiple');
        }

        const style = document.createElement('style');
        style.id = 'print-page-size';
        // If printing multiple copies, force A4 page size to fill the sheet
        style.textContent = `@page { size: ${copies > 1 ? 'A4' : (sizeMap[size] || 'A4')}; margin: ${copies > 1 ? '1cm' : '0'}; }`;
        document.head.appendChild(style);
    });
    window.addEventListener('afterprint', function() {
        const s = document.getElementById('print-page-size');
        if (s) s.remove();
        
        const tempArea = document.getElementById('tempPrintArea');
        if (tempArea) {
            tempArea.remove();
            document.getElementById('printWrapper').style.display = '';
        }
    });

    function triggerPrint() {
        // window.print() will automatically trigger the beforeprint event which now handles
        // everything including DOM generation and CSS injection, ensuring consistency.
        window.print();
    }
</script>
</body>
</html>
