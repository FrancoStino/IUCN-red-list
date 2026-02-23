<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\IucnApiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Assessments - IUCN Red List Explorer')]
class AssessmentsList extends Component
{
    // Route parameters
    public string $type;          // 'system' or 'country'
    public string $code;          // system code or country code
    public string $name = '';

    // UI toggles
    public string $viewMode = 'list';     // 'list' or 'card'
    public string $scrollMode = 'paginate'; // 'paginate' or 'scroll'

    // Pagination
    #[Url]
    public int $page = 1;

    // Filters
    #[Url]
    public string $yearFilter = '';
    public string $extinctFilter = ''; // '' (all), 'yes', 'no'

    // Data
    public array $assessments = [];
    public array $allAssessments = []; // for infinite scroll accumulation
    public array $pagination = ['total' => 0, 'per_page' => 100, 'current_page' => 1];
    public bool $hasMore = true;

    public function mount(string $type, string $code): void
    {
        $this->type = $type;
        $this->code = $code;
        
        $service = app(IucnApiService::class);
        if ($this->type === 'system') {
            $systems = $service->getSystems();
            $match = collect($systems)->firstWhere('code', $this->code);
            $this->name = $match['name'] ?? strtoupper($this->code);
        } else {
            $countries = $service->getCountries();
            $match = collect($countries)->firstWhere('code', $this->code);
            $this->name = $match['name'] ?? strtoupper($this->code);
        }

        $this->loadAssessments();
    }

    public function loadAssessments(): void
    {
        $service = app(IucnApiService::class);

        $result = match ($this->type) {
            'system' => $service->getAssessmentsBySystem($this->code, $this->page),
            'country' => $service->getAssessmentsByCountry($this->code, $this->page),
            default => ['assessments' => [], 'pagination' => ['total' => 0, 'per_page' => 100, 'current_page' => 1]],
        };

        $this->pagination = $result['pagination'];

        if ($this->scrollMode === 'scroll') {
            $this->allAssessments = array_merge($this->allAssessments, $result['assessments']);
            $this->assessments = $this->allAssessments;
        } else {
            $this->assessments = $result['assessments'];
        }

        $this->hasMore = $this->page < ($this->pagination['total_pages'] ?? 1);
    }

    public function loadMore(): void
    {
        if ($this->hasMore) {
            $this->page++;
            $this->loadAssessments();
        }
    }

    public function goToPage(int $page): void
    {
        $this->page = $page;
        $this->loadAssessments();
    }

    public function nextPage(): void
    {
        if ($this->hasMore) {
            $this->page++;
            $this->loadAssessments();
        }
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->loadAssessments();
        }
    }

    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === 'list' ? 'card' : 'list';
    }

    public function toggleScrollMode(): void
    {
        $this->scrollMode = $this->scrollMode === 'paginate' ? 'scroll' : 'paginate';
        // Reset on mode change
        $this->page = 1;
        $this->allAssessments = [];
        $this->loadAssessments();
    }

    public function render()
    {
        // Apply client-side filters
        $filtered = collect($this->assessments);

        if ($this->yearFilter !== '') {
            $filtered = $filtered->filter(fn ($a) =>
                ($a['year_published'] ?? '') == $this->yearFilter
            );
        }

        if ($this->extinctFilter === 'yes') {
            $filtered = $filtered->filter(fn ($a) =>
                !empty($a['possibly_extinct']) || !empty($a['possibly_extinct_in_the_wild'])
            );
        } elseif ($this->extinctFilter === 'no') {
            $filtered = $filtered->filter(fn ($a) =>
                empty($a['possibly_extinct']) && empty($a['possibly_extinct_in_the_wild'])
            );
        }

        // Get unique years for filter dropdown
        $years = collect($this->assessments)
            ->pluck('year_published')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return view('livewire.assessments-list', [
            'filteredAssessments' => $filtered->values()->all(),
            'years' => $years,
            'totalPages' => $this->pagination['total_pages'] ?? 1,
        ]);
    }
}
