<div>
    @if(empty($assessment))
        {{-- Empty State --}}
        <div class="rounded-2xl border-2 border-dashed border-gray-300 p-16 text-center">
            <div class="text-6xl mb-4">📋</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Assessment not found</h2>
            <p class="text-gray-500 mb-6">We couldn't find an assessment with ID <span class="font-mono font-semibold text-gray-700">{{ $assessmentId }}</span></p>
            <a
                href="/"
                wire:navigate
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors shadow-sm"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    @else
        @php
            $cat = $assessment['category'] ?? 'NE';
            $catName = \App\Services\IucnApiService::translateCategory($cat);
            $sisId = $assessment['sis_id'] ?? null;
            $year = $assessment['year_published'] ?? 'N/A';
            $criteria = $assessment['criteria'] ?? null;
            $popTrend = $assessment['population_trend'] ?? null;
            $possiblyExtinct = $assessment['possibly_extinct'] ?? false;
            $possiblyExtinctWild = $assessment['possibly_extinct_in_the_wild'] ?? false;
            $conservationActions = $assessment['conservation_actions'] ?? [];
            $documentation = $assessment['documentation'] ?? '';

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
                class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors group"
            >
                <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>

        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <span class="inline-flex items-center rounded-full bg-gray-100 border border-gray-200 px-3 py-1 text-xs font-mono font-semibold text-gray-600">
                            Assessment #{{ $assessmentId }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-indigo-50 border border-indigo-200 px-3 py-1 text-xs font-semibold text-indigo-700">
                            {{ $year }}
                        </span>
                    </div>

                    @if($sisId)
                        <a
                            href="/species/{{ $sisId }}"
                            wire:navigate
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700 hover:text-emerald-900 transition-colors group"
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
                @if($sisId)
                    <a
                        href="https://www.iucnredlist.org/species/{{ $sisId }}/{{ $assessmentId }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 hover:text-gray-900 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        IUCN Red List
                    </a>
                @endif
            </div>
        </div>

        {{-- Conservation Status Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-xl">🛡️</span>
                <h2 class="text-xl font-bold text-gray-900">Conservation Status</h2>
            </div>

            <div class="flex flex-col sm:flex-row items-start gap-6">
                {{-- Category Badge (Large) --}}
                <div class="flex-shrink-0 flex flex-col items-center">
                    <span class="inline-flex items-center justify-center w-20 h-20 rounded-2xl {{ $catColors['bg'] }} {{ $catColors['text'] }} text-2xl font-extrabold shadow-lg ring-4 {{ $catColors['ring'] }}">
                        {{ $cat }}
                    </span>
                </div>

                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $catName }}</h3>

                    @if($criteria)
                        <p class="mt-2 text-sm text-gray-600">
                            <span class="font-semibold text-gray-700">Criteria:</span>
                            <span class="font-mono">{{ $criteria }}</span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Extinction Risk & Population Trend --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- Extinction Risk --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-lg">⚠️</span>
                    <h3 class="text-lg font-bold text-gray-900">Extinction Risk</h3>
                </div>

                <div class="space-y-3">
                    {{-- Possibly Extinct --}}
                    <div class="flex items-center gap-3 rounded-xl p-3 {{ $possiblyExtinct ? 'bg-red-50 border border-red-200' : 'bg-gray-50 border border-gray-200' }}">
                        @if($possiblyExtinct)
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-red-600 text-white flex items-center justify-center text-sm font-bold">!</span>
                            <span class="font-semibold text-red-800">Possibly Extinct</span>
                        @else
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-bold">—</span>
                            <span class="text-gray-500">Not flagged as Possibly Extinct</span>
                        @endif
                    </div>

                    {{-- Possibly Extinct in the Wild --}}
                    <div class="flex items-center gap-3 rounded-xl p-3 {{ $possiblyExtinctWild ? 'bg-orange-50 border border-orange-200' : 'bg-gray-50 border border-gray-200' }}">
                        @if($possiblyExtinctWild)
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center text-sm font-bold">!</span>
                            <span class="font-semibold text-orange-800">Possibly Extinct in the Wild</span>
                        @else
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-bold">—</span>
                            <span class="text-gray-500">Not flagged as Possibly Extinct in the Wild</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Population Trend --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-lg">📈</span>
                    <h3 class="text-lg font-bold text-gray-900">Population Trend</h3>
                </div>

                <div class="flex items-center gap-4 rounded-xl p-4 {{ $trendDisplay['bg'] }} border {{ $trendDisplay['border'] }}">
                    <span class="flex-shrink-0 w-12 h-12 rounded-xl {{ $trendDisplay['bg'] }} {{ $trendDisplay['color'] }} flex items-center justify-center text-3xl font-bold">
                        {{ $trendDisplay['icon'] }}
                    </span>
                    <div>
                        <p class="text-lg font-bold {{ $trendDisplay['color'] }}">{{ $trendDisplay['label'] }}</p>
                        <p class="text-xs text-gray-500">Population trend</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Conservation Actions --}}
        @if(count($conservationActions) > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-xl">🌱</span>
                    <h2 class="text-xl font-bold text-gray-900">Conservation Actions</h2>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                        {{ count($conservationActions) }}
                    </span>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($conservationActions as $action)
                        @php
                            $actionName = is_array($action) ? ($action['name'] ?? $action['code'] ?? json_encode($action)) : (string) $action;
                        @endphp
                        <span class="inline-flex items-center rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-2 text-sm font-medium text-emerald-800">
                            {{ $actionName }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Documentation --}}
        @if(!empty($documentation))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-xl">📄</span>
                    <h2 class="text-xl font-bold text-gray-900">Documentation</h2>
                </div>

                <div class="prose prose-sm max-w-none
                    [&>h1]:text-xl [&>h1]:font-bold [&>h1]:text-gray-900 [&>h1]:mt-6 [&>h1]:mb-3
                    [&>h2]:text-lg [&>h2]:font-bold [&>h2]:text-gray-900 [&>h2]:mt-5 [&>h2]:mb-2
                    [&>h3]:text-base [&>h3]:font-semibold [&>h3]:text-gray-800 [&>h3]:mt-4 [&>h3]:mb-2
                    [&>p]:text-gray-700 [&>p]:leading-relaxed [&>p]:mb-3
                    [&>ul]:list-disc [&>ul]:pl-5 [&>ul]:mb-3 [&>ul]:text-gray-700
                    [&>ol]:list-decimal [&>ol]:pl-5 [&>ol]:mb-3 [&>ol]:text-gray-700
                    [&>li]:mb-1
                    [&>a]:text-emerald-700 [&>a]:underline [&>a]:underline-offset-2
                    [&>table]:w-full [&>table]:border-collapse [&>table]:text-sm
                    [&>table_th]:bg-gray-50 [&>table_th]:border [&>table_th]:border-gray-200 [&>table_th]:px-3 [&>table_th]:py-2 [&>table_th]:text-left
                    [&>table_td]:border [&>table_td]:border-gray-200 [&>table_td]:px-3 [&>table_td]:py-2
                    [&_a]:text-emerald-700 [&_a]:underline [&_a]:underline-offset-2
                    [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-3
                    [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:mb-3
                    [&_li]:mb-1 [&_li]:text-gray-700
                    [&_p]:text-gray-700 [&_p]:leading-relaxed [&_p]:mb-3
                    [&_h1]:text-xl [&_h1]:font-bold [&_h1]:text-gray-900 [&_h1]:mt-6 [&_h1]:mb-3
                    [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-gray-900 [&_h2]:mt-5 [&_h2]:mb-2
                    [&_h3]:text-base [&_h3]:font-semibold [&_h3]:text-gray-800 [&_h3]:mt-4 [&_h3]:mb-2
                    [&_table]:w-full [&_table]:border-collapse [&_table]:text-sm
                    [&_th]:bg-gray-50 [&_th]:border [&_th]:border-gray-200 [&_th]:px-3 [&_th]:py-2 [&_th]:text-left [&_th]:font-semibold
                    [&_td]:border [&_td]:border-gray-200 [&_td]:px-3 [&_td]:py-2
                ">
                    {!! $documentation !!}
                </div>
            </div>
        @endif
    @endif
</div>
