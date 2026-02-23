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

    public array $assessments = [];

    public bool $loading = true;

    public function mount(int $sisId): void
    {
        $this->sisId = $sisId;
    }

    public function loadData(): void
    {
        $service = app(IucnApiService::class);
        $data = $service->getTaxonDetails($this->sisId);
        $this->taxon = $data['taxon'] ?? [];
        $this->assessments = $data['assessments'] ?? [];
        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.species-detail', [
            'assessments' => $this->assessments,
        ]);
    }
}
