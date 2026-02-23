<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Favorite;
use Livewire\Attributes\On;
use Livewire\Component;

class FavoritesCount extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->count = Favorite::count();
    }

    #[On('favorites-updated')]
    public function refreshCount(): void
    {
        $this->count = Favorite::count();
    }

    public function render()
    {
        return view('livewire.favorites-count');
    }
}
