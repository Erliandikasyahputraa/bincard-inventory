<?php

namespace App\Livewire;

use App\Models\Supplier;
use Livewire\Component;

class PemasokForm extends Component
{
    public ?int $pemasokId = null;
    public string $nama = '';
    public string $alamat = '';
    public string $telepon = '';
    public string $email = '';

    public function mount(?int $id = null): void
    {
        if ($id !== null) {
            $s = Supplier::findOrFail($id);
            $this->pemasokId = $s->id;
            $this->nama = $s->nama;
            $this->alamat = $s->alamat ?? '';
            $this->telepon = $s->telepon ?? '';
            $this->email = $s->email ?? '';
        }
    }

    public function simpan(): void
    {
        $this->validate([
            'nama' => 'required|string|max:255',
        ]);
        $data = ['nama' => $this->nama, 'alamat' => $this->alamat ?: null, 'telepon' => $this->telepon ?: null, 'email' => $this->email ?: null];
        if ($this->pemasokId !== null) {
            Supplier::findOrFail($this->pemasokId)->update($data);
            session()->flash('sukses', 'Pemasok diperbarui.');
        } else {
            Supplier::create($data);
            session()->flash('sukses', 'Pemasok ditambahkan.');
        }
        $this->redirect(route('pemasok.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pemasok-form')
            ->layout('layouts.app', ['title' => $this->pemasokId ? 'Edit Pemasok' : 'Tambah Pemasok']);
    }
}
