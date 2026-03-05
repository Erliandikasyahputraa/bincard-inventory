<div class="w-full">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Data Pelanggan</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola data pembeli atau pelanggan tujuan.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
            <div class="relative group flex-1 md:w-64">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 w-4 h-4"></i>
                <input type="text" wire:model.live.debounce.300ms="cari" placeholder="Cari nama..."
                    class="w-full pl-10 pr-4 py-2.5 bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-500 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all duration-300 text-sm">
            </div>
            <a href="{{ route('pelanggan.tambah') }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-[#1F6FEB] hover:bg-[#388BFD] text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 text-sm whitespace-nowrap">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Pelanggan
            </a>
        </div>
    </div>

    <div class="bg-[#161B22] border border-[#30363D] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="border-b border-[#30363D] bg-[#0D1117] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold whitespace-nowrap">Nama Pelanggan</th>
                        <th class="px-6 py-4 font-semibold w-40">Telepon</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#30363D]">
                    @forelse($pelanggan as $c)
                        <tr class="hover:bg-[#21262D] transition-colors group">
                            <td class="px-6 py-4 text-slate-800 dark:text-slate-200 text-sm font-medium">{{ $c->nama }}</td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm font-mono"><a href="tel:{{ $c->telepon }}" class="hover:text-[#58A6FF] transition-colors">{{ $c->telepon ?: '-' }}</a></td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm"><a href="mailto:{{ $c->email }}" class="hover:text-[#58A6FF] transition-colors">{{ $c->email ?: '-' }}</a></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('pelanggan.edit', $c->id) }}" class="p-2 text-slate-500 dark:text-slate-400 hover:text-[#58A6FF] hover:bg-[#1F6FEB]/10 rounded-lg transition-colors" title="Edit Data">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <button type="button" wire:click="hapus({{ $c->id }})" wire:confirm="Yakin menghapus pelanggan ini secara permanen?" class="p-2 text-slate-500 dark:text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus Permanen">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <div class="w-16 h-16 bg-[#21262D] rounded-full flex items-center justify-center mx-auto mb-4 border border-[#30363D]">
                                    <i data-lucide="users" class="w-8 h-8 text-slate-500"></i>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 font-medium mb-1">Belum ada Pelanggan</p>
                                <p class="text-slate-500 text-sm">Silakan tambahkan data konsumen atau relasi Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pelanggan->hasPages())
        <div class="px-6 py-4 border-t border-[#30363D] bg-[#0D1117]/50">
            {{ $pelanggan->links() }}
        </div>
        @endif
    </div>
</div>
