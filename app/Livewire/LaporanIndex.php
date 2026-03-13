<?php

namespace App\Livewire;

use App\Models\StockTransaction;
use Livewire\Component;

use Livewire\Attributes\Title;

#[Title('Laporan')]
class LaporanIndex extends Component
{
    public string $tanggalMulai = '';
    public string $tanggalSelesai = '';
    public string $tipeTransaksi = '';
    public string $sortBy = 'terbaru';

    public function mount(): void
    {
        $this->tanggalMulai = now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSelesai = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = StockTransaction::with(['product', 'user'])
            ->when($this->tanggalMulai, fn ($q) => $q->whereDate('created_at', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai, fn ($q) => $q->whereDate('created_at', '<=', $this->tanggalSelesai))
            ->when($this->tipeTransaksi !== '', fn ($q) => $q->where('type', $this->tipeTransaksi));

        if ($this->sortBy === 'terbaru') {
            $query->orderByDesc('created_at');
        } elseif ($this->sortBy === 'terlama') {
            $query->orderBy('created_at');
        } elseif ($this->sortBy === 'terbanyak') {
            $query->orderByDesc('quantity');
        } elseif ($this->sortBy === 'terdikit') {
            $query->orderBy('quantity');
        }

        $transaksi = $query->paginate(20);
        return view('livewire.laporan-index', ['transaksi' => $transaksi])
            ->layout('layouts.app', ['title' => 'Laporan']);
    }
}
