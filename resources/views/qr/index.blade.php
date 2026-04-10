@extends('layouts.app')
@section('title', 'Cetak QR Code - BINGO')
@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Cetak QR Code</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Pilih produk yang ingin dicetak labelnya.</p>
    </div>
    <div class="flex items-center gap-2 print:hidden" id="floatBar">
        <span class="text-xs text-slate-500 dark:text-slate-400 hidden" id="selectedCount"></span>
        <button onclick="printSelected()" id="btnPrintSelected"
            class="hidden items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Terpilih
        </button>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Halaman Ini
        </button>
    </div>
</div>

{{-- Print Styles --}}
<style>
    @media print {
        body * { visibility: hidden; }
        #printable-area, #printable-area * { visibility: visible; }
        #printable-area {
            position: absolute; left: 0; top: 0; width: 100%;
            display: grid !important;
            grid-template-columns: repeat(4, 1fr);
            gap: 0 !important;
            background: white !important;
        }
        .qr-card { page-break-inside: avoid; break-inside: avoid; border: 1px solid #e5e7eb !important; }
        .qr-card.not-selected { display: none !important; }
    }
</style>

{{-- Search & filter bar --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 mb-4 print:hidden shadow-sm">
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <form method="GET" action="{{ route('qr.print') }}" class="relative flex-1 w-full max-w-md">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk, SKU, atau barcode..."
                class="pl-9 pr-4 py-2.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all outline-none text-slate-900 dark:text-white dark:placeholder-slate-500">
            @if(request('search'))
                <a href="{{ route('qr.print') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 shrink-0">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" id="selectAllCheck" onchange="toggleSelectAll()" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span class="text-xs font-medium">Pilih semua</span>
            </label>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <div class="flex flex-col">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $products->total() }} produk</span>
            </div>
        </div>
    </div>
</div>

{{-- QR Grid --}}
<div id="printable-area" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
    @forelse($products as $product)
        <div class="qr-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 flex flex-col items-center text-center cursor-pointer transition-all duration-150 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700 relative group"
             data-name="{{ strtolower($product->name) }}"
             data-sku="{{ strtolower($product->sku) }}"
             data-barcode="{{ strtolower($product->barcode) }}"
             data-id="{{ $product->id }}"
             onclick="toggleCard(this)">

            {{-- Selected indicator --}}
            <div class="card-check hidden absolute top-2 right-2 w-5 h-5 rounded-full bg-emerald-500 text-white items-center justify-center shadow-sm">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>

            {{-- SKU badge --}}
            <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500 mb-2 truncate w-full">{{ $product->sku }}</span>

            {{-- QR — lazy loaded via IntersectionObserver --}}
            <div class="bg-white p-1.5 border border-slate-100 rounded-lg mb-2.5 shadow-sm w-full flex items-center justify-center">
                <img data-src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Crect width='80' height='80' fill='%23f1f5f9'/%3E%3Crect x='20' y='20' width='10' height='10' fill='%23cbd5e1'/%3E%3Crect x='50' y='20' width='10' height='10' fill='%23cbd5e1'/%3E%3Crect x='20' y='50' width='10' height='10' fill='%23cbd5e1'/%3E%3C/svg%3E"
                     alt="QR {{ $product->sku }}"
                     class="w-20 h-20 object-contain qr-img" />
            </div>

            {{-- Product name --}}
            <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-2 leading-snug mb-1.5 w-full">{{ $product->name }}</h3>

            {{-- Barcode text --}}
            <p class="font-mono text-[9px] font-semibold text-slate-500 dark:text-slate-400 tracking-wider truncate w-full mb-2">{{ $product->barcode }}</p>

            {{-- Location --}}
            @if($product->location)
            <span class="text-[9px] text-slate-400 dark:text-slate-500 mb-2">📍 {{ $product->location }}</span>
            @endif

            {{-- Action buttons --}}
            <div class="flex gap-1.5 w-full mt-auto print:hidden">
                <a href="{{ route('qr.print.single', $product->id) }}" target="_blank"
                   onclick="event.stopPropagation()"
                   class="flex-1 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 dark:hover:text-white rounded-lg transition-colors text-[10px] font-bold flex items-center justify-center gap-1">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Full Label
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-full py-16 flex flex-col items-center gap-3 text-center print:hidden">
            <i data-lucide="package-search" class="w-12 h-12 text-slate-300"></i>
            <p class="text-slate-400">Belum ada data produk untuk dicetak.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mt-6 print:hidden">
    {{ $products->links() }}
</div>

@push('scripts')
<script>
    // ── Lazy-load QR images via IntersectionObserver ──
    const qrObserver = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const img = entry.target;
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                obs.unobserve(img);
            }
        });
    }, { rootMargin: '200px' });

    document.querySelectorAll('.qr-img[data-src]').forEach(img => qrObserver.observe(img));

    // Ensure all QR loaded before print
    window.addEventListener('beforeprint', () => {
        document.querySelectorAll('.qr-img[data-src]').forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    });

    // ── Card selection ──
    const selectedIds = new Set();

    function toggleCard(card) {
        const id = card.dataset.id;
        const check = card.querySelector('.card-check');
        if (selectedIds.has(id)) {
            selectedIds.delete(id);
            card.classList.remove('ring-2', 'ring-emerald-400', 'border-emerald-400');
            check.classList.remove('flex');
            check.classList.add('hidden');
        } else {
            selectedIds.add(id);
            card.classList.add('ring-2', 'ring-emerald-400', 'border-emerald-400');
            check.classList.remove('hidden');
            check.classList.add('flex');
        }
        updateSelectionUI();
    }

    function toggleSelectAll() {
        const allVisible = document.querySelectorAll('.qr-card:not([style*="display: none"])');
        const shouldSelect = document.getElementById('selectAllCheck').checked;
        allVisible.forEach(card => {
            const id = card.dataset.id;
            const check = card.querySelector('.card-check');
            if (shouldSelect) {
                selectedIds.add(id);
                card.classList.add('ring-2', 'ring-emerald-400', 'border-emerald-400');
                check.classList.remove('hidden');
                check.classList.add('flex');
            } else {
                selectedIds.delete(id);
                card.classList.remove('ring-2', 'ring-emerald-400', 'border-emerald-400');
                check.classList.remove('flex');
                check.classList.add('hidden');
            }
        });
        updateSelectionUI();
    }

    function updateSelectionUI() {
        const count = selectedIds.size;
        const btn = document.getElementById('btnPrintSelected');
        const countEl = document.getElementById('selectedCount');
        if (count > 0) {
            btn.classList.remove('hidden');
            btn.classList.add('inline-flex');
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Cetak ${count} Terpilih`;
            countEl.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
            btn.classList.remove('inline-flex');
            countEl.classList.add('hidden');
        }
    }

    function printSelected() {
        // Mark non-selected as not-selected for print CSS
        document.querySelectorAll('.qr-card').forEach(card => {
            if (!selectedIds.has(card.dataset.id)) {
                card.classList.add('not-selected');
            } else {
                card.classList.remove('not-selected');
            }
        });
        window.print();
        // Restore after print
        setTimeout(() => {
            document.querySelectorAll('.qr-card.not-selected').forEach(c => c.classList.remove('not-selected'));
        }, 1000);
    }
</script>
@endpush
@endsection
