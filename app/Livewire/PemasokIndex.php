<?php

namespace App\Livewire;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Title;

#[Title('Data Pemasok')]
class PemasokIndex extends Component
{
    use WithPagination;

    public string $cari = '';
    public array $selectedIds = [];
    public bool $selectAll = false;

    public function hapus(int $id): void
    {
        Supplier::findOrFail($id)->delete();
        $this->dispatch('sukses', 'Pemasok dihapus.');
    }

    public function render()
    {
        $pemasok = $this->getCurrentQuery()
            ->orderBy('nama')
            ->paginate(15);
        return view('livewire.pemasok-index', ['pemasok' => $pemasok])
            ->layout('layouts.app', ['title' => 'Data Pemasok']);
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

        Supplier::whereIn('id', $this->selectedIds)->delete();
        $deleted = count($this->selectedIds);
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('sukses', "{$deleted} pemasok dihapus.");
    }

    private function getCurrentQuery()
    {
        return Supplier::when($this->cari !== '', fn ($q) => $q->where('nama', 'like', '%' . $this->cari . '%'));
    }
}
