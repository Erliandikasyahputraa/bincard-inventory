<?php

namespace App\Livewire;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Title;

#[Title('Audit Log')]
class AuditLogIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($inner) {
                    $inner->where('model_type', 'like', '%' . (string) $this->search . '%')
                        ->orWhere('action', 'like', '%' . (string) $this->search . '%')
                        ->orWhereHas('user', function ($q) {
                            $q->where('name', 'like', '%' . (string) $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(15);
            
        return view('livewire.audit-log-index', compact('logs'))
            ->layout('layouts.app', ['title' => 'Log Aktivitas Master Data - BINGO']);
    }
}
