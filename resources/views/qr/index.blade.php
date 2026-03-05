@extends('layouts.app')
@section('title', 'Cetak QR Code - Bincard Pro')
@section('content')

<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Cetak QR Code</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Pilih dan cetak label QR untuk produk inventory Anda.</p>
    </div>
    
    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-[#1F6FEB] rounded-xl hover:bg-[#388BFD] transition-colors shadow-lg shadow-[#1F6FEB]/20">
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

<div class="bg-[#161B22] border border-[#30363D] rounded-2xl p-6">
    <div id="printable-area" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @forelse($products as $product)
            <div class="print-break bg-[#0D1117] border border-[#30363D] print:border-gray-300 print:bg-white rounded-xl p-4 flex flex-col items-center text-center">
                <div class="w-full flex justify-center items-start mb-2">
                    <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400 print:text-gray-500">{{ $product->sku }}</span>
                </div>
                
                <!-- QR Code via external API for instant rendering -->
                <div class="bg-white p-2 rounded-lg mb-3 shadow-sm">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0" alt="QR Code {{ $product->sku }}" class="w-24 h-24 object-contain" />
                </div>
                
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 print:text-black line-clamp-2 leading-tight mb-3">{{ $product->name }}</h3>

                <a href="{{ route('qr.print.single', $product->id) }}" target="_blank" class="print:hidden w-full py-2 px-3 bg-[#1F6FEB]/10 hover:bg-[#1F6FEB] text-[#58A6FF] hover:text-slate-900 dark:text-white rounded-lg transition-colors text-xs font-bold flex items-center justify-center gap-1 mt-auto">
                    <i data-lucide="maximize-2" class="w-3 h-3"></i> Cetak 1 Lembar
                </a>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <i data-lucide="package-search" class="w-12 h-12 text-slate-600 mx-auto mb-3"></i>
                <p class="text-slate-500 dark:text-slate-400">Belum ada data produk untuk dicetak.</p>
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
