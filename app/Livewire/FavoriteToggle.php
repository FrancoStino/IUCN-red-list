<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Favorite;
use Livewire\Component;

class FavoriteToggle extends Component
{
    public int $taxonId;
    public string $scientificName;
    public bool $isFavorite = false;

    public function mount(int $taxonId, string $scientificName): void
    {
        $this->taxonId = $taxonId;
        $this->scientificName = $scientificName;
        $this->isFavorite = Favorite::where('taxon_id', $this->taxonId)->exists();
    }

    public function toggle(): void
    {
        if ($this->isFavorite) {
            Favorite::where('taxon_id', $this->taxonId)->delete();
            $this->isFavorite = false;
        } else {
            Favorite::create([
                'taxon_id' => $this->taxonId,
                'scientific_name' => $this->scientificName,
                'added_at' => now(),
            ]);
            $this->isFavorite = true;
        }
    }

    public function render()
    {
        return view('livewire.favorite-toggle');
    }
}
