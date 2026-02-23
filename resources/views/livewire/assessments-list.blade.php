@php
use App\Services\IucnApiService;

$getCategoryColor = function ($category) {
    return match ($category) {
        'EX', 'EW' => 'bg-black text-white border border-gray-700',
        'CR' => 'bg-red-600 text-white border border-red-800',
        'EN' => 'bg-orange-500 text-white border border-orange-700',
        'VU' => 'bg-yellow-400 text-yellow-900 border border-yellow-600',
        'NT' => 'bg-lime-400 text-lime-900 border border-lime-600',
        'LC' => 'bg-green-500 text-white border border-green-700',
        'DD' => 'bg-gray-500 text-white border border-gray-700',
        default => 'bg-gray-200 text-gray-800 border border-gray-300',
    };
};
@endphp

<div wire:init="loadData">
    {{-- Header --}}
    <div class="mb-8 space-y-4">
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-emerald-700 hover:text-emerald-900 transition-colors group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Dashboard
        </a>

        {{-- Title area --}}
        <div class="relative rounded-2xl bg-emerald-950 overflow-hidden px-8 py-8 sm:px-10">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-emerald-400 blur-3xl"></div>
            </div>
            <div class="relative flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ $name }}
                    </h1>
                    <p class="mt-2 text-sm text-emerald-300/70 font-mono bg-emerald-900/40 inline-block px-3 py-1 rounded-lg">
                        {{ ucfirst($type) }} · Code: {{ $code }}
                    </p>
                </div>
                <div class="flex items-center">
                    @if($loading)
                        <span class="inline-flex items-center rounded-full bg-emerald-800/50 border border-emerald-700/50 px-4 py-1.5 text-sm font-semibold text-emerald-300 animate-pulse">
                            Loading...
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-emerald-800/50 border border-emerald-700/50 px-4 py-1.5 text-sm font-semibold text-emerald-300">
                            {{ number_format($pagination['total']) }} Total Assessments
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($loading)
        {{-- Skeleton: Controls Bar --}}
        <div class="sticky top-[68px] z-10 bg-stone-50/90 backdrop-blur-xl border-b border-gray-200 py-4 mb-8 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3 animate-pulse">
                    <div class="h-10 w-32 bg-gray-200 rounded-xl"></div>
                    <div class="h-10 w-36 bg-gray-200 rounded-xl"></div>
                </div>
                <div class="flex items-center gap-3 animate-pulse">
                    <div class="h-10 w-40 bg-gray-200 rounded-xl"></div>
                    <div class="h-10 w-48 bg-gray-200 rounded-xl"></div>
                </div>
            </div>
        </div>

        {{-- Skeleton: Table rows --}}
        <div class="bg-white shadow-sm ring-1 ring-gray-100 rounded-2xl overflow-hidden">
            <div class="divide-y divide-gray-50">
                {{-- Skeleton header --}}
                <div class="bg-gray-50/80 px-6 py-4 flex gap-6 animate-pulse">
                    <div class="h-3 w-12 bg-gray-200 rounded"></div>
                    <div class="h-3 w-32 bg-gray-200 rounded"></div>
                    <div class="h-3 w-24 bg-gray-200 rounded"></div>
                    <div class="h-3 w-20 bg-gray-200 rounded"></div>
                    <div class="h-3 w-12 bg-gray-200 rounded"></div>
                </div>
                @for($i = 0; $i < 8; $i++)
                    <div class="px-6 py-4 flex items-center gap-6 animate-pulse">
                        <div class="h-4 w-12 bg-gray-200 rounded"></div>
                        <div class="h-4 w-40 bg-gray-200 rounded"></div>
                        <div class="h-4 w-24 bg-gray-100 rounded"></div>
                        <div class="h-6 w-16 bg-gray-200 rounded-lg"></div>
                        <div class="h-4 w-8 bg-gray-100 rounded"></div>
                        <div class="h-6 w-6 bg-gray-200 rounded-full ml-auto"></div>
                    </div>
                @endfor
            </div>
        </div>
    @else
        {{-- Toggle Controls Bar (Sticky) --}}
        <div class="sticky top-[68px] z-10 bg-stone-50/90 backdrop-blur-xl border-b border-gray-200 py-4 mb-8 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

                {{-- View & Scroll Toggles --}}
                <div class="flex items-center gap-3">
                    <div class="flex rounded-xl shadow-sm overflow-hidden" role="group">
                        <button wire:click="toggleViewMode" type="button" class="relative inline-flex items-center px-4 py-2.5 text-sm font-semibold transition-all duration-300 {{ $viewMode === 'list' ? 'text-emerald-700 bg-emerald-50 ring-1 ring-inset ring-emerald-300' : 'text-gray-600 bg-white ring-1 ring-inset ring-gray-200 hover:bg-gray-50' }} rounded-l-xl focus:z-10">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            List
                        </button>
                        <button wire:click="toggleViewMode" type="button" class="relative -ml-px inline-flex items-center px-4 py-2.5 text-sm font-semibold transition-all duration-300 {{ $viewMode === 'card' ? 'text-emerald-700 bg-emerald-50 ring-1 ring-inset ring-emerald-300' : 'text-gray-600 bg-white ring-1 ring-inset ring-gray-200 hover:bg-gray-50' }} rounded-r-xl focus:z-10">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Card
                        </button>
                    </div>

                    <div class="flex rounded-xl shadow-sm overflow-hidden" role="group">
                        <button wire:click="toggleScrollMode" type="button" class="relative inline-flex items-center px-4 py-2.5 text-sm font-semibold transition-all duration-300 {{ $scrollMode === 'paginate' ? 'text-amber-700 bg-amber-50 ring-1 ring-inset ring-amber-300' : 'text-gray-600 bg-white ring-1 ring-inset ring-gray-200 hover:bg-gray-50' }} rounded-l-xl focus:z-10">
                            Pages
                        </button>
                        <button wire:click="toggleScrollMode" type="button" class="relative -ml-px inline-flex items-center px-4 py-2.5 text-sm font-semibold transition-all duration-300 {{ $scrollMode === 'scroll' ? 'text-amber-700 bg-amber-50 ring-1 ring-inset ring-amber-300' : 'text-gray-600 bg-white ring-1 ring-inset ring-gray-200 hover:bg-gray-50' }} rounded-r-xl focus:z-10">
                            Infinite
                        </button>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <select wire:model.live="yearFilter" class="block w-full md:w-40 rounded-xl border border-gray-200 bg-white py-2.5 pl-3 pr-10 text-sm text-gray-900 shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 focus:outline-none transition-all duration-300">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="extinctFilter" class="block w-full md:w-48 rounded-xl border border-gray-200 bg-white py-2.5 pl-3 pr-10 text-sm text-gray-900 shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 focus:outline-none transition-all duration-300">
                        <option value="">Extinction: All</option>
                        <option value="yes">Possibly Extinct</option>
                        <option value="no">Not Extinct</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="relative min-h-[50vh]">
            <div wire:loading class="absolute inset-0 bg-stone-50/60 backdrop-blur-sm z-20 flex items-center justify-center rounded-2xl">
                <div class="flex flex-col items-center gap-3">
                    <div class="animate-spin rounded-full h-10 w-10 border-2 border-emerald-200 border-t-emerald-600"></div>
                    <span class="text-sm text-gray-500 font-medium">Loading...</span>
                </div>
            </div>

            @if(count($filteredAssessments) === 0)
                <div class="text-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No assessments found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ count($assessments) > 0 ? "Try adjusting your filters to see more results." : "We couldn't find any assessments for this " . $type . "." }}
                    </p>
                    @if($yearFilter !== '' || $extinctFilter !== '')
                        <div class="mt-6">
                            <button type="button" wire:click="$set('yearFilter', ''); $set('extinctFilter', '');" class="inline-flex items-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors duration-300">
                                Clear Filters
                            </button>
                        </div>
                    @endif
                </div>
            @else
                @if($viewMode === 'list')
                    {{-- List View --}}
                    <div class="bg-white shadow-sm ring-1 ring-gray-100 rounded-2xl overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Year</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Species</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Assessment ID</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Flags</th>
                                    <th scope="col" class="px-3 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                        <svg class="w-4 h-4 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 7.875c-.278-2.908-2.754-5.125-5.752-5.125A5.5 5.5 0 0 0 12 5.052 5.5 5.5 0 0 0 7.688 2.75C4.714 2.75 2.25 5.072 2.25 8c0 3.925 2.438 7.111 4.739 9.256a25.175 25.175 0 0 0 4.244 3.17c.138.082.27.157.383.218l.022.012.007.004.003.001a.752.752 0 0 0 .704 0l.003-.001.007-.004.022-.012a15.247 15.247 0 0 0 .383-.218 25.18 25.18 0 0 0 4.244-3.17C19.312 15.111 21.75 11.925 21.75 8c0-.042 0-.083-.002-.125Z" /></svg>
                                    </th>
                                    <th scope="col" class="relative py-4 pl-3 pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @foreach($filteredAssessments as $a)
                                    <tr class="hover:bg-emerald-50/40 transition-colors duration-200 group">
                                        <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-gray-500 font-mono">
                                            {{ $a['year_published'] ?? 'N/A' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm italic font-semibold text-gray-900">
                                            {{ $a['taxon_scientific_name'] ?? 'Unknown' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <a href="/assessment/{{ $a['assessment_id'] }}" wire:navigate class="text-emerald-600 hover:text-emerald-800 hover:underline font-medium transition-colors">
                                                {{ $a['assessment_id'] }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            @php $cat = $a['red_list_category_code'] ?? ''; @endphp
                                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold shadow-sm {{ $getCategoryColor($cat) }}">
                                                {{ $cat }} - {{ IucnApiService::translateCategory($cat) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            @if(!empty($a['possibly_extinct']) || !empty($a['possibly_extinct_in_the_wild']))
                                                <span class="inline-flex items-center text-red-600 font-semibold" title="Possibly Extinct">
                                                    <span class="mr-1 text-lg">💀</span> PE
                                                </span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-center">
                                            <livewire:favorite-toggle
                                                :taxon-id="$a['sis_taxon_id']"
                                                :scientific-name="$a['taxon_scientific_name'] ?? 'Unknown'"
                                                :compact="true"
                                                :wire:key="'fav-list-'.$a['sis_taxon_id'].'-'.$a['assessment_id']"
                                            />
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                            <a href="{{ $a['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-emerald-600 transition-colors opacity-0 group-hover:opacity-100 flex items-center justify-end">
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
                            @php $cat = $a['red_list_category_code'] ?? ''; @endphp
                            <div class="group flex flex-col justify-between bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-lg ring-1 ring-gray-100 hover:ring-emerald-200 overflow-hidden transition-all duration-300 hover:-translate-y-1">
                                {{-- Top gradient --}}
                                <div class="h-1 bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400"></div>

                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold uppercase tracking-wider shadow-sm {{ $getCategoryColor($cat) }}">
                                            {{ $cat }}
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg">
                                                {{ $a['year_published'] ?? 'N/A' }}
                                            </span>
                                            <livewire:favorite-toggle
                                                :taxon-id="$a['sis_taxon_id']"
                                                :scientific-name="$a['taxon_scientific_name'] ?? 'Unknown'"
                                                :compact="true"
                                                :wire:key="'fav-card-'.$a['sis_taxon_id'].'-'.$a['assessment_id']"
                                            />
                                        </div>
                                    </div>

                                    <h3 class="text-lg font-semibold italic text-gray-900 mb-1 group-hover:text-emerald-800 transition-colors duration-300">
                                        {{ $a['taxon_scientific_name'] ?? 'Unknown' }}
                                    </h3>
                                    <p class="text-sm font-mono text-emerald-600 mb-2">
                                        <a href="/assessment/{{ $a['assessment_id'] }}" wire:navigate class="hover:underline">
                                            ID: {{ $a['assessment_id'] }}
                                        </a>
                                    </p>

                                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                                        {{ IucnApiService::translateCategory($cat) }}
                                    </p>

                                    @if(!empty($a['possibly_extinct']) || !empty($a['possibly_extinct_in_the_wild']))
                                        <div class="inline-flex items-center rounded-lg bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-200">
                                            <span class="mr-1">💀</span> Possibly Extinct
                                        </div>
                                    @endif
                                </div>

                                <div class="bg-gray-50/80 px-6 py-3 border-t border-gray-100 flex justify-between items-center mt-auto">
                                    <span class="text-xs text-gray-400 font-mono" title="SIS Taxon ID">SIS: {{ $a['sis_taxon_id'] ?? 'N/A' }}</span>
                                    <a href="{{ $a['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-emerald-600 hover:text-emerald-800 inline-flex items-center transition-colors">
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
                        <div class="flex items-center justify-between bg-white px-6 py-4 rounded-2xl shadow-sm ring-1 ring-gray-100">
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Showing page <span class="font-bold text-gray-900">{{ $page }}</span> of <span class="font-bold text-gray-900">{{ $totalPages }}</span>
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-xl shadow-sm overflow-hidden" aria-label="Pagination">
                                        <button wire:click="previousPage" @disabled($page <= 1) class="relative inline-flex items-center px-3 py-2 text-gray-500 bg-white ring-1 ring-inset ring-gray-200 hover:bg-gray-50 focus:z-20 disabled:opacity-40 disabled:cursor-not-allowed transition-colors rounded-l-xl">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <span class="relative inline-flex items-center px-5 py-2 text-sm font-bold text-emerald-800 ring-1 ring-inset ring-gray-200 bg-emerald-50">
                                            {{ $page }} / {{ $totalPages }}
                                        </span>

                                        <button wire:click="nextPage" @disabled(!$hasMore) class="relative inline-flex items-center px-3 py-2 text-gray-500 bg-white ring-1 ring-inset ring-gray-200 hover:bg-gray-50 focus:z-20 disabled:opacity-40 disabled:cursor-not-allowed transition-colors rounded-r-xl">
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
                                <button wire:click="previousPage" @disabled($page <= 1) class="relative inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 transition-colors">Previous</button>
                                <button wire:click="nextPage" @disabled(!$hasMore) class="relative ml-3 inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 transition-colors">Next</button>
                            </div>
                        </div>
                    @else
                        {{-- Infinite Scroll Load More Button --}}
                        <div class="text-center py-8">
                            @if($hasMore)
                                <button wire:click="loadMore" class="inline-flex items-center px-8 py-3.5 border-0 text-base font-semibold rounded-2xl shadow-md text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 active:scale-95 hover:shadow-lg group">
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
                                <div class="inline-flex items-center px-5 py-2.5 rounded-full bg-gray-100 text-sm font-medium text-gray-500">
                                    <span class="mr-2">🏁</span> You've reached the end of the list
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
