<div class="max-w-7xl mx-auto pb-12">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3 transition-colors duration-300 ease-in-out">
                <i data-lucide="history" class="w-8 h-8 text-blue-500 transition-colors duration-300 ease-in-out"></i>
                Audit Log (Riwayat Data)
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 transition-colors duration-300 ease-in-out">Pantau seluruh rekam jejak perubahan Master Data (Produk, Supplier, Pelanggan).</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 transition-colors duration-300 ease-in-out"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Log..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-800 dark:text-slate-200 placeholder-slate-500 focus:border-blue-500 dark:border-blue-400 outline-none transition-colors">
            </div>
        </div>
    </div>

    <!-- Log Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl relative overflow-hidden transition-colors duration-300 ease-in-out">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse transition-colors duration-300 ease-in-out">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out">
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">Waktu & User</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">Aksi & Modul</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">Ringkasan Perubahan Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-slate-600 dark:text-slate-300 transition-colors duration-300 ease-in-out">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group">
                            <td class="py-4 px-4 align-top w-56">
                                <p class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-slate-900 dark:text-white transition-colors mb-0.5">
                                    {{ $log->user ? $log->user->name : 'Sistem Automatis' }}
                                </p>
                                <p class="text-[11px] text-slate-500 flex items-center gap-1 transition-colors duration-300 ease-in-out">
                                    <i data-lucide="clock" class="w-3 h-3"></i> {{ $log->created_at->format('d M Y H:i') }}
                                </p>
                            </td>
                            <td class="py-4 px-4 align-top w-48">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded border 
                                    {{ $log->action == 'created' ? 'bg-emerald-600 dark:bg-emerald-500/20 text-[#3FB950] border-[#238636]/30' : '' }}
                                    {{ $log->action == 'updated' ? 'bg-blue-500/10 text-blue-500 border-[#1F6FEB]/30' : '' }}
                                    {{ $log->action == 'deleted' ? 'bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-500/30' : '' }} transition-colors duration-300 ease-in-out">
                                    {{ strtoupper($log->action) }}
                                </span>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-mono transition-colors duration-300 ease-in-out">
                                    {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                </p>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-3 max-h-32 overflow-y-auto no-scrollbar text-xs font-mono transition-colors duration-300 ease-in-out">
                                    @if($log->action === 'updated')
                                        <div class="mb-2">
                                            <span class="text-slate-500 italic block mb-1 transition-colors duration-300 ease-in-out">Sebelumnya:</span>
                                            <code class="text-rose-600 dark:text-rose-400 break-all transition-colors duration-300 ease-in-out">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</code>
                                        </div>
                                        <div>
                                            <span class="text-slate-500 italic block mb-1 transition-colors duration-300 ease-in-out">Berubah Menjadi:</span>
                                            <code class="text-emerald-600 dark:text-emerald-400 break-all transition-colors duration-300 ease-in-out">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</code>
                                        </div>
                                    @elseif($log->action === 'created')
                                        <div class="text-emerald-600 dark:text-emerald-400 break-all transition-colors duration-300 ease-in-out">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</div>
                                    @elseif($log->action === 'deleted')
                                        <div class="text-rose-600 dark:text-rose-400 break-all transition-colors duration-300 ease-in-out">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-500 transition-colors duration-300 ease-in-out">
                                <i data-lucide="shield-alert" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                Belum ada riwayat aktivitas modifikasi master data terekam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    </div>
</div>
