<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

use Livewire\Attributes\Title;

#[Title('Data Pelanggan')]
class PelangganForm extends Component
{
    public ?int $pelangganId = null;
    public string $nama = '';
    public string $alamat = '';
    public string $telepon = '';
    public string $email = '';

    public function mount(?int $id = null): void
    {
        if ($id !== null) {
            $c = Customer::findOrFail($id);
            $this->pelangganId = $c->id;
            $this->nama = $c->nama;
            $this->alamat = $c->alamat ?? '';
            $this->telepon = $c->telepon ?? '';
            $this->email = $c->email ?? '';
        }
    }

    public function simpan(): void
    {
        $this->validate(['nama' => 'required|string|max:255']);
        $data = ['nama' => $this->nama, 'alamat' => $this->alamat ?: null, 'telepon' => $this->telepon ?: null, 'email' => $this->email ?: null];
        if ($this->pelangganId !== null) {
            Customer::findOrFail($this->pelangganId)->update($data);
            session()->flash('sukses', 'Pelanggan diperbarui.');
        } else {
            Customer::create($data);
            session()->flash('sukses', 'Pelanggan ditambahkan.');
        }
        $this->redirect(route('pelanggan.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pelanggan-form')
            ->layout('layouts.app', ['title' => $this->pelangganId ? 'Edit Pelanggan' : 'Tambah Pelanggan']);
    }
}
