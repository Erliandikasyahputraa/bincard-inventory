<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserManagementIndex extends Component
{
    public $users;
    public $roles;

    // Modal state
    public bool $isModalOpen = false;
    public ?int $editingUserId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'Pelaksana';

    public function mount()
    {
        $this->roles = Role::pluck('name')->toArray();
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = User::with('roles')->get();
    }

    public function openModal(?int $userId = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'password', 'role']);
        $this->editingUserId = $userId;

        if ($userId) {
            $user = User::findOrFail($userId);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->roles->first()?->name ?? 'Pelaksana';
        }

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function simpan()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->editingUserId)],
            'role' => 'required|in:' . implode(',', $this->roles),
        ];

        // Jika mode buat baru, password wajib. Jika mode edit, password opsional (untuk reset).
        if (!$this->editingUserId) {
            $rules['password'] = 'required|min:8';
        } else {
            $rules['password'] = 'nullable|min:8';
        }

        $this->validate($rules);

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->name = $this->name;
            $user->email = $this->email;
            
            if (!empty($this->password)) {
                $user->password = Hash::make($this->password);
            }
            $user->save();
            $user->syncRoles([$this->role]);

            $this->dispatch('transaksi-sukses', message: 'User berhasil diperbarui.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole($this->role);

            $this->dispatch('transaksi-sukses', message: 'User baru berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->loadUsers();
    }

    public function hapusUser(int $userId)
    {
        if (auth()->id() === $userId) {
            $this->dispatch('transaksi-gagal', message: 'Anda tidak bisa menghapus akun Anda sendiri.');
            return;
        }

        User::findOrFail($userId)->delete();
        $this->loadUsers();
        $this->dispatch('transaksi-sukses', message: 'User berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.user-management-index')
            ->layout('layouts.app', ['title' => 'Manajemen Pengguna']);
    }
}
