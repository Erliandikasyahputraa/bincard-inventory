<?php

namespace App\Livewire;

use App\Models\CompanySetting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

use Livewire\Attributes\Title;

#[Title('Pengaturan Perusahaan')]
class PengaturanPerusahaan extends Component
{
    use WithFileUploads;

    public string $nama_perusahaan = '';
    public string $alamat = '';
    public string $telepon = '';
    public string $email = '';
    
    public $logo;
    public ?string $logo_path = null;
    
    public int $uploadIteration = 0;

    public function mount(): void
    {
        $setting = CompanySetting::first();
        if ($setting) {
            $this->nama_perusahaan = (string) $setting->nama_perusahaan;
            $this->alamat = (string) $setting->alamat;
            $this->telepon = (string) $setting->telepon;
            $this->email = (string) $setting->email;
            $this->logo_path = $setting->logo_path;
        }
    }

    public function simpan(): void
    {
        $data = $this->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048', // max 2MB
        ]);

        $setting = CompanySetting::query()->firstOrNew();
        
        if ($this->logo) {
            // Hapus logo lama jika ada
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $data['logo_path'] = $this->logo->store('company-logos', 'public');
            $this->logo_path = $data['logo_path'];
            $this->reset('logo');
            $this->uploadIteration++;
        }

        $setting->fill($data)->save();

        $this->dispatch('transaksi-sukses', ['message' => 'Pengaturan perusahaan tersimpan.']);
    }

    public function render()
    {
        return view('livewire.pengaturan-perusahaan')
            ->layout('layouts.app', ['title' => 'Pengaturan Perusahaan']);
    }
}

