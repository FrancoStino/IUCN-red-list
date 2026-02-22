<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\IucnApiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Species Detail - IUCN Red List Explorer')]
class SpeciesDetail extends Component
{
    public int $sisId;
    public array $taxon = [];

    public function mount(int $sisId, IucnApiService $service): void
    {
        $this->sisId = $sisId;
        $this->taxon = $service->getTaxonDetails($sisId);
    }

    public function render()
    {
        return view('livewire.species-detail');
    }
}
