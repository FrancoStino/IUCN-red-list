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
#[Title('Dashboard - IUCN Red List Explorer')]
class Dashboard extends Component
{
    public array $systems = [];

    public array $countries = [];

    public string $search = '';

    public function mount(): void
    {
        $service = app(IucnApiService::class);
        $this->systems = $service->getSystems();
        $this->countries = $service->getCountries();
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.dashboard')->render();
    }

    public function render()
    {
        $filteredCountries = collect($this->countries)
            ->when($this->search, function ($collection) {
                return $collection->filter(fn ($c) => str_contains(strtolower($c['name'] ?? ''), strtolower($this->search))
                );
            })
            ->values()
            ->all();

        return view('livewire.dashboard', [
            'filteredCountries' => $filteredCountries,
        ]);
    }
}
