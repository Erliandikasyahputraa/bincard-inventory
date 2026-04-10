<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak QR — {{ $product->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .mono { font-family: 'JetBrains Mono', 'Courier New', monospace; }

        /* ── Label Size Targets ── */
        .label-a4      { width: 21cm;   min-height: 29.7cm; }
        .label-10x7    { width: 10cm;   min-height: 7cm; }
        .label-5x5     { width: 5cm;    min-height: 5cm; }

        @media print {
            @page { margin: 0; }
            body  { margin: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .print-label { box-shadow: none !important; border-radius: 0 !important; transform: none !important; margin: 0 !important; }
            .print-wrapper { transform: none !important; height: auto !important; margin: 0 !important; padding: 0 !important; }
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
                    class="size-btn active px-4 py-2.5 rounded-xl text-sm font-semibold border-2 transition-colors">
                    📄 A4 Penuh
                </button>
                <button onclick="setSize('10x7')" data-size="10x7"
                    class="size-btn px-4 py-2.5 rounded-xl text-sm font-semibold border-2 transition-colors">
                    🏷️ Label 10×7 cm
                </button>
                <button onclick="setSize('5x5')" data-size="5x5"
                    class="size-btn px-4 py-2.5 rounded-xl text-sm font-semibold border-2 transition-colors">
                    🔖 Label 5×5 cm
                </button>
            </div>
        </div>

        <div class="flex gap-3">
            <button onclick="window.print()"
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
            $company = \App\Models\CompanySetting::first();
            $companyName = $company?->nama_perusahaan ?? config('app.name', 'BINGO');
        @endphp

        {{-- A4 layout --}}
        <div id="label-a4" class="print-label label-a4 bg-white shadow-2xl rounded-xl flex flex-col items-center justify-center p-12 text-center">
            {{-- Company name --}}
            <p class="mono text-xs font-bold uppercase tracking-[4px] text-slate-400 mb-6">{{ strtoupper($companyName) }}</p>

            {{-- QR Code --}}
            <div class="bg-white p-3 border-4 border-slate-900 rounded-xl mb-6 shadow-sm">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                     alt="QR Code" class="w-[320px] h-[320px] object-contain" />
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

        {{-- 10×7 cm layout --}}
        <div id="label-10x7" class="print-label label-10x7 bg-white shadow-2xl rounded-lg hidden flex-row items-center gap-3 p-3 overflow-hidden" style="min-height:7cm;">
            <div class="shrink-0">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                     alt="QR" class="w-[5.5cm] h-[5.5cm] object-contain" />
            </div>
            <div class="flex flex-col justify-center overflow-hidden flex-1">
                <p class="mono text-[7px] font-bold uppercase tracking-[3px] text-slate-400 mb-1">{{ strtoupper($companyName) }}</p>
                <h1 class="text-[11px] font-black uppercase text-gray-900 leading-snug mb-1 break-words">{{ $product->name }}</h1>
                <p class="mono text-[9px] font-bold text-slate-500 tracking-widest mb-2">{{ $product->sku }}</p>
                <div class="barcode-lines scale-75 origin-left" id="barcode10x7"></div>
                <p class="mono text-[8px] font-bold tracking-[3px] text-slate-800 mt-1">{{ $product->barcode }}</p>
                @if($product->location || $product->uom)
                <p class="text-[8px] text-slate-400 mt-1">
                    @if($product->location) Rak: <strong class="text-slate-700">{{ $product->location }}</strong> @endif
                    @if($product->location && $product->uom) | @endif
                    @if($product->uom) Satuan: <strong class="text-slate-700">{{ $product->uom }}</strong> @endif
                </p>
                @endif
            </div>
        </div>

        {{-- 5×5 cm layout --}}
        <div id="label-5x5" class="print-label label-5x5 bg-white shadow-2xl rounded-lg hidden flex-col items-center justify-between p-2 text-center overflow-hidden" style="min-height:5cm;">
            <p class="mono text-[6px] font-bold uppercase tracking-[2px] text-slate-400">{{ strtoupper($companyName) }}</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                 alt="QR" class="w-[3cm] h-[3cm] object-contain" />
            <div>
                <p class="text-[7px] font-black uppercase text-gray-900 leading-snug break-words">{{ $product->name }}</p>
                <div class="barcode-lines justify-center mt-1" style="height:18px;gap:1px;" id="barcode5x5"></div>
                <p class="mono text-[6px] font-bold tracking-[2px] text-slate-800 mt-0.5">{{ $product->barcode }}</p>
                @if($product->location || $product->uom)
                <p class="text-[6px] text-slate-400 mt-0.5">
                    @if($product->location) Rak: <strong class="text-slate-700">{{ $product->location }}</strong> @endif
                    @if($product->location && $product->uom) • @endif
                    @if($product->uom) <strong class="text-slate-700">{{ $product->uom }}</strong> @endif
                </p>
                @endif
            </div>
        </div>
    </div>

<script>
    // Active size button styling
    const currentSize = localStorage.getItem('qr_label_size') || 'a4';
    const allLabels = { 'a4': 'a4', '10x7': '10x7', '5x5': '5x5' };

    function setSize(size) {
        localStorage.setItem('qr_label_size', size);
        // Hide all labels
        Object.keys(allLabels).forEach(k => {
            const el = document.getElementById('label-' + k);
            el.classList.add('hidden');
            el.classList.remove('flex');
        });
        // Show selected
        const target = document.getElementById('label-' + size);
        target.classList.remove('hidden');
        target.classList.add('flex');

        // Update button styles
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700', 'active');
            btn.classList.add('border-slate-200', 'text-slate-600');
        });
        const active = document.querySelector(`[data-size="${size}"]`);
        if (active) {
            active.classList.remove('border-slate-200', 'text-slate-600');
            active.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700', 'active');
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

    // @media print: set correct @page size
    window.addEventListener('beforeprint', function() {
        const size = localStorage.getItem('qr_label_size') || 'a4';
        const sizeMap = { 'a4': 'A4', '10x7': '100mm 70mm', '5x5': '50mm 50mm' };
        const style = document.createElement('style');
        style.id = 'print-page-size';
        style.textContent = `@page { size: ${sizeMap[size] || 'A4'}; }`;
        document.head.appendChild(style);
    });
    window.addEventListener('afterprint', function() {
        const s = document.getElementById('print-page-size');
        if (s) s.remove();
    });
</script>
</body>
</html>
