<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

use Livewire\Attributes\Title;

#[Title('Manajemen Pengguna')]
class UserManagementIndex extends Component
{
    public $users;
    public $roles;
    public array $selectedIds = [];
    public bool $selectAll = false;

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

    public function updatedSelectAll(bool $value): void
    {
        if (!$value) {
            $this->selectedIds = [];
            return;
        }

        $this->selectedIds = $this->users
            ->where('id', '!=', auth()->id())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
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
            $before = $user->only(['name', 'email']);
            $beforeRole = $user->roles->first()?->name;
            $user->name = $this->name;
            $user->email = $this->email;
            
            if (!empty($this->password)) {
                $user->password = Hash::make($this->password);
            }
            $user->save();
            $user->syncRoles([$this->role]);
            $this->logManualAudit(
                'updated',
                $user->id,
                array_merge($before, ['role' => $beforeRole]),
                ['name' => $user->name, 'email' => $user->email, 'role' => $this->role]
            );

            $this->dispatch('transaksi-sukses', message: 'User berhasil diperbarui.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole($this->role);
            $this->logManualAudit(
                'created',
                $user->id,
                null,
                ['name' => $user->name, 'email' => $user->email, 'role' => $this->role]
            );

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

        $user = User::findOrFail($userId);
        $old = ['name' => $user->name, 'email' => $user->email, 'role' => $user->roles->first()?->name];
        $user->delete();
        $this->logManualAudit('deleted', $userId, $old, null);
        $this->loadUsers();
        $this->dispatch('transaksi-sukses', message: 'User berhasil dihapus.');
    }

    public function hapusUserTerpilih(): void
    {
        if (count($this->selectedIds) === 0) {
            return;
        }

        $ids = collect($this->selectedIds)
            ->reject(fn ($id) => (int) $id === (int) auth()->id())
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (count($ids) === 0) {
            $this->dispatch('transaksi-gagal', message: 'Anda tidak bisa menghapus akun Anda sendiri.');
            return;
        }

        User::whereIn('id', $ids)->delete();
        $this->logManualAudit(
            'deleted',
            0,
            ['target_ids' => $ids, 'target_count' => count($ids)],
            ['bulk' => true, 'note' => 'Bulk delete dari Manajemen Pengguna']
        );
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->loadUsers();
        $this->dispatch('transaksi-sukses', message: count($ids) . ' user berhasil dihapus.');
    }

    private function logManualAudit(string $action, int $modelId, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => User::class,
            'model_id' => $modelId,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    public function render()
    {
        return view('livewire.user-management-index')
            ->layout('layouts.app', ['title' => 'Manajemen Pengguna']);
    }
}
