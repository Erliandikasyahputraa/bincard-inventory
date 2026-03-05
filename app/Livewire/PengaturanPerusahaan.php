<?php

namespace App\Livewire;

use App\Models\CompanySetting;
use Livewire\Component;

class PengaturanPerusahaan extends Component
{
    public string $nama_perusahaan = '';
    public string $alamat = '';
    public string $telepon = '';
    public string $email = '';

    public function mount(): void
    {
        $setting = CompanySetting::first();
        if ($setting) {
            $this->nama_perusahaan = (string) $setting->nama_perusahaan;
            $this->alamat = (string) $setting->alamat;
            $this->telepon = (string) $setting->telepon;
            $this->email = (string) $setting->email;
        }
    }

    public function simpan(): void
    {
        $data = $this->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        CompanySetting::query()->firstOrNew()->fill($data)->save();

        $this->dispatch('transaksi-sukses', ['message' => 'Pengaturan perusahaan tersimpan.']);
    }

    public function render()
    {
        return view('livewire.pengaturan-perusahaan')
            ->layout('layouts.app', ['title' => 'Pengaturan Perusahaan']);
    }
}

