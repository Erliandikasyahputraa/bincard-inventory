<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Label — {{ config('app.name', 'BINGO') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }

        @media print {
            @page { margin: 0; }
            body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; background: white !important; }
            .no-print { display: none !important; }
            .label-page-break { page-break-after: always; }
        }

        /* Container for labels */
        .bulk-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            padding: 20px;
        }

        /* Label Size Targets */
        .label-a4      { width: 21cm;   height: 29.7cm; margin: 0 auto; }
        .label-10x7    { width: 10cm;   height: 7cm; }
        .label-3x10    { width: 10.5cm; height: 3cm; }
        .label-5x5     { width: 5cm;    height: 5cm; }

        .barcode-lines {
            display: flex;
            align-items: flex-end;
            gap: 1px;
        }
        .barcode-lines span {
            display: inline-block;
            background: #111;
        }

        /* Print grid logic for small labels */
        @media print {
            .bulk-container { padding: 0; gap: 0; display: block; }
            .print-item { 
                page-break-inside: avoid; 
                break-inside: avoid;
                margin: 0 !important;
                display: inline-block !important;
                vertical-align: top;
            }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- Toolbar --}}
    <div class="no-print sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('qr.print') }}" class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-sm font-bold text-slate-800">Cetak Massal</h1>
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Ukuran: {{ strtoupper($size) }} · Total: {{ $products->count() }} Produk</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Sekarang
            </button>
        </div>
    </div>

    @php $systemName = config('app.name', 'BINGO'); @endphp

    <div class="bulk-container">
        @foreach($products as $product)
            <div class="print-item mb-4">
                {{-- A4 Case --}}
                @if($size === 'a4')
                    <div class="label-a4 bg-white shadow-xl flex flex-col items-center justify-center p-12 text-center {{ !$loop->last ? 'label-page-break' : '' }}">
                        <p class="mono text-xs font-bold uppercase tracking-[4px] text-slate-400 mb-6">{{ strtoupper($systemName) }}</p>
                        <div class="flex items-center gap-6 mb-6">
                            <div class="bg-white p-2 border-4 border-slate-100 rounded-xl">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" class="w-[320px] h-[320px] object-cover rounded-lg" />
                                @else
                                    <div class="w-[320px] h-[320px] bg-slate-50 flex items-center justify-center text-slate-200"><svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg></div>
                                @endif
                            </div>
                            <div class="bg-white p-3 border-4 border-slate-900 rounded-xl">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=600x600&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0" class="w-[320px] h-[320px] object-contain" />
                            </div>
                        </div>
                        <h1 class="text-5xl font-black text-gray-900 uppercase mb-2">{{ $product->name }}</h1>
                        <p class="mono text-2xl text-slate-600 font-semibold mb-4">{{ $product->sku }}</p>
                        <div class="flex flex-col items-center">
                            <div class="barcode-lines" style="height:36px; gap:2px;">
                                @for($i=0; $i<40; $i++) <span style="width:{{ [2,1,3,1,2][($i+rand(0,4))%5] }}px; height:{{ 25 + rand(0,11) }}px"></span> @endfor
                            </div>
                            <p class="mono text-lg font-bold tracking-[6px] text-slate-800 mt-2">{{ $product->barcode }}</p>
                        </div>
                    </div>

                {{-- 10x7 Case --}}
                @elseif($size === '10x7')
                    <div class="label-10x7 bg-white shadow-md border border-slate-100 rounded-lg overflow-hidden flex flex-col p-2">
                        <p class="mono text-[8px] font-bold uppercase tracking-[3px] text-slate-400 mb-1 text-center">{{ strtoupper($systemName) }}</p>
                        <div class="flex items-center justify-center gap-4 mb-2">
                            <div class="border border-slate-100 rounded overflow-hidden" style="width:3.2cm; height:3.2cm;">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full bg-slate-50 flex items-center justify-center"><svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg></div>
                                @endif
                            </div>
                            <div class="border-2 border-slate-900 rounded overflow-hidden" style="width:3.2cm; height:3.2cm;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0" class="w-full h-full object-contain p-1" />
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center text-center">
                            <h2 class="text-[11px] font-black text-gray-900 uppercase leading-tight line-clamp-2">{{ $product->name }}</h2>
                            <p class="mono text-[8px] font-bold text-slate-500 mb-1">{{ $product->sku }}</p>
                            <div class="barcode-lines" style="height:14px; gap:1px;">
                                @for($i=0; $i<28; $i++) <span style="width:{{ [2,1,1,2][($i+rand(0,3))%4] }}px; height:{{ 10 + rand(0,4) }}px"></span> @endfor
                            </div>
                            <p class="mono text-[8px] font-bold tracking-[2px] text-slate-800">{{ $product->barcode }}</p>
                        </div>
                    </div>

                {{-- 5x5 Case --}}
                @elseif($size === '5x5')
                    <div class="label-5x5 bg-white shadow-sm border border-slate-100 rounded-lg overflow-hidden flex flex-col p-1.5 text-center">
                        <p class="mono text-[6px] font-bold uppercase tracking-[2px] text-slate-400 mb-1">{{ strtoupper($systemName) }}</p>
                        <div class="flex items-center justify-center gap-1.5 mb-1.5">
                            <div class="border border-slate-50 rounded" style="width:2cm; height:2cm;">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center"><svg class="w-6 h-6 text-slate-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg></div>
                                @endif
                            </div>
                            <div class="border-2 border-slate-950 rounded" style="width:2cm; height:2cm;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0" class="w-full h-full object-contain p-0.5" />
                            </div>
                        </div>
                        <h2 class="text-[8px] font-black text-gray-950 uppercase leading-none line-clamp-1 mb-0.5">{{ $product->name }}</h2>
                        <p class="mono text-[6px] text-slate-500 font-bold tracking-tight mb-0.5">{{ $product->sku }}</p>
                        <div class="flex flex-col items-center">
                            <div class="barcode-lines" style="height:10px; gap:1px;">
                                @for($i=0; $i<22; $i++) <span style="width:{{ [1,2,1][($i+rand(0,2))%3] }}px; height:{{ 7 + rand(0,3) }}px"></span> @endfor
                            </div>
                            <p class="mono text-[6px] font-bold text-slate-800 tracking-wider">{{ $product->barcode }}</p>
                        </div>
                    </div>

                {{-- 3x10.5 Case (Industrial Horizontal) --}}
                @elseif($size === '3x10.5')
                    <div class="label-3x10 bg-white shadow-md border-2 border-slate-900 flex overflow-hidden mb-[0.2cm]">
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
                @endif
            </div>
        @endforeach
    </div>

    {{-- Pre-trigger print if requested? Actually better let user click it --}}
    <script>
        // No heavy JS needed here. Pure, fast rendering.
    </script>
</body>
</html>
