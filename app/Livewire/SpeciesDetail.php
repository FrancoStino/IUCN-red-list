<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\IucnApiService;
use Livewire\Attributes\Defer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Defer]
#[Layout('components.layouts.app')]
#[Title('Species Detail - IUCN Red List Explorer')]
class SpeciesDetail extends Component
{
    public int $sisId;

    public array $taxon = [];

    public array $assessments = [];

    public function mount(int $sisId): void
    {
        $this->sisId = $sisId;

        $service = app(IucnApiService::class);
        $data = $service->getTaxonDetails($this->sisId);
        $this->taxon = $data['taxon'] ?? [];
        $this->assessments = $data['assessments'] ?? [];
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.species-detail')->render();
    }

    public function render()
    {
        return view('livewire.species-detail', [
            'assessments' => $this->assessments,
        ]);
    }
}
