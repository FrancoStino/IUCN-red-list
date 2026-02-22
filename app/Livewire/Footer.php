<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\IucnApiService;
use Livewire\Component;

class Footer extends Component
{
    public int $speciesCount = 0;
    public string $redListVersion = 'N/A';
    public string $apiVersion = 'v4';

    public function mount(IucnApiService $service): void
    {
        $stats = $service->getStatistics();
        $this->speciesCount = $stats['species_count'];
        $this->redListVersion = $stats['red_list_version'];
        $this->apiVersion = $stats['api_version'];
    }

    public function render()
    {
        return view('livewire.footer');
    }
}
