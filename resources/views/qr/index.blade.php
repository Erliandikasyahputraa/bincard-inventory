@extends('layouts.app')
@section('title', 'Cetak QR Code - BINGO')
@section('content')

{{-- ═══ Size Picker Modal (pure JS - works everywhere, no Alpine issues) ═══ --}}
{{-- ═══ Size Picker Modal (pure JS - works everywhere, no Alpine issues) ═══ --}}
<div id="sizeModal"
     style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#fff; border-radius:1.25rem; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); padding:1.5rem; width:100%; max-width:380px;">
        <h3 style="font-size:1rem; font-weight:700; color:#1e293b; margin-bottom:0.25rem;">Pilih Ukuran Label</h3>
        <p style="font-size:0.75rem; color:#94a3b8; margin-bottom:1.25rem;">Klik ukuran yang diinginkan, lalu klik Cetak.</p>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0.5rem; margin-bottom:1.25rem;" id="sizeOptions">
            <button onclick="selectModalSize('a4')" id="sz_a4"
                style="border:2px solid #e2e8f0; border-radius:0.75rem; padding:0.875rem 0.25rem; background:#fff; color:#475569; font-weight:600; font-size:0.75rem; cursor:pointer;"
                >📄 A4 Penuh</button>
            <button onclick="selectModalSize('10x7')" id="sz_10x7"
                style="border:2px solid #3b82f6; border-radius:0.75rem; padding:0.875rem 0.25rem; background:#eff6ff; color:#2563eb; font-weight:700; font-size:0.75rem; cursor:pointer;"
                >🏷️ 10×7 cm</button>
            <button onclick="selectModalSize('5x5')" id="sz_5x5"
                style="border:2px solid #e2e8f0; border-radius:0.75rem; padding:0.875rem 0.25rem; background:#fff; color:#475569; font-weight:600; font-size:0.75rem; cursor:pointer;"
                >🔖 5×5 cm</button>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <button onclick="closeModal()" style="flex:1; padding:0.75rem; border-radius:0.75rem; border:1.5px solid #e2e8f0; font-weight:600; font-size:0.875rem; color:#64748b; background:#fff; cursor:pointer;">Batal</button>
            <button onclick="doConfirmPrint()" style="flex:1; padding:0.75rem; border-radius:0.75rem; background:#2563eb; color:#fff; font-weight:700; font-size:0.875rem; border:none; cursor:pointer;">🖨 Cetak</button>
        </div>
    </div>
</div>

{{-- ═══ Loading Overlay for Bulk Print (smooth UX for 5000+ items) ═══ --}}
<div id="printLoading" style="display:none; position:fixed; inset:0; z-index:10000; background:rgba(255,255,255,0.9); backdrop-filter:blur(8px); align-items:center; justify-content:center; flex-direction:column; gap:1.5rem;">
    <div style="width:64px; height:64px; border:6px solid #f3f4f6; border-top:6px solid #10b981; border-radius:50%; animation:spin 1s linear infinite;"></div>
    <div style="text-align:center;">
        <h3 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin-bottom:0.5rem;">Menyiapkan Label...</h3>
        <p style="font-size:0.875rem; color:#64748b; max-width:280px;">Mohon tunggu sebentar, kami sedang memproses data produk dalam jumlah besar agar siap dicetak.</p>
    </div>
    <style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
</div>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Cetak QR Code</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Pilih produk yang ingin dicetak labelnya.</p>
    </div>
    <div class="flex items-center gap-2 print:hidden" id="floatBar">
        <span class="text-xs text-slate-500 dark:text-slate-400 hidden" id="selectedCount"></span>
        <button onclick="openModal('selected')" id="btnPrintSelected"
            class="hidden items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Terpilih
        </button>
        <button onclick="openModal('all')" type="button"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl transition-colors shadow-md border-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Cetak Semua ({{ $totalProdukSistem ?? $products->count() }})
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
            grid-template-columns: repeat(3, 1fr);
            gap: 0 !important;
            background: white !important;
        }
        .qr-card {
            page-break-inside: avoid;
            break-inside: avoid;
            border: 1px solid #d1d5db !important;
            padding: 8px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }
        .qr-card.not-selected { display: none !important; }
        /* Saat print: image-wrap jadi row, foto & QR berdampingan, sama besar */
        .image-wrap {
            position: static !important;
            display: flex !important;
            flex-direction: row !important;
            gap: 6px !important;
            align-items: center !important;
            justify-content: center !important;
            height: auto !important;
            width: auto !important;
        }
        .photo-layer {
            position: static !important;
            opacity: 1 !important;
            width: 4rem !important;
            height: 4rem !important;
            flex-shrink: 0 !important;
        }
        .qr-layer {
            position: static !important;
            opacity: 1 !important;
            width: 4rem !important;
            height: 4rem !important;
            flex-shrink: 0 !important;
        }
        .qr-layer img { width: 4rem !important; height: 4rem !important; }
    }
</style>

{{-- Search & filter bar --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 mb-4 print:hidden shadow-sm">
    {{-- Row 1: Search + Cetak actions --}}
    <form method="GET" action="{{ route('qr.print') }}" class="flex flex-wrap gap-3 items-center">
        {{-- Hidden preserve params --}}
        <input type="hidden" name="sort" value="{{ request('sort', 'name') }}">
        <input type="hidden" name="dir" value="{{ request('dir', 'asc') }}">
        <input type="hidden" name="location" value="{{ request('location') }}">

        {{-- Search --}}
        <div class="relative flex-1 min-w-[180px]">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama produk, SKU, atau barcode..."
                class="pl-9 pr-4 py-2.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none text-slate-900 dark:text-white dark:placeholder-slate-500">
        </div>



        {{-- Filter Lorong --}}
        @if(count($aisles) > 0)
        <div class="relative shrink-0">
            <i data-lucide="split" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
            <select name="aisle" 
                onchange="this.form.submit()"
                class="pl-9 pr-8 py-2.5 bg-white dark:bg-slate-950 border border-emerald-200 dark:border-emerald-900/30 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none appearance-none text-emerald-700 dark:text-emerald-400 font-bold transition-all cursor-pointer shadow-sm">
                <option value="">Semua Lorong</option>
                @foreach($aisles as $a)
                    <option value="{{ $a }}" {{ request('aisle') == $a ? 'selected' : '' }}>Lorong {{ $a }}</option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-400 w-4 h-4 pointer-events-none"></i>
        </div>
        @endif

        <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
            Cari
        </button>
    </form>

    {{-- Row 2: Sort toggle pills + Cetak actions --}}
    <div class="flex flex-wrap items-center gap-2 mt-3">
        {{-- Sort pills: satu tombol per field, klik toggle asc/desc --}}
        @php
            $curSort = request('sort', 'name');
            $curDir  = request('dir', 'asc');
            $sortOptions = [
                'name'    => 'Nama',
                'newest'  => 'Terbaru',
                'location'=> 'Rak',
                'stock'   => 'Stok',
            ];
        @endphp
        <span class="text-xs text-slate-400 font-medium mr-1 shrink-0">Urut:</span>
        @foreach($sortOptions as $key => $label)
            @php
                $isActive = $curSort === $key;
                // Klik field aktif → toggle dir; klik field lain → asc (kecuali newest → desc)
                $nextDir = $isActive
                    ? ($curDir === 'asc' ? 'desc' : 'asc')
                    : ($key === 'newest' ? 'desc' : 'asc');
                $href = route('qr.print', array_merge(request()->except(['sort','dir','page']), ['sort'=>$key,'dir'=>$nextDir]));
            @endphp
            <a href="{{ $href }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all
                   {{ $isActive
                       ? 'bg-blue-600 text-white border-blue-600'
                       : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-blue-400 hover:text-blue-600 shadow-sm' }}">
                {{ $label }}
                @if($isActive)
                    @if($curDir === 'asc')
                        <i data-lucide="arrow-up" class="w-3 h-3"></i>
                    @else
                        <i data-lucide="arrow-down" class="w-3 h-3"></i>
                    @endif
                @else
                    <i data-lucide="arrow-up-down" class="w-2.5 h-2.5 text-slate-300 opacity-50"></i>
                @endif
            </a>
        @endforeach

        {{-- Separator --}}
        <span class="text-slate-200 dark:text-slate-700">|</span>

        {{-- Info count --}}
        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
            {{ is_object($products) && method_exists($products, 'total') ? $products->total() : $products->count() }} produk
        </span>

        {{-- Pilih halaman (checkbox) --}}
        <div class="flex items-center gap-2 ml-auto">
            <label class="flex items-center gap-1.5 cursor-pointer select-none text-xs text-slate-600 dark:text-slate-300">
                <input type="checkbox" id="selectAllCheck" onchange="toggleSelectAll()" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span class="font-medium whitespace-nowrap">Pilih halaman</span>
            </label>
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

            {{-- Image wrapper: foto + QR berdampingan (flex di print) --}}
            <div class="image-wrap relative w-[7rem] h-[7rem] mb-2 shrink-0 mx-auto">
                {{-- Foto produk (tampil di layar, disandingkan saat print) --}}
                <div class="photo-layer absolute inset-0 transition-opacity duration-300 opacity-100 group-hover:opacity-0 flex items-center justify-center bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                    @if($product->image_path)
                        <img data-src="{{ asset('storage/' . $product->image_path) }}" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E" alt="Foto" class="w-full h-full object-cover photo-img">
                    @else
                        <div class="w-full h-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center">
                            <i data-lucide="package" class="w-8 h-8 text-slate-300"></i>
                        </div>
                    @endif
                </div>

                {{-- QR Code: tampil saat hover di layar, DAN saat print (berdampingan dengan foto) --}}
                <div class="qr-layer absolute inset-0 transition-opacity duration-300 opacity-0 group-hover:opacity-100 flex items-center justify-center bg-white rounded-xl">
                    <img data-src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('scan.index', ['barcode' => $product->barcode])) }}&margin=0"
                         src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Crect width='80' height='80' fill='%23f1f5f9'/%3E%3C/svg%3E"
                         alt="QR" class="w-[7rem] h-[7rem] object-contain qr-img" />
                </div>
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
    @if(is_object($products) && method_exists($products, 'links'))
        {{ $products->links() }}
    @endif
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

    document.querySelectorAll('.qr-img[data-src], .photo-img[data-src]').forEach(img => qrObserver.observe(img));

    // Ensure all QR loaded before print
    window.addEventListener('beforeprint', () => {
        document.querySelectorAll('.qr-img[data-src], .photo-img[data-src]').forEach(img => {
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
            if (countEl) {
                countEl.textContent = count + ' dipilih';
                countEl.classList.remove('hidden');
            }
        } else {
            btn.classList.add('hidden');
            btn.classList.remove('inline-flex');
            if (countEl) countEl.classList.add('hidden');
        }
    }

    // ─── Pure-JS modal untuk pilih ukuran sebelum cetak ───────────────────
    let _pendingAction = null;
    let _chosenSize    = '10x7';

    function openModal(action) {
        _pendingAction = action;
        _chosenSize    = '10x7';
        selectModalSize('10x7');                          // reset highlight
        document.getElementById('sizeModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('sizeModal').style.display = 'none';
    }

    function selectModalSize(size) {
        _chosenSize = size;
        ['a4', '10x7', '5x5'].forEach(s => {
            const btn = document.getElementById('sz_' + s);
            if (!btn) return;
            if (s === size) {
                btn.style.border      = '2px solid #3b82f6';
                btn.style.background  = '#eff6ff';
                btn.style.color       = '#2563eb';
                btn.style.fontWeight  = '700';
            } else {
                btn.style.border      = '2px solid #e2e8f0';
                btn.style.background  = '#fff';
                btn.style.color       = '#475569';
                btn.style.fontWeight  = '600';
            }
        });
    }

    function doConfirmPrint() {
        closeModal();
        document.getElementById('printLoading').style.display = 'flex';

        if (_pendingAction === 'all') {
            const base = '{{ route('qr.print.all', request()->except('page')) }}';
            const sep  = base.includes('?') ? '&' : '?';
            window.location.href = base + sep + 'size=' + _chosenSize;
        } else if (_pendingAction === 'selected') {
            const ids  = Array.from(selectedIds).join(',');
            const base = '{{ route('qr.print.all', request()->except('page')) }}';
            const sep  = base.includes('?') ? '&' : '?';
            window.location.href = base + sep + 'size=' + _chosenSize + '&ids=' + ids;
        }
    }

    // Close modal when clicking backdrop
    document.getElementById('sizeModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    function doPrintSelected() {
        // Force-load semua QR images pada kartu terpilih sebelum print
        const toLoad = [];
        document.querySelectorAll('.qr-card').forEach(card => {
            const isSelected = selectedIds.has(card.dataset.id);
            if (!isSelected) {
                card.classList.add('not-selected');
            } else {
                card.classList.remove('not-selected');
                card.querySelectorAll('.qr-img[data-src]').forEach(img => toLoad.push(img));
            }
        });

        if (toLoad.length === 0) {
            window.print();
            restoreAfterPrint();
            return;
        }

        let loaded = 0;
        toLoad.forEach(img => {
            const src = img.dataset.src;
            img.removeAttribute('data-src');
            img.onload = img.onerror = () => {
                loaded++;
                if (loaded >= toLoad.length) {
                    window.print();
                    restoreAfterPrint();
                }
            };
            img.src = src;
        });
    }

    // Legacy alias (masih dipakai beberapa bagian lama)
    function printSelected() { openModal('selected'); }

    function restoreAfterPrint() {
        setTimeout(() => {
            document.querySelectorAll('.qr-card.not-selected').forEach(c => c.classList.remove('not-selected'));
        }, 1000);
    }
</script>
@endpush
@endsection
