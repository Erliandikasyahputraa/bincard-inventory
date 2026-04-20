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
        .label-a4      { width: 21cm;   height: 29.7cm; }
        .label-10x7    { width: 10cm;   height: 7cm; }
        .label-5x5     { width: 5cm;    height: 5cm; }

        @media print {
            @page { margin: 0; }
            body  { margin: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; background: transparent !important; }
            body > *:not(#printWrapper) { display: none !important; }
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

        {{-- 10×7 cm layout: foto kiri | QR + info kanan --}}
        <div id="label-10x7" class="print-label label-10x7 bg-white shadow-2xl rounded-lg hidden overflow-hidden" style="display:none;">
            <div class="flex h-full">
                {{-- Kiri: Foto produk --}}
                <div class="shrink-0 flex items-center justify-center border-r border-slate-200" style="width:3.3cm;">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="Foto"
                             style="width:3.3cm;height:7cm;object-fit:cover;" />
                    @else
                        <div style="width:3.3cm;height:7cm;background:#f8fafc;display:flex;align-items:center;justify-content:center;">
                            <svg style="width:36px;height:36px;color:#cbd5e1" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 7.703 4.734a2 2 0 0 0 1.994 0L20.7 7"/><path d="m7.5 4.27 9 5.15"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Kanan: QR besar + info teks --}}
                <div class="flex flex-col items-center justify-between flex-1 p-2">
                    {{-- QR besar di atas --}}
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=260x260&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                         alt="QR" style="width:4.9cm;height:4.9cm;object-fit:contain;" />

                    {{-- Info di bawah QR --}}
                    <div style="width:100%;text-align:center;overflow:hidden;">
                        <p style="font-family:'JetBrains Mono',monospace;font-size:6px;font-weight:700;color:#94a3b8;letter-spacing:2px;text-transform:uppercase;">{{ strtoupper($systemName) }}</p>
                        <p style="font-size:8px;font-weight:900;color:#111;text-transform:uppercase;line-height:1.2;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin:1px 0;">{{ $product->name }}</p>
                        <div class="barcode-lines" id="barcode10x7" style="height:12px;gap:1px;justify-content:center;"></div>
                        <p style="font-family:'JetBrains Mono',monospace;font-size:6px;font-weight:700;color:#334155;letter-spacing:1px;">{{ $product->barcode }}</p>
                        @if($product->location || $product->uom)
                        <p style="font-size:6px;color:#94a3b8;margin-top:1px;">
                            @if($product->location)<strong style="color:#475569;">{{ $product->location }}</strong>@endif
                            @if($product->location && $product->uom) &middot; @endif
                            @if($product->uom)<strong style="color:#475569;">{{ $product->uom }}</strong>@endif
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 5×5 cm layout: QR besar dominan + info kompak --}}
        <div id="label-5x5" class="print-label label-5x5 bg-white shadow-2xl rounded-lg hidden overflow-hidden" style="display:none;">
            <div style="width:5cm;height:5cm;display:flex;flex-direction:column;align-items:center;padding:4px;box-sizing:border-box;overflow:hidden;">
                {{-- Brand --}}
                <p style="font-size:5px;font-weight:700;letter-spacing:2px;color:#94a3b8;text-transform:uppercase;font-family:'JetBrains Mono',monospace;">{{ strtoupper($systemName) }}</p>

                {{-- QR besar + foto mini berdampingan --}}
                <div style="display:flex;align-items:center;justify-content:center;gap:4px;margin-top:2px;">
                    {{-- Foto mini --}}
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="Foto"
                             style="width:1.3cm;height:1.3cm;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0;flex-shrink:0;" />
                    @else
                        <div style="width:1.3cm;height:1.3cm;border-radius:4px;border:1px solid #e2e8f0;background:#f8fafc;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:14px;height:14px;color:#cbd5e1" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 7.703 4.734a2 2 0 0 0 1.994 0L20.7 7"/><path d="m7.5 4.27 9 5.15"/></svg>
                        </div>
                    @endif
                    {{-- QR dominan --}}
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                         alt="QR" style="width:2.8cm;height:2.8cm;object-fit:contain;border:1.5px solid #111;border-radius:4px;flex-shrink:0;" />
                </div>

                {{-- Nama produk --}}
                <p style="font-size:6.5px;font-weight:900;color:#111;text-transform:uppercase;line-height:1.2;text-align:center;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;width:100%;margin-top:3px;">{{ $product->name }}</p>

                {{-- Barcode visual --}}
                <div class="barcode-lines" id="barcode5x5" style="height:10px;gap:1px;justify-content:center;width:100%;"></div>

                {{-- Barcode text + info --}}
                <p style="font-family:'JetBrains Mono',monospace;font-size:5px;font-weight:700;letter-spacing:1px;color:#334155;text-align:center;">{{ $product->barcode }}</p>
                @if($product->location || $product->uom)
                <p style="font-size:5px;color:#94a3b8;text-align:center;overflow:hidden;white-space:nowrap;max-width:100%;">
                    @if($product->location)<strong style="color:#475569;">{{ $product->location }}</strong>@endif
                    @if($product->location && $product->uom) &middot; @endif
                    @if($product->uom)<strong style="color:#475569;">{{ $product->uom }}</strong>@endif
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
