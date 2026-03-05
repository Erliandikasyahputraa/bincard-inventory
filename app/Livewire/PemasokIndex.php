<?php

namespace App\Livewire;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class PemasokIndex extends Component
{
    use WithPagination;

    public string $cari = '';

    public function hapus(int $id): void
    {
        Supplier::findOrFail($id)->delete();
        $this->dispatch('sukses', 'Pemasok dihapus.');
    }

    public function render()
    {
        $pemasok = Supplier::when($this->cari !== '', fn ($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
            ->orderBy('nama')
            ->paginate(15);
        return view('livewire.pemasok-index', ['pemasok' => $pemasok])
            ->layout('layouts.app', ['title' => 'Data Pemasok']);
    }
}
