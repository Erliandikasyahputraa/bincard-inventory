<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Panduan Sistem BINGO')]
class PanduanSistem extends Component
{
    public function render()
    {
        return view('livewire.panduan-sistem')
            ->layout('layouts.app');
    }
}
