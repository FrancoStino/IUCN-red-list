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

    public function mount(int $assessmentId, IucnApiService $service): void
    {
        $this->assessmentId = $assessmentId;
        $this->assessment = $service->getAssessmentDetails($assessmentId);
    }

    public function render()
    {
        return view('livewire.assessment-detail');
    }
}
