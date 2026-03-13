<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Title;

#[Title('Data Pelanggan')]
class PelangganIndex extends Component
{
    use WithPagination;

    public string $cari = '';

    public function hapus(int $id): void
    {
        Customer::findOrFail($id)->delete();
        $this->dispatch('sukses', 'Pelanggan dihapus.');
    }

    public function render()
    {
        $pelanggan = Customer::when($this->cari !== '', fn ($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
            ->orderBy('nama')
            ->paginate(15);
        return view('livewire.pelanggan-index', ['pelanggan' => $pelanggan])
            ->layout('layouts.app', ['title' => 'Data Pelanggan']);
    }
}
