<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Favorite;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Favorites - IUCN Red List Explorer')]
class FavoritesList extends Component
{
    public function removeFavorite(int $taxonId): void
    {
        Favorite::where('taxon_id', $taxonId)->delete();
        $this->dispatch('favorites-updated');
    }

    public function render()
    {
        return view('livewire.favorites-list', [
            'favorites' => Favorite::orderByDesc('added_at')->get(),
        ]);
    }
}
