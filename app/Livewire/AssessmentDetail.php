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
#[Title('Assessment Detail - IUCN Red List Explorer')]
class AssessmentDetail extends Component
{
    public int $assessmentId;

    public array $assessment = [];

    public function mount(int $assessmentId): void
    {
        $this->assessmentId = $assessmentId;

        $service = app(IucnApiService::class);
        $this->assessment = $service->getAssessmentDetails($this->assessmentId);
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.assessment-detail')->render();
    }

    public function render()
    {
        return view('livewire.assessment-detail');
    }
}
