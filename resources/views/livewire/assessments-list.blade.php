@php
use App\Services\IucnApiService;

$getCategoryColor = function ($category) {
    return match ($category) {
        'EX', 'EW' => 'bg-black text-white dark:bg-black dark:text-gray-300 border border-gray-700',
        'CR' => 'bg-red-600 text-white dark:bg-red-700 border border-red-800',
        'EN' => 'bg-orange-500 text-white dark:bg-orange-600 border border-orange-700',
        'VU' => 'bg-yellow-400 text-yellow-900 dark:bg-yellow-500 border border-yellow-600',
        'NT' => 'bg-lime-400 text-lime-900 dark:bg-lime-500 border border-lime-600',
        'LC' => 'bg-green-500 text-white dark:bg-green-600 border border-green-700',
        'DD' => 'bg-gray-500 text-white dark:bg-gray-600 border border-gray-700',
        default => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600',
    };
};
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">
    {{-- Header --}}
    <div class="mb-8 space-y-4">
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">
            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Dashboard
        </a>
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Assessments for <span class="text-blue-600 dark:text-blue-400 uppercase">{{ $type }}</span>
                </h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300 font-mono bg-gray-100 dark:bg-gray-800 inline-block px-2 py-1 rounded">
                    Code: {{ $code }}
                </p>
            </div>
            <div class="flex items-center">
                <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-3 py-1 text-sm font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-400/30">
                    {{ number_format($pagination['total']) }} Total Assessments
                </span>
            </div>
        </div>
    </div>

    {{-- Toggle Controls Bar (Sticky) --}}
    <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 py-4 mb-8 -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            
            {{-- View & Scroll Toggles --}}
            <div class="flex items-center gap-2">
                <div class="flex rounded-md shadow-sm" role="group">
                    <button wire:click="toggleViewMode" type="button" class="relative inline-flex items-center rounded-l-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold {{ $viewMode === 'list' ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-gray-700 ring-1 ring-inset ring-blue-600' : 'text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }} focus:z-10 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        List
                    </button>
                    <button wire:click="toggleViewMode" type="button" class="relative -ml-px inline-flex items-center rounded-r-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold {{ $viewMode === 'card' ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-gray-700 ring-1 ring-inset ring-blue-600' : 'text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }} focus:z-10 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Card
                    </button>
                </div>

                <div class="flex rounded-md shadow-sm ml-2" role="group">
                    <button wire:click="toggleScrollMode" type="button" class="relative inline-flex items-center rounded-l-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold {{ $scrollMode === 'paginate' ? 'text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-gray-700 ring-1 ring-inset ring-purple-600' : 'text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }} focus:z-10 transition-colors">
                        Pages
                    </button>
                    <button wire:click="toggleScrollMode" type="button" class="relative -ml-px inline-flex items-center rounded-r-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold {{ $scrollMode === 'scroll' ? 'text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-gray-700 ring-1 ring-inset ring-purple-600' : 'text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }} focus:z-10 transition-colors">
                        Infinite
                    </button>
                </div>
            </div>

            {{-- Filters --}}
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select wire:model.live="yearFilter" class="block w-full md:w-40 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 dark:text-white dark:bg-gray-800 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                <select wire:model.live="extinctFilter" class="block w-full md:w-48 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 dark:text-white dark:bg-gray-800 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
                    <option value="">Extinction: All</option>
                    <option value="yes">Possibly Extinct</option>
                    <option value="no">Not Extinct</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="relative min-h-[50vh]">
        <div wire:loading class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm z-20 flex items-center justify-center rounded-xl">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
        </div>

        @if(count($filteredAssessments) === 0)
            <div class="text-center py-24 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No assessments found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ count($assessments) > 0 ? "Try adjusting your filters to see more results." : "We couldn't find any assessments for this " . $type . "." }}
                </p>
                @if($yearFilter !== '' || $extinctFilter !== '')
                    <div class="mt-6">
                        <button type="button" wire:click="$set('yearFilter', ''); $set('extinctFilter', '');" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        @else
            @if($viewMode === 'list')
                {{-- List View --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 sm:rounded-xl overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200 sm:pl-6">Year</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">Assessment ID</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">Category</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">Flags</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($filteredAssessments as $a)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 dark:text-gray-400 font-mono sm:pl-6">
                                        {{ $a['year_published'] ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium">
                                        <a href="/assessment/{{ $a['assessment_id'] }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:underline">
                                            {{ $a['assessment_id'] }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @php $cat = $a['category'] ?? ''; @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium shadow-sm {{ $getCategoryColor($cat) }}">
                                            {{ $cat }} - {{ IucnApiService::translateCategory($cat) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @if(!empty($a['possibly_extinct']) || !empty($a['possibly_extinct_in_the_wild']))
                                            <span class="inline-flex items-center text-red-600 dark:text-red-400 font-medium" title="Possibly Extinct">
                                                <span class="mr-1 text-lg">💀</span> PE
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="https://www.iucnredlist.org/species/{{ $a['sis_id'] ?? '' }}/{{ $a['assessment_id'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors opacity-0 group-hover:opacity-100 flex items-center justify-end">
                                            IUCN <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Card View --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($filteredAssessments as $a)
                        @php $cat = $a['category'] ?? ''; @endphp
                        <div class="group flex flex-col justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden transition-all duration-200">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-bold uppercase tracking-wider shadow-sm {{ $getCategoryColor($cat) }}">
                                        {{ $cat }}
                                    </span>
                                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded">
                                        {{ $a['year_published'] ?? 'N/A' }}
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                    <a href="/assessment/{{ $a['assessment_id'] }}" wire:navigate class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ $a['assessment_id'] }}
                                    </a>
                                </h3>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                                    {{ IucnApiService::translateCategory($cat) }}
                                </p>

                                @if(!empty($a['possibly_extinct']) || !empty($a['possibly_extinct_in_the_wild']))
                                    <div class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/20 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10 dark:ring-red-500/20">
                                        <span class="mr-1">💀</span> Possibly Extinct
                                    </div>
                                @endif
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center mt-auto">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-mono" title="SIS ID">SIS: {{ $a['sis_id'] ?? 'N/A' }}</span>
                                <a href="https://www.iucnredlist.org/species/{{ $a['sis_id'] ?? '' }}/{{ $a['assessment_id'] }}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 inline-flex items-center transition-colors">
                                    View on IUCN
                                    <svg class="ml-1 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- Pagination & Infinite Scroll Controls --}}
        @if(count($assessments) > 0)
            <div class="mt-8">
                @if($scrollMode === 'paginate')
                    <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing page <span class="font-medium">{{ $page }}</span> of <span class="font-medium">{{ $totalPages }}</span>
                                </p>
                            </div>
                            <div>
                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                    <button wire:click="previousPage" @disabled($page <= 1) class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 dark:text-gray-500 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        <span class="sr-only">Previous</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    
                                    {{-- Simplified page indicator --}}
                                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800">
                                        {{ $page }} / {{ $totalPages }}
                                    </span>

                                    <button wire:click="nextPage" @disabled(!$hasMore) class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 dark:text-gray-500 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        <span class="sr-only">Next</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </nav>
                            </div>
                        </div>
                        {{-- Mobile pagination --}}
                        <div class="flex flex-1 justify-between sm:hidden">
                            <button wire:click="previousPage" @disabled($page <= 1) class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">Previous</button>
                            <button wire:click="nextPage" @disabled(!$hasMore) class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">Next</button>
                        </div>
                    </div>
                @else
                    {{-- Infinite Scroll Load More Button --}}
                    <div class="text-center py-6">
                        @if($hasMore)
                            <button wire:click="loadMore" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all active:scale-95 group">
                                <span wire:loading.remove wire:target="loadMore">Load More Assessments</span>
                                <span wire:loading wire:target="loadMore" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </span>
                                <svg class="ml-2 w-5 h-5 group-hover:translate-y-0.5 transition-transform" wire:loading.remove wire:target="loadMore" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        @else
                            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-800 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <span class="mr-2">🏁</span> You've reached the end of the list
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
