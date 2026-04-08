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
    public array $selectedIds = [];
    public bool $selectAll = false;

    public function hapus(int $id): void
    {
        Customer::findOrFail($id)->delete();
        $this->dispatch('sukses', 'Pelanggan dihapus.');
    }

    public function render()
    {
        $pelanggan = $this->getCurrentQuery()
            ->orderBy('nama')
            ->paginate(15);
        return view('livewire.pelanggan-index', ['pelanggan' => $pelanggan])
            ->layout('layouts.app', ['title' => 'Data Pelanggan']);
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedIds = $value
            ? $this->getCurrentQuery()->pluck('id')->map(fn ($id) => (int) $id)->all()
            : [];
    }

    public function hapusTerpilih(): void
    {
        if (count($this->selectedIds) === 0) {
            return;
        }

        Customer::whereIn('id', $this->selectedIds)->delete();
        $deleted = count($this->selectedIds);
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('sukses', "{$deleted} pelanggan dihapus.");
    }

    private function getCurrentQuery()
    {
        return Customer::when($this->cari !== '', fn ($q) => $q->where('nama', 'like', '%' . $this->cari . '%'));
    }
}
