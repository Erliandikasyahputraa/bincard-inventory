<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Data Produk</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Kelola daftar seluruh inventaris gudang.</p>
    </div>
</x-slot:header>

<div class="w-full"
    x-data="{
        selected: [],
        selectAll: false,
        allIds: {{ json_encode($produk->pluck('id')->values()) }},
        toggleAll() {
            if (this.selectAll) {
                this.selected = [...this.allIds];
            } else {
                this.selected = [];
            }
        },
        toggleOne(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(i => i !== id);
            } else {
                this.selected.push(id);
            }
            this.selectAll = this.selected.length === this.allIds.length;
        },
        async deleteSelected() {
            if (this.selected.length === 0) return;
            const ids = [...this.selected];
            this.selected = [];
            this.selectAll = false;
            await $wire.hapusTerpilih(ids);
        }
    }">
    <div class="flex flex-col lg:flex-row justify-end lg:items-center gap-4 mb-4 lg:mb-6">
        <div class="flex flex-col sm:flex-row w-full lg:w-auto gap-3 flex-1 lg:flex-none">
            <div class="relative group flex-1 md:w-80 lg:w-96">
                <i data-lucide="search" wire:loading.remove wire:target="cari" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 w-4 h-4 transition-all duration-300 ease-in-out"></i>
                <i data-lucide="loader-2" wire:loading wire:target="cari" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
                <input type="text" enterkeyhint="search" x-on:keydown.enter="$el.blur()" wire:model.live.debounce.500ms="cari" placeholder="Cari nama, barcode, SKU..."
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-500 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all duration-300 text-sm">
                <button x-show="$wire.cari !== ''" wire:click="$set('cari', '')" type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            <div class="relative w-full sm:w-48 flex-shrink-0">
                <i data-lucide="filter" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 w-4 h-4 transition-colors duration-300 ease-in-out"></i>
                <select wire:model.live="sortBy" class="w-full pl-10 pr-8 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all duration-300 text-sm appearance-none cursor-pointer">
                    <option value="newest">Terbaru</option>
                    <option value="filter_kritis">Hanya Stok Kritis</option>
                    <option value="filter_habis">Hanya Stok Habis</option>
                    <option value="name_asc">Nama (A-Z)</option>
                    <option value="name_desc">Nama (Z-A)</option>
                    <option value="stock_highest">Stok Terbanyak</option>
                    <option value="stock_lowest">Stok Terendah</option>
                    <option value="rack_asc">Lokasi / Rak</option>
                </select>
                <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none transition-colors duration-300 ease-in-out"></i>
            </div>
            
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('produk.import') }}" class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-slate-500 dark:text-slate-400 transition-colors duration-300 ease-in-out"></i> Import
                </a>
                <a href="{{ route('produk.tambah') }}" class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2.5 bg-[#10B981] dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-[#10B981] text-white font-bold rounded-xl transition-colors shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 text-sm whitespace-nowrap">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah
                </a>
            </div>
        </div>
    </div>
    <div wire:loading wire:target="cari,sortBy,gotoPage,nextPage,previousPage" class="mb-4">
        <div class="animate-pulse h-16 rounded-xl bg-slate-100 dark:bg-slate-800"></div>
    </div>
    {{-- Bulk action bar (Alpine-driven, no server roundtrip) --}}
    <div x-show="selected.length > 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-center justify-between bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl px-4 py-3">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-500 text-white text-xs font-bold" x-text="selected.length"></span>
            <p class="text-sm text-rose-700 dark:text-rose-300 font-medium">produk dipilih</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="selected = []; selectAll = false"
                class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors px-2 py-1">
                Batalkan
            </button>
            <button type="button"
                wire:loading.attr="disabled" wire:target="hapusTerpilih"
                @click="if(confirm('Hapus ' + selected.length + ' produk? Tindakan ini tidak bisa dibatalkan.')) deleteSelected()"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition-colors disabled:opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Hapus <span x-text="selected.length"></span> Produk
            </button>
        </div>
    </div>
    <!-- Mobile Card View (visible on small screens) -->
    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="cari,sortBy,gotoPage,nextPage,previousPage,hapusTerpilih,selectAll"
        class="md:hidden flex flex-col divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900 rounded-2xl border border-[#D1D5DB] dark:border-slate-800 overflow-hidden shadow-sm transition-all duration-300 ease-in-out">
        @forelse($produk as $p)
            @php
                $isHabis = $p->current_stock == 0;
                $isKritis = !$isHabis && $p->current_stock <= $p->min_stock;
                $rowBg = $isHabis
                    ? 'bg-rose-50 dark:bg-rose-900/10 border-l-4 border-l-rose-400'
                    : ($isKritis ? 'bg-amber-50 dark:bg-amber-900/10 border-l-4 border-l-amber-400' : '');
            @endphp
            <div class="px-3 py-2.5 flex items-center gap-2.5 {{ $rowBg }} transition-colors">
                {{-- Stock badge (compact) --}}
                <span class="shrink-0 text-xs font-bold w-8 text-center {{ $isHabis ? 'text-rose-600' : ($isKritis ? 'text-amber-600' : 'text-emerald-600 dark:text-emerald-400') }}">
                    {{ $p->current_stock }}
                </span>
                {{-- Product info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate leading-snug">{{ $p->name }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[10px] text-slate-400 font-mono">{{ $p->barcode }}</span>
                        @if($p->location)
                            <span class="text-[9px] px-1 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded font-mono">{{ $p->location }}</span>
                        @endif
                        @if($isHabis)
                            <span class="text-[8px] font-bold text-rose-600 bg-rose-100 dark:bg-rose-900/30 px-1 py-0.5 rounded uppercase">HABIS</span>
                        @elseif($isKritis)
                            <span class="text-[8px] font-bold text-amber-600 bg-amber-100 dark:bg-amber-900/30 px-1 py-0.5 rounded uppercase">KRITIS</span>
                        @endif
                    </div>
                </div>
                {{-- Actions --}}
                <div class="flex items-center shrink-0">
                    <a href="{{ route('produk.bin-card', $p->id) }}" wire:navigate
                        class="p-1.5 rounded-lg text-slate-400 hover:text-[#10B981] transition-colors" title="Bin Card">
                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                    </a>
                    <a href="{{ route('produk.edit', $p->id) }}"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-blue-500 transition-colors" title="Edit">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </a>
                    <button type="button" wire:click="hapus({{ $p->id }})" wire:confirm="Seluruh riwayat transaksi produk ini (ledger) mungkin akan terpengaruh. Lanjutkan menghapus?"
                        wire:loading.attr="disabled" wire:target="hapus"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 transition-colors" title="Hapus">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 flex flex-col items-center gap-2">
                <i data-lucide="package-search" class="w-8 h-8 text-slate-300"></i>
                <p class="text-slate-400 text-sm">Tidak ada produk ditemukan</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View (hidden on small screens) -->
    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="cari,sortBy,gotoPage,nextPage,previousPage,hapusTerpilih,selectAll"
         class="hidden md:block bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl transition-all duration-300 ease-in-out">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px] transition-colors duration-300 ease-in-out">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">
                        <th class="px-3 py-4 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </th>
                        <th class="px-6 py-4 font-semibold whitespace-nowrap">Barcode / SKU</th>
                        <th class="px-6 py-4 font-semibold">Nama Produk</th>
                        <th class="px-6 py-4 font-semibold text-center whitespace-nowrap transition-colors duration-300 ease-in-out">Stok Saat Ini</th>
                        <th class="px-6 py-4 font-semibold text-center transition-colors duration-300 ease-in-out">Lokasi Rak</th>
                        <th class="px-6 py-4 font-semibold text-right transition-colors duration-300 ease-in-out">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($produk as $p)
                        @php
                            $dIsHabis = $p->current_stock == 0;
                            $dIsKritis = !$dIsHabis && $p->current_stock <= $p->min_stock;
                            $dRowBg = $dIsHabis
                                ? 'bg-rose-50 dark:bg-rose-900/10'
                                : ($dIsKritis ? 'bg-amber-50 dark:bg-amber-900/10' : '');
                        @endphp
                        <tr :class="selected.includes({{ $p->id }}) ? 'bg-rose-50/60 dark:bg-rose-900/10 ring-1 ring-inset ring-rose-200 dark:ring-rose-900' : '{{ $dRowBg }}'"
                            class="border-l-4 {{ $dIsHabis ? 'border-l-rose-400' : ($dIsKritis ? 'border-l-amber-400' : 'border-l-transparent') }} hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors group">
                            <td class="px-3 py-4 text-center">
                                <input type="checkbox" :checked="selected.includes({{ $p->id }})" @change="toggleOne({{ $p->id }})" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-800 dark:text-slate-200 font-mono text-xs transition-colors duration-300 ease-in-out">{{ $p->barcode }}</span>
                                    <span class="text-slate-500 font-mono text-[10px] mt-0.5 transition-colors duration-300 ease-in-out">{{ $p->sku }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-900 dark:text-white font-medium text-sm block line-clamp-2 transition-colors duration-300 ease-in-out">{{ $p->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center transition-colors duration-300 ease-in-out">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-lg font-bold {{ $dIsHabis ? 'text-rose-600 dark:text-rose-400' : ($dIsKritis ? 'text-amber-600 dark:text-amber-400' : 'text-blue-500') }} transition-colors duration-300 ease-in-out">{{ $p->current_stock }}</span>
                                    @if($dIsHabis)
                                        <span class="text-[9px] text-rose-600 dark:text-rose-500 font-bold uppercase tracking-wider">Habis</span>
                                    @elseif($dIsKritis)
                                        <span class="text-[9px] text-amber-600 dark:text-amber-500 font-bold uppercase tracking-wider">Kritis</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center transition-colors duration-300 ease-in-out">
                                <span class="inline-block px-2 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-md text-slate-500 dark:text-slate-400 text-xs font-mono transition-colors duration-300 ease-in-out">{{ $p->location ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right transition-colors duration-300 ease-in-out">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('produk.bin-card', $p->id) }}" wire:navigate
                                        class="p-2 text-slate-500 dark:text-slate-400 hover:text-[#10B981] hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Lihat Bin Card">
                                        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('produk.edit', $p->id) }}" class="p-2 text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Edit Data">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <button type="button" wire:click="hapus({{ $p->id }})" wire:confirm="Seluruh riwayat transaksi produk ini (ledger) mungkin akan terpengaruh. Lanjutkan menghapus?"
                                        wire:loading.attr="disabled" wire:target="hapus"
                                        class="p-2 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Hapus Permanen">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center transition-colors duration-300 ease-in-out">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out">
                                    <i data-lucide="package-search" class="w-8 h-8 text-slate-500 transition-colors duration-300 ease-in-out"></i>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 font-medium mb-1 transition-colors duration-300 ease-in-out">Tidak ada produk ditemukan</p>
                                <p class="text-slate-500 text-sm transition-colors duration-300 ease-in-out">Tambahkan produk baru atau import dari Excel.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produk->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 transition-colors duration-300 ease-in-out">
            {{ $produk->links(data: ['scrollTo' => false]) }}
        </div>
        @endif
    </div>

    {{-- Mobile pagination --}}
    @if($produk->hasPages())
    <div class="md:hidden px-2 py-3">
        {{ $produk->links(data: ['scrollTo' => false]) }}
    </div>
    @endif
</div>
