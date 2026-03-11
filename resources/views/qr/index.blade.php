@extends('layouts.app')
@section('title', 'Cetak QR Code - BINGO')
@section('content')

<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Cetak QR Code</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 transition-colors duration-300 ease-in-out">Pilih dan cetak label QR untuk produk inventory Anda.</p>
    </div>
    
    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-blue-600 dark:bg-blue-500 rounded-xl hover:bg-[#388BFD] transition-colors shadow-lg shadow-[#1F6FEB]/20">
        <i data-lucide="printer" class="w-4 h-4"></i>
        Cetak Label
    </button>
</div>

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-area, #printable-area * {
            visibility: visible;
        }
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white !important;
        }
        .print-break {
            page-break-inside: avoid;
        }
    }
</style>

<div x-data="{ search: '' }" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 transition-colors duration-300 ease-in-out">
    
    <div class="mb-6 relative max-w-md print:hidden">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
        <input type="text" x-model="search" placeholder="Cari nama produk atau SKU secara instan..." 
            class="pl-9 pr-4 py-2.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all outline-none text-slate-900 dark:text-white dark:placeholder-slate-500">
    </div>

    <div id="printable-area" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @forelse($products as $product)
            <div class="print-break bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 print:border-gray-300 print:bg-white rounded-xl p-4 flex flex-col items-center text-center transition-colors duration-300 ease-in-out"
                x-show="search === '' || '{{ strtolower(addslashes($product->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($product->sku)) }}'.includes(search.toLowerCase())">
                <div class="w-full flex justify-center items-start mb-2">
                    <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400 print:text-gray-500 transition-colors duration-300 ease-in-out">{{ $product->sku }}</span>
                </div>
                
                <!-- QR Code via external API for instant rendering -->
                <div class="bg-white p-2 rounded-lg mb-3 shadow-sm transition-colors duration-300 ease-in-out">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0" alt="QR Code {{ $product->sku }}" class="w-24 h-24 object-contain" />
                </div>
                
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 print:text-black line-clamp-2 leading-tight mb-3 transition-colors duration-300 ease-in-out">{{ $product->name }}</h3>

                <a href="{{ route('qr.print.single', $product->id) }}" target="_blank" class="print:hidden w-full py-2 px-3 bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 dark:hover:text-white rounded-lg transition-colors text-xs font-bold flex items-center justify-center gap-1 mt-auto">
                    <i data-lucide="maximize-2" class="w-3 h-3"></i> Cetak 1 Lembar
                </a>
            </div>
        @empty
            <div class="col-span-full py-12 text-center transition-colors duration-300 ease-in-out">
                <i data-lucide="package-search" class="w-12 h-12 text-slate-600 mx-auto mb-3 transition-colors duration-300 ease-in-out"></i>
                <p class="text-slate-500 dark:text-slate-400 transition-colors duration-300 ease-in-out">Belum ada data produk untuk dicetak.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    // Ensure Lucide icons also render properly before print
    window.addEventListener('beforeprint', () => {
        // Any print prep needed
    });
</script>
@endpush
@endsection
