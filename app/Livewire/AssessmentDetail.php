<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\IucnApiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Assessment Detail - IUCN Red List Explorer')]
class AssessmentDetail extends Component
{
    public int $assessmentId;

    public array $assessment = [];

    public bool $loading = true;

    public function mount(int $assessmentId): void
    {
        $this->assessmentId = $assessmentId;
    }

    public function loadData(): void
    {
        $service = app(IucnApiService::class);
        $this->assessment = $service->getAssessmentDetails($this->assessmentId);
        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.assessment-detail');
    }
}
