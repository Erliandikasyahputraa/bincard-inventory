<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2 transition-colors duration-300 ease-in-out">
            <i data-lucide="users-round" class="w-5 h-5 text-blue-500 transition-colors duration-300 ease-in-out"></i>
            Manajemen Pengguna
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Kelola akses, jabatan, dan kata sandi akun karyawan Anda.</p>
    </div>
</x-slot:header>

<div class="max-w-6xl mx-auto pb-12">
    <div class="mb-6 flex flex-col md:flex-row justify-end items-end gap-4">
        <div>
            <button wire:click="openModal" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-[#16A34A] dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-[#16A34A] rounded-xl transition-colors shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- User Table List -->
    <div class="bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-3xl p-6 shadow-xl relative overflow-hidden transition-colors duration-300 ease-in-out">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse transition-colors duration-300 ease-in-out">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out">
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">Nama & Email</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">Peran (Role)</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right transition-colors duration-300 ease-in-out">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-slate-600 dark:text-slate-300 transition-colors duration-300 ease-in-out">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group">
                            <td class="py-4 px-4">
                                <p class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-slate-900 dark:text-white transition-colors">{{ $user->name }}
                                    @if(auth()->id() === $user->id)
                                        <span class="ml-2 px-2 py-0.5 rounded-full bg-[#3B82F6] dark:bg-blue-500/10 text-blue-500 text-[10px] font-bold uppercase tracking-wider transition-colors duration-300 ease-in-out">Anda</span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500 font-mono transition-colors duration-300 ease-in-out">{{ $user->email }}</p>
                            </td>
                            <td class="py-4 px-4">
                                @if($user->hasRole('Admin'))
                                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-full text-xs font-bold transition-colors duration-300 ease-in-out">Admin</span>
                                @else
                                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded-full text-xs font-bold transition-colors duration-300 ease-in-out">Pelaksana</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right transition-colors duration-300 ease-in-out">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openModal({{ $user->id }})" class="p-2 sm:px-3 sm:py-2 bg-[#3B82F6] dark:bg-blue-500 hover:bg-[#388BFD] text-slate-900 dark:text-white rounded-lg transition-colors border border-[#1F6FEB] shadow-lg shadow-[#1F6FEB]/20 flex items-center gap-2" title="Edit / Reset Password">
                                        <i data-lucide="pencil" class="w-4 h-4"></i> <span class="hidden sm:inline text-xs font-bold transition-colors duration-300 ease-in-out">Edit</span>
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                    <button wire:click="hapusUser({{ $user->id }})" wire:confirm="Yakin ingin menghapus pengguna ini secara permanen?" class="p-2 sm:px-3 sm:py-2 bg-rose-600 hover:bg-rose-500 text-slate-900 dark:text-white rounded-lg transition-colors border border-rose-600 shadow-lg shadow-rose-600/20 flex items-center gap-2" title="Hapus Pengguna">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i> <span class="hidden sm:inline text-xs font-bold transition-colors duration-300 ease-in-out">Hapus</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-500 transition-colors duration-300 ease-in-out">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 transition-colors duration-300 ease-in-out">
            <div class="bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in duration-200 transition-colors duration-300 ease-in-out">
                <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 transition-colors duration-300 ease-in-out">
                        <i data-lucide="user-cog" class="w-5 h-5 text-blue-500 transition-colors duration-300 ease-in-out"></i>
                        {{ $editingUserId ? 'Edit Pengguna & Sandi' : 'Tambah Pengguna Baru' }}
                    </h2>
                    <button wire:click="closeModal" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:text-white transition-colors"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                
                <form wire:submit="simpan" class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 outline-none focus:border-blue-500 dark:border-blue-400 transition-colors duration-300 ease-in-out">
                        @error('name') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Email Login</label>
                        <input type="email" wire:model="email" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 outline-none focus:border-blue-500 dark:border-blue-400 transition-colors duration-300 ease-in-out">
                        @error('email') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Peran (Role Akses)</label>
                        <select wire:model="role" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 outline-none focus:border-blue-500 dark:border-blue-400 transition-colors duration-300 ease-in-out">
                            @foreach($roles as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                    </div>
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">
                            Kata Sandi (Password)
                            @if($editingUserId) <span class="text-blue-500 font-normal lowercase normal-case transition-colors duration-300 ease-in-out">(Kosongkan jika tidak ingin merubah sandi)</span> @endif
                        </label>
                        <input type="password" wire:model="password" placeholder="Minimal 8 karakter" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 outline-none focus:border-blue-500 dark:border-blue-400 transition-colors duration-300 ease-in-out">
                        @error('password') <span class="text-rose-600 dark:text-rose-500 text-[11px] font-bold mt-1 block transition-colors duration-300 ease-in-out">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-6 border-t border-slate-200 dark:border-slate-800/50 flex justify-end gap-3 transition-colors duration-300 ease-in-out">
                        <button type="button" wire:click="closeModal" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-[#3B82F6] dark:bg-blue-500 hover:bg-[#388BFD] text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 flex items-center" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="simpan"><i data-lucide="save" class="w-4 h-4 inline-block mr-2 -mt-0.5"></i> Simpan Data</span>
                            <span wire:loading wire:target="simpan"><i data-lucide="loader-2" class="w-4 h-4 animate-spin inline-block mr-2 -mt-0.5"></i> Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

