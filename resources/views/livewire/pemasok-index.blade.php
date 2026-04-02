<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Data Pemasok</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Kelola data vendor dan supplier barang.</p>
    </div>
</x-slot:header>

<div class="w-full">
    <div class="flex flex-col sm:flex-row justify-end items-center gap-4 mb-4 lg:mb-6">
        <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
            <div class="relative group flex-1 md:w-64">
                <i data-lucide="search" wire:loading.remove wire:target="cari" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 w-4 h-4 transition-colors duration-300 ease-in-out"></i>
                <i data-lucide="loader-2" wire:loading wire:target="cari" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
                <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()" wire:model.live.debounce.300ms="cari" placeholder="Cari nama..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-500 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all duration-300 text-sm">
            </div>
            <a href="{{ route('pemasok.tambah') }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-[#3B82F6] dark:bg-blue-500 hover:bg-[#388BFD] text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 text-sm whitespace-nowrap">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Pemasok
            </a>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl transition-colors duration-300 ease-in-out">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px] transition-colors duration-300 ease-in-out">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">
                        <th class="px-6 py-4 font-semibold whitespace-nowrap">Nama Pemasok</th>
                        <th class="px-6 py-4 font-semibold w-40">Telepon</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold text-right transition-colors duration-300 ease-in-out">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($pemasok as $s)
                        <tr class="hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                            <td class="px-6 py-4 text-slate-800 dark:text-slate-200 text-sm font-medium transition-colors duration-300 ease-in-out">{{ $s->nama }}</td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm font-mono transition-colors duration-300 ease-in-out"><a href="tel:{{ $s->telepon }}" class="hover:text-blue-500 transition-colors">{{ $s->telepon ?: '-' }}</a></td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm transition-colors duration-300 ease-in-out"><a href="mailto:{{ $s->email }}" class="hover:text-blue-500 transition-colors">{{ $s->email ?: '-' }}</a></td>
                            <td class="px-6 py-4 text-right transition-colors duration-300 ease-in-out">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('pemasok.edit', $s->id) }}" class="p-2 text-slate-500 dark:text-slate-400 hover:text-blue-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Edit Data">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <button type="button" wire:click="hapus({{ $s->id }})" wire:confirm="Yakin menghapus pemasok ini secara permanen?"
                                        class="p-2 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Hapus Permanen">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center transition-colors duration-300 ease-in-out">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out">
                                    <i data-lucide="truck" class="w-8 h-8 text-slate-500 transition-colors duration-300 ease-in-out"></i>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 font-medium mb-1 transition-colors duration-300 ease-in-out">Belum ada Pemasok</p>
                                <p class="text-slate-500 text-sm transition-colors duration-300 ease-in-out">Silakan tambahkan data supplier / vendor Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pemasok->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 transition-colors duration-300 ease-in-out">
            {{ $pemasok->links() }}
        </div>
        @endif
    </div>
</div>

