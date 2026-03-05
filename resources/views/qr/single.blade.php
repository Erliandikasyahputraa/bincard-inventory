<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak QR - {{ $product->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: portrait;
                margin: 0; 
            }
            body { 
                margin: 0; 
                -webkit-print-color-adjust: exact; 
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen items-center justify-center p-0 print:bg-white print:p-0 transition-colors duration-300 ease-in-out">
    <!-- Controls -->
    <div class="no-print fixed top-4 right-4 flex gap-2 z-50">
        <button onclick="window.print()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Sekarang
        </button>
        <button onclick="window.close()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-[#0A1931] dark:text-white font-bold rounded-lg shadow-lg transition-colors">
            Tutup
        </button>
    </div>

    <!-- Printable Area (Scale to fit standard HVS/A4) -->
    <div class="bg-white w-full max-w-[21cm] h-[29.7cm] flex flex-col items-center justify-center p-12 text-center shadow-2xl print:shadow-none print:w-full print:h-screen print:max-w-none transition-colors duration-300 ease-in-out">
        
        <div class="flex flex-col items-center gap-8 lg:gap-12 mt-[-5rem]">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=600x600&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0" alt="QR Code" class="w-[300px] h-[300px] sm:w-[500px] sm:h-[500px] object-contain" />
            
            <div class="space-y-4">
                <h1 class="text-4xl sm:text-6xl font-bold text-gray-900 leading-tight uppercase tracking-tight transition-colors duration-300 ease-in-out">{{ $product->name }}</h1>
                <p class="text-3xl sm:text-4xl text-gray-700 font-mono tracking-widest font-semibold border-b-4 border-gray-900 inline-block pb-2 transition-colors duration-300 ease-in-out">{{ $product->sku }}</p>
            </div>
        </div>

    </div>
</body>
</html>
