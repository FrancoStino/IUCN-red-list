<div>
    @if(empty($taxon))
        {{-- Empty State --}}
        <div class="rounded-3xl border-2 border-dashed border-gray-200 p-16 text-center bg-white">
            <div class="text-6xl mb-5">🔍</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Species not found</h2>
            <p class="text-gray-500 mb-8">We couldn't find a species with SIS ID <span class="font-mono font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded-lg">{{ $sisId }}</span></p>
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

        {{-- Header Section --}}
        <div class="relative bg-emerald-950 rounded-3xl shadow-xl overflow-hidden p-8 sm:p-10 mb-8">
            {{-- Decorative --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-emerald-400 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-56 h-56 rounded-full bg-teal-400 blur-3xl"></div>
            </div>

            <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
                <div class="flex-1 min-w-0">
                    {{-- Scientific Name --}}
                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white italic">
                        {{ $taxon['scientific_name'] ?? 'Unknown Species' }}
                    </h1>

                    {{-- Taxonomy Info --}}
                    @php
                        $taxonomyFields = [
                            'kingdom' => $taxon['kingdom_name'] ?? null,
                            'phylum' => $taxon['phylum_name'] ?? null,
                            'class' => $taxon['class_name'] ?? null,
                            'order' => $taxon['order_name'] ?? null,
                            'family' => $taxon['family_name'] ?? null,
                        ];
                        $taxonomyFields = array_filter($taxonomyFields);
                    @endphp

                    @if(count($taxonomyFields) > 0)
                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            @foreach($taxonomyFields as $rank => $value)
                                <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-900/50 border border-emerald-700/40 px-3 py-1.5 text-xs font-medium">
                                    <span class="text-emerald-400/70 uppercase tracking-wider">{{ $rank }}</span>
                                    <span class="text-emerald-100">{{ $value }}</span>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- SIS ID Badge --}}
                    <div class="mt-4">
                        <span class="inline-flex items-center rounded-full bg-emerald-800/50 border border-emerald-600/40 px-4 py-1.5 text-xs font-mono font-bold text-emerald-300">
                            SIS {{ $sisId }}
                        </span>
                    </div>
                </div>

                {{-- Favorite Toggle --}}
                <div class="flex-shrink-0">
                    <livewire:favorite-toggle :taxon-id="$sisId" :scientific-name="$taxon['scientific_name'] ?? 'Unknown'" />
                </div>
            </div>
        </div>

        {{-- Common Names Section --}}
        @php
            $commonNames = $taxon['common_names'] ?? [];
        @endphp

        @if(count($commonNames) > 0)
            <section class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-amber-100 text-lg">💬</span>
                    <h2 class="text-xl font-bold text-gray-900">Common Names</h2>
                    <span class="inline-flex items-center rounded-full bg-gray-100 border border-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                        {{ count($commonNames) }}
                    </span>
                </div>

                {{-- Main common name highlight --}}
                @php
                    $mainName = collect($commonNames)->firstWhere('main', true);
                @endphp

                @if($mainName)
                    <div class="mb-5 rounded-2xl bg-amber-50 border border-amber-200 p-5">
                        <div class="flex items-center gap-3">
                            <span class="text-amber-500 text-xl">★</span>
                            <span class="text-lg font-bold text-amber-900">{{ $mainName['name'] ?? '' }}</span>
                            @if(!empty($mainName['language']))
                                <span class="ml-auto text-xs font-semibold text-amber-600 bg-amber-100 rounded-full px-3 py-1 border border-amber-200">
                                    {{ $mainName['language'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- All common names as tags --}}
                <div class="flex flex-wrap gap-2">
                    @foreach($commonNames as $cn)
                        @if(empty($cn['main']) || !$cn['main'])
                            <span class="inline-flex items-center gap-1.5 rounded-xl bg-gray-50 border border-gray-200 px-3 py-2 text-sm hover:bg-gray-100 transition-colors duration-200">
                                <span class="font-medium text-gray-800">{{ $cn['name'] ?? '' }}</span>
                                @if(!empty($cn['language']))
                                    <span class="text-xs text-gray-400">({{ $cn['language'] }})</span>
                                @endif
                            </span>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Assessments Section --}}
        @php
            $assessments = $assessments ?? [];
        @endphp

        <section class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 text-lg">📊</span>
                <h2 class="text-xl font-bold text-gray-900">Assessments</h2>
                @if(count($assessments) > 0)
                    <span class="inline-flex items-center rounded-full bg-gray-100 border border-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                        {{ count($assessments) }}
                    </span>
                @endif
            </div>

            @if(count($assessments) === 0)
                <div class="rounded-2xl border-2 border-dashed border-gray-200 p-10 text-center text-gray-400">
                    No assessments available for this species.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($assessments as $a)
                        @php
                            $cat = $a['red_list_category_code'] ?? 'NE';
                            $catName = \App\Services\IucnApiService::translateCategory($cat);
                            $aId = $a['assessment_id'] ?? null;
                            $year = $a['year_published'] ?? 'N/A';

                            // Category color mapping
                            $catColors = match($cat) {
                                'EX' => ['bg' => 'bg-black', 'text' => 'text-white', 'border' => 'border-black'],
                                'EW' => ['bg' => 'bg-gray-800', 'text' => 'text-white', 'border' => 'border-gray-800'],
                                'CR' => ['bg' => 'bg-red-600', 'text' => 'text-white', 'border' => 'border-red-600'],
                                'EN' => ['bg' => 'bg-orange-500', 'text' => 'text-white', 'border' => 'border-orange-500'],
                                'VU' => ['bg' => 'bg-yellow-500', 'text' => 'text-white', 'border' => 'border-yellow-500'],
                                'NT' => ['bg' => 'bg-lime-500', 'text' => 'text-white', 'border' => 'border-lime-500'],
                                'LC' => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'border' => 'border-emerald-500'],
                                'DD' => ['bg' => 'bg-gray-400', 'text' => 'text-white', 'border' => 'border-gray-400'],
                                default => ['bg' => 'bg-gray-200', 'text' => 'text-gray-700', 'border' => 'border-gray-300'],
                            };
                        @endphp

                        @if($aId)
                            <a
                                href="/assessment/{{ $aId }}"
                                wire:navigate
                                class="group flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 hover:shadow-lg hover:border-emerald-200 hover:-translate-y-0.5 transition-all duration-300"
                            >
                                {{-- Category Badge --}}
                                <span class="flex-shrink-0 inline-flex items-center justify-center w-14 h-14 rounded-2xl {{ $catColors['bg'] }} {{ $catColors['text'] }} text-sm font-bold shadow-md">
                                    {{ $cat }}
                                </span>

                                {{-- Assessment Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 group-hover:text-emerald-700 transition-colors duration-300">
                                        {{ $catName }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        Published {{ $year }} · Assessment #{{ $aId }}
                                    </p>
                                </div>

                                {{-- Arrow --}}
                                <svg class="flex-shrink-0 w-5 h-5 text-gray-300 group-hover:text-emerald-500 transform group-hover:translate-x-1 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</div>
