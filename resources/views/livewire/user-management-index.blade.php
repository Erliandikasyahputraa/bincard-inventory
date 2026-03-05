<div class="max-w-6xl mx-auto pb-12">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <i data-lucide="users-round" class="w-8 h-8 text-[#58A6FF]"></i>
                Manajemen Pengguna
            </h1>
            <p class="text-slate-400 text-sm mt-1">Kelola akses, jabatan, dan kata sandi akun karyawan Anda.</p>
        </div>
        <div>
            <button wire:click="openModal" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-[#238636] hover:bg-[#2EA043] rounded-xl transition-colors shadow-lg shadow-[#238636]/20">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- User Table List -->
    <div class="bg-[#161B22] border border-[#30363D] rounded-3xl p-6 shadow-xl relative overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#30363D]">
                        <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama & Email</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Peran (Role)</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#30363D]/50 text-slate-300">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#0D1117] transition-colors group">
                            <td class="py-4 px-4">
                                <p class="font-bold text-slate-200 group-hover:text-white transition-colors">{{ $user->name }}
                                    @if(auth()->id() === $user->id)
                                        <span class="ml-2 px-2 py-0.5 rounded-full bg-[#1F6FEB]/10 text-[#58A6FF] text-[10px] font-bold uppercase tracking-wider">Anda</span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500 font-mono">{{ $user->email }}</p>
                            </td>
                            <td class="py-4 px-4">
                                @if($user->hasRole('Admin'))
                                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-full text-xs font-bold">Admin</span>
                                @else
                                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded-full text-xs font-bold">Pelaksana</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openModal({{ $user->id }})" class="p-2 bg-[#21262D] hover:bg-[#30363D] text-[#58A6FF] rounded-lg transition-colors border border-[#30363D]" title="Edit / Reset Password">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                    <button wire:click="hapusUser({{ $user->id }})" wire:confirm="Yakin ingin menghapus pengguna ini secara permanen?" class="p-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-lg transition-colors" title="Hapus Pengguna">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-500">
                                <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                Belum ada data pengguna lainnya.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div class="bg-[#161B22] border border-[#30363D] rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in duration-200">
                <div class="flex items-center justify-between p-6 border-b border-[#30363D]">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i data-lucide="user-cog" class="w-5 h-5 text-[#58A6FF]"></i>
                        {{ $editingUserId ? 'Edit Pengguna & Sandi' : 'Tambah Pengguna Baru' }}
                    </h2>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-white transition-colors"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                
                <form wire:submit="simpan" class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-3 bg-[#0D1117] border border-[#30363D] rounded-xl text-slate-200 outline-none focus:border-[#58A6FF]">
                        @error('name') <span class="text-rose-500 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email Login</label>
                        <input type="email" wire:model="email" class="w-full px-4 py-3 bg-[#0D1117] border border-[#30363D] rounded-xl text-slate-200 outline-none focus:border-[#58A6FF]">
                        @error('email') <span class="text-rose-500 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Peran (Role Akses)</label>
                        <select wire:model="role" class="w-full px-4 py-3 bg-[#0D1117] border border-[#30363D] rounded-xl text-slate-200 outline-none focus:border-[#58A6FF]">
                            @foreach($roles as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-rose-500 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                            Kata Sandi (Password)
                            @if($editingUserId) <span class="text-[#58A6FF] font-normal lowercase normal-case">(Kosongkan jika tidak ingin merubah sandi)</span> @endif
                        </label>
                        <input type="password" wire:model="password" placeholder="Minimal 8 karakter" class="w-full px-4 py-3 bg-[#0D1117] border border-[#30363D] rounded-xl text-slate-200 outline-none focus:border-[#58A6FF]">
                        @error('password') <span class="text-rose-500 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-6 border-t border-[#30363D]/50 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-6 py-2.5 bg-[#21262D] hover:bg-[#30363D] text-slate-300 font-bold rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-[#1F6FEB] hover:bg-[#388BFD] text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 flex items-center" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="simpan"><i data-lucide="save" class="w-4 h-4 inline-block mr-2 -mt-0.5"></i> Simpan Data</span>
                            <span wire:loading wire:target="simpan"><i data-lucide="loader-2" class="w-4 h-4 animate-spin inline-block mr-2 -mt-0.5"></i> Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
