<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2 transition-colors duration-300 ease-in-out">
            <i data-lucide="history" class="w-5 h-5 text-blue-500 transition-colors duration-300 ease-in-out"></i>
            Audit Log (Riwayat Data)
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Pantau seluruh rekam jejak perubahan Master Data.</p>
    </div>
</x-slot:header>

<div class="max-w-7xl mx-auto pb-12">
    <!-- Header -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center justify-end gap-4">
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <i data-lucide="search" wire:loading.remove wire:target="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 transition-colors duration-300 ease-in-out"></i>
                <i data-lucide="loader-2" wire:loading wire:target="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
                <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()" wire:model.live.debounce.300ms="search" placeholder="Cari Log..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-800 dark:text-slate-200 placeholder-slate-500 focus:border-blue-500 dark:border-blue-400 outline-none transition-colors">
            </div>
        </div>
    </div>

    <!-- Log Table -->
    <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-3xl p-6 shadow-xl relative overflow-hidden transition-colors duration-300 ease-in-out">
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
                                    {{ $log->action == 'created' ? 'bg-[#10B981] dark:bg-emerald-500/20 text-[#3FB950] border-[#238636]/30' : '' }}
                                    {{ $log->action == 'updated' ? 'bg-blue-500/10 text-blue-500 border-[#1F6FEB]/30' : '' }}
                                    {{ $log->action == 'deleted' ? 'bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-500/30' : '' }} transition-colors duration-300 ease-in-out">
                                    {{ strtoupper($log->action) }}
                                </span>
                                @php
                                    $modelName = class_basename($log->model_type);
                                    $displayName = "#" . $log->model_id;
                                    
                                    // Extract logical name from historical payload directly (safest)
                                    $newVal = (array) $log->new_values;
                                    $oldVal = (array) $log->old_values;
                                    
                                    if (isset($newVal['name']) && !empty($newVal['name'])) {
                                        $displayName = $newVal['name'];
                                    } elseif (isset($oldVal['name']) && !empty($oldVal['name'])) {
                                        $displayName = $oldVal['name'];
                                    } else {
                                        // Fallback to database lookup
                                        $record = $log->model_type::find($log->model_id);
                                        if ($record) {
                                            $displayName = $record->name ?? $record->nama ?? $displayName;
                                        } else {
                                            $displayName .= " (Dihapus)";
                                        }
                                    }
                                @endphp
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-mono transition-colors duration-300 ease-in-out">
                                    {{ $modelName }}: <br><strong class="text-slate-700 dark:text-slate-300">{{ $displayName }}</strong>
                                </p>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-3 max-h-40 overflow-y-auto no-scrollbar text-[11px] font-mono transition-colors duration-300 ease-in-out">
                                    @php
                                        $keyLabels = [
                                            'name' => 'Nama Produk', 'nama' => 'Nama', 'barcode' => 'Barcode', 'sku' => 'SKU',
                                            'current_stock' => 'Stok Saat Ini', 'min_stock' => 'Stok Minimum', 'max_stock' => 'Stok Maksimum',
                                            'location' => 'Lokasi Rak', 'price' => 'Harga', 'description' => 'Deskripsi', 'telepon' => 'Telepon',
                                            'email' => 'Email', 'alamat' => 'Alamat', 'is_active' => 'Status Aktif'
                                        ];
                                        $ignoredKeys = ['id', 'created_at', 'updated_at', 'deleted_at', 'user_id', 'product_id'];
                                    @endphp
                                    
                                    @if($log->action === 'updated')
                                        @php
                                            $changes = [];
                                            $oldArr = (array)$log->old_values;
                                            $newArr = (array)$log->new_values;
                                            foreach($newArr as $k => $newV) {
                                                if(in_array($k, $ignoredKeys)) continue;
                                                $oldV = $oldArr[$k] ?? null;
                                                if($oldV !== $newV && !($oldV === null && $newV === '')) {
                                                    $label = $keyLabels[$k] ?? ucwords(str_replace('_', ' ', $k));
                                                    $changes[] = [
                                                        'label' => $label,
                                                        'old' => is_array($oldV) ? json_encode($oldV) : $oldV,
                                                        'new' => is_array($newV) ? json_encode($newV) : $newV,
                                                    ];
                                                }
                                            }
                                        @endphp
                                        @if(count($changes) > 0)
                                            <ul class="space-y-1">
                                                @foreach($changes as $change)
                                                    <li class="pl-2 border-l-2 border-blue-500/30 text-slate-700 dark:text-slate-300 break-all text-[11px] font-sans flex items-center flex-wrap gap-1 mb-1.5">
                                                        <span class="font-bold text-slate-800 dark:text-slate-100 uppercase text-[10px] tracking-wider">{{ $change['label'] }}:</span>
                                                        <span class="text-rose-500 line-through bg-rose-500/10 px-1 rounded">{{ $change['old'] ?: '-' }}</span>
                                                        <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400"></i>
                                                        <span class="text-[#10B981] dark:text-emerald-400 font-bold bg-emerald-500/10 px-1 rounded">{{ $change['new'] ?: '-' }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-slate-500 italic">Mendistribusikan metadata pembaruan (Tidak ada field spesifik yang berubah).</span>
                                        @endif
                                        
                                    @elseif($log->action === 'created')
                                        <ul class="space-y-1.5">
                                            @foreach((array)$log->new_values as $k => $v)
                                                @if(in_array($k, $ignoredKeys) || (empty($v) && $v !== 0 && $v !== '0')) @continue @endif
                                                @php $label = $keyLabels[$k] ?? ucwords(str_replace('_', ' ', $k)); @endphp
                                                <li class="pl-2 border-l-2 border-emerald-500/30 text-slate-700 dark:text-slate-300 break-all font-sans text-[11px]">
                                                    <span class="font-bold uppercase text-slate-800 dark:text-slate-100 text-[10px] tracking-wider">{{ $label }}:</span>
                                                    <span class="text-[#10B981] dark:text-emerald-400">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif($log->action === 'deleted')
                                        <ul class="space-y-1.5">
                                            @foreach((array)$log->old_values as $k => $v)
                                                @if(in_array($k, $ignoredKeys) || (empty($v) && $v !== 0 && $v !== '0')) @continue @endif
                                                @php $label = $keyLabels[$k] ?? ucwords(str_replace('_', ' ', $k)); @endphp
                                                <li class="pl-2 border-l-2 border-rose-500/30 text-slate-700 dark:text-slate-300 break-all font-sans text-[11px]">
                                                    <span class="font-bold uppercase text-slate-800 dark:text-slate-100 text-[10px] tracking-wider">{{ $label }}:</span>
                                                    <span class="text-rose-600 dark:text-rose-400">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
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
