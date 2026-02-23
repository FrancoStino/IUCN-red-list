<div>
    @if(empty($assessment))
        {{-- Empty State --}}
        <div class="rounded-3xl border-2 border-dashed border-gray-200 p-16 text-center bg-white">
            <div class="text-6xl mb-5">📋</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Assessment not found</h2>
            <p class="text-gray-500 mb-8">We couldn't find an assessment with ID <span class="font-mono font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded-lg">{{ $assessmentId }}</span></p>
            <a
                href="/"
                wire:navigate
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-2xl hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-md hover:shadow-lg"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    @else
        @php
            $cat = $assessment['red_list_category']['code'] ?? 'NE';
            $catName = $assessment['red_list_category']['description']['en'] ?? \App\Services\IucnApiService::translateCategory($cat);
            $sisId = $assessment['sis_taxon_id'] ?? null;
            $year = $assessment['year_published'] ?? 'N/A';
            $criteria = $assessment['criteria'] ?? null;
            $popTrend = $assessment['population_trend']['description']['en'] ?? null;
            $possiblyExtinct = $assessment['possibly_extinct'] ?? false;
            $possiblyExtinctWild = $assessment['possibly_extinct_in_the_wild'] ?? false;
            $conservationActions = $assessment['conservation_actions'] ?? [];
            $documentation = $assessment['documentation'] ?? [];

            $catColors = match($cat) {
                'EX' => ['bg' => 'bg-black', 'text' => 'text-white', 'ring' => 'ring-black/20', 'light_bg' => 'bg-gray-100', 'light_text' => 'text-gray-900'],
                'EW' => ['bg' => 'bg-gray-800', 'text' => 'text-white', 'ring' => 'ring-gray-800/20', 'light_bg' => 'bg-gray-100', 'light_text' => 'text-gray-800'],
                'CR' => ['bg' => 'bg-red-600', 'text' => 'text-white', 'ring' => 'ring-red-600/20', 'light_bg' => 'bg-red-50', 'light_text' => 'text-red-800'],
                'EN' => ['bg' => 'bg-orange-500', 'text' => 'text-white', 'ring' => 'ring-orange-500/20', 'light_bg' => 'bg-orange-50', 'light_text' => 'text-orange-800'],
                'VU' => ['bg' => 'bg-yellow-500', 'text' => 'text-white', 'ring' => 'ring-yellow-500/20', 'light_bg' => 'bg-yellow-50', 'light_text' => 'text-yellow-800'],
                'NT' => ['bg' => 'bg-lime-500', 'text' => 'text-white', 'ring' => 'ring-lime-500/20', 'light_bg' => 'bg-lime-50', 'light_text' => 'text-lime-800'],
                'LC' => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'ring' => 'ring-emerald-500/20', 'light_bg' => 'bg-emerald-50', 'light_text' => 'text-emerald-800'],
                'DD' => ['bg' => 'bg-gray-400', 'text' => 'text-white', 'ring' => 'ring-gray-400/20', 'light_bg' => 'bg-gray-50', 'light_text' => 'text-gray-700'],
                default => ['bg' => 'bg-gray-200', 'text' => 'text-gray-700', 'ring' => 'ring-gray-300/20', 'light_bg' => 'bg-gray-50', 'light_text' => 'text-gray-600'],
            };

            $trendDisplay = match($popTrend) {
                'Increasing' => ['icon' => '↑', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'label' => 'Increasing'],
                'Decreasing' => ['icon' => '↓', 'color' => 'text-red-600', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'label' => 'Decreasing'],
                'Stable' => ['icon' => '→', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'label' => 'Stable'],
                default => ['icon' => '?', 'color' => 'text-gray-500', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'label' => $popTrend ?? 'Unknown'],
            };
        @endphp

        {{-- Back Link --}}
        <div class="mb-6">
            <a
                href="javascript:history.back()"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700 hover:text-emerald-900 transition-colors group"
            >
                <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>

        {{-- Header --}}
        <div class="relative bg-emerald-950 rounded-3xl shadow-xl overflow-hidden p-8 sm:p-10 mb-8">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-emerald-400 blur-3xl"></div>
            </div>

            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div>
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <span class="inline-flex items-center rounded-full bg-emerald-900/50 border border-emerald-700/40 px-4 py-1.5 text-xs font-mono font-bold text-emerald-300">
                            Assessment #{{ $assessmentId }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-amber-900/40 border border-amber-600/30 px-3 py-1.5 text-xs font-bold text-amber-300">
                            {{ $year }}
                        </span>
                    </div>

                    @if($sisId)
                        <a
                            href="/species/{{ $sisId }}"
                            wire:navigate
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-400 hover:text-emerald-200 transition-colors group"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            View Species (SIS {{ $sisId }})
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                </div>

                {{-- External Link --}}
                <a
                    href="{{ $assessment['url'] ?? 'https://www.iucnredlist.org/species/' . ($sisId ?? '') . '/' . $assessmentId }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-800/50 border border-emerald-600/40 rounded-2xl hover:bg-emerald-700/50 transition-all duration-300"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    IUCN Red List
                </a>
            </div>
        </div>

        {{-- Conservation Status Section --}}
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 text-lg">🛡️</span>
                <h2 class="text-xl font-bold text-gray-900">Conservation Status</h2>
            </div>

            <div class="flex flex-col sm:flex-row items-start gap-6">
                {{-- Category Badge (Large) --}}
                <div class="flex-shrink-0 flex flex-col items-center">
                    <span class="inline-flex items-center justify-center w-24 h-24 rounded-3xl {{ $catColors['bg'] }} {{ $catColors['text'] }} text-3xl font-extrabold shadow-xl ring-4 {{ $catColors['ring'] }}">
                        {{ $cat }}
                    </span>
                </div>

                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $catName }}</h3>

                    @if($criteria)
                        <p class="mt-3 text-sm text-gray-600">
                            <span class="font-semibold text-gray-700">Criteria:</span>
                            <span class="font-mono bg-gray-100 px-2 py-0.5 rounded-lg">{{ $criteria }}</span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Extinction Risk & Population Trend --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- Extinction Risk --}}
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-7">
                <div class="flex items-center gap-3 mb-5">
                    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-100 text-lg">⚠️</span>
                    <h3 class="text-lg font-bold text-gray-900">Extinction Risk</h3>
                </div>

                <div class="space-y-3">
                    {{-- Possibly Extinct --}}
                    <div class="flex items-center gap-3 rounded-2xl p-4 {{ $possiblyExtinct ? 'bg-red-50 border border-red-200' : 'bg-gray-50 border border-gray-200' }}">
                        @if($possiblyExtinct)
                            <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center text-sm font-bold shadow-md">!</span>
                            <span class="font-bold text-red-800">Possibly Extinct</span>
                        @else
                            <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-gray-200 text-gray-400 flex items-center justify-center text-sm font-bold">—</span>
                            <span class="text-gray-500">Not flagged as Possibly Extinct</span>
                        @endif
                    </div>

                    {{-- Possibly Extinct in the Wild --}}
                    <div class="flex items-center gap-3 rounded-2xl p-4 {{ $possiblyExtinctWild ? 'bg-orange-50 border border-orange-200' : 'bg-gray-50 border border-gray-200' }}">
                        @if($possiblyExtinctWild)
                            <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center text-sm font-bold shadow-md">!</span>
                            <span class="font-bold text-orange-800">Possibly Extinct in the Wild</span>
                        @else
                            <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-gray-200 text-gray-400 flex items-center justify-center text-sm font-bold">—</span>
                            <span class="text-gray-500">Not flagged as Possibly Extinct in the Wild</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Population Trend --}}
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-7">
                <div class="flex items-center gap-3 mb-5">
                    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-100 text-lg">📈</span>
                    <h3 class="text-lg font-bold text-gray-900">Population Trend</h3>
                </div>

                <div class="flex items-center gap-5 rounded-2xl p-5 {{ $trendDisplay['bg'] }} border {{ $trendDisplay['border'] }}">
                    <span class="flex-shrink-0 w-14 h-14 rounded-2xl {{ $trendDisplay['bg'] }} {{ $trendDisplay['color'] }} flex items-center justify-center text-3xl font-bold">
                        {{ $trendDisplay['icon'] }}
                    </span>
                    <div>
                        <p class="text-xl font-bold {{ $trendDisplay['color'] }}">{{ $trendDisplay['label'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Population trend</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Conservation Actions --}}
        @if(count($conservationActions) > 0)
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 text-lg">🌱</span>
                    <h2 class="text-xl font-bold text-gray-900">Conservation Actions</h2>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                        {{ count($conservationActions) }}
                    </span>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($conservationActions as $action)
                        @php
                            $actionName = is_array($action) ? ($action['description']['en'] ?? $action['code'] ?? 'Unknown') : (string) $action;
                        @endphp
                        <span class="inline-flex items-center rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm font-medium text-emerald-800 hover:bg-emerald-100 transition-colors duration-200">
                            {{ $actionName }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Systems --}}
        @php $systems = $assessment['systems'] ?? []; @endphp
        @if(count($systems) > 0)
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-100 text-lg">🌍</span>
                    <h2 class="text-xl font-bold text-gray-900">Systems</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($systems as $system)
                        <a href="/assessments/system/{{ $system['code'] ?? '' }}"
                           wire:navigate
                           class="inline-flex items-center rounded-xl bg-blue-50 border border-blue-200 px-4 py-2.5 text-sm font-medium text-blue-800 hover:bg-blue-100 hover:border-blue-300 transition-all duration-300 hover:-translate-y-0.5">
                            {{ $system['description']['en'] ?? $system['code'] ?? 'Unknown' }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Documentation --}}
        @php
            $docSections = is_array($documentation) ? $documentation : [];
            $sectionLabels = [
                'rationale' => 'Rationale',
                'range' => 'Geographic Range',
                'population' => 'Population',
                'habitats' => 'Habitat and Ecology',
                'threats' => 'Threats',
                'measures' => 'Conservation Measures',
                'use_trade' => 'Use and Trade',
                'trend_justification' => 'Population Trend Justification',
                'taxonomic_notes' => 'Taxonomic Notes',
            ];
            $hasDoc = collect($docSections)->filter()->isNotEmpty();
        @endphp

        @if($hasDoc)
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
                <div class="flex items-center gap-3 mb-8">
                    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 text-lg">📄</span>
                    <h2 class="text-xl font-bold text-gray-900">Documentation</h2>
                </div>

                <div class="space-y-10">
                    @foreach($sectionLabels as $key => $label)
                        @if(!empty($docSections[$key]))
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-2 border-b border-gray-100">
                                    {{ $label }}
                                </h3>
                                <div class="text-sm leading-relaxed text-gray-700
                                    [&_a]:text-emerald-700 [&_a]:underline [&_a]:underline-offset-2 [&_a]:font-medium [&_a:hover]:text-emerald-900
                                    [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-3
                                    [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:mb-3
                                    [&_li]:mb-1.5 [&_li]:text-gray-700
                                    [&_p]:text-gray-700 [&_p]:leading-relaxed [&_p]:mb-3
                                    [&_table]:w-full [&_table]:border-collapse [&_table]:text-sm [&_table]:rounded-xl [&_table]:overflow-hidden
                                    [&_th]:bg-gray-50 [&_th]:border [&_th]:border-gray-200 [&_th]:px-4 [&_th]:py-2.5 [&_th]:text-left [&_th]:font-semibold
                                    [&_td]:border [&_td]:border-gray-200 [&_td]:px-4 [&_td]:py-2.5
                                ">
                                    {!! $docSections[$key] !!}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
