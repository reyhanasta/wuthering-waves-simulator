<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class HomePage extends Component
{
    public $banners = [];

    public function mount()
    {
        $this->banners = [
            ['image' => Storage::url('public/images/background/gacha-banner-2.jpg'), 'title' => 'Standard Banner'],
            ['image' => Storage::url('public/images/background/gacha-banner-2.jpg'), 'title' => 'Weapon Banner'],
            ['image' => Storage::url('public/images/background/gacha-banner-2.jpg'), 'title' => 'Limited Banner'],
        ];
    }
    public function render()
    {
        return view('livewire.home-page');
    }
}
