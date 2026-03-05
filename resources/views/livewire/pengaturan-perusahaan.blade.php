<div>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Pengaturan Perusahaan</h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm">Atur identitas perusahaan yang akan tampil di Surat Jalan dan laporan.</p>
    </div>
    <form wire:submit="simpan" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-xl space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan *</label>
            <input type="text" wire:model="nama_perusahaan" class="w-full rounded border-gray-300 shadow-sm">
            @error('nama_perusahaan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat</label>
            <textarea wire:model="alamat" rows="3" class="w-full rounded border-gray-300 shadow-sm"></textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telepon</label>
                <input type="text" wire:model="telepon" class="w-full rounded border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full rounded border-gray-300 shadow-sm">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>

