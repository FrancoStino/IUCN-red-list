<div wire:init="loadData">
    {{-- Hero Section --}}
    <div class="relative mb-14 rounded-3xl bg-emerald-950 overflow-hidden px-8 py-14 sm:px-12 sm:py-16">
        {{-- Decorative elements --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-emerald-400 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-teal-400 blur-3xl"></div>
        </div>

        <div class="relative">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-800/60 text-emerald-300 text-xs font-semibold tracking-wider uppercase mb-5">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                IUCN Red List API v4
            </div>
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Explore <span class="text-emerald-400">Biodiversity</span>
            </h1>
            <p class="mt-4 text-lg text-emerald-200/70 max-w-2xl leading-relaxed">
                Navigate the IUCN Red List by ecological system or country. Discover species assessments and conservation status worldwide.
            </p>
        </div>
    </div>

    @if($loading)
        {{-- Skeleton: Systems --}}
        <section class="mb-16">
            <div class="flex items-center gap-3 mb-8">
                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-xl">🌐</span>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Ecological Systems</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @for($i = 0; $i < 3; $i++)
                    <div class="rounded-2xl bg-white/80 shadow-sm border border-gray-100 overflow-hidden animate-pulse">
                        <div class="h-1.5 bg-gray-200"></div>
                        <div class="p-7">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gray-200"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 rounded-lg w-3/4"></div>
                                    <div class="h-4 bg-gray-100 rounded-lg w-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </section>

        {{-- Skeleton: Countries --}}
        <section>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 text-xl">🗺️</span>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Countries</h2>
                    <span class="inline-flex items-center rounded-full bg-gray-100 border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-400">
                        Loading...
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @for($i = 0; $i < 12; $i++)
                    <div class="flex items-center gap-3.5 rounded-2xl bg-white border border-gray-100 p-4 shadow-sm animate-pulse">
                        <div class="flex-shrink-0 flex items-center gap-2.5">
                            <div class="w-8 h-6 rounded bg-gray-200"></div>
                            <div class="w-8 h-4 rounded bg-gray-100"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="h-4 bg-gray-200 rounded-lg w-3/4"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </section>
    @else
        {{-- Systems Section --}}
        <section class="mb-16">
            <div class="flex items-center gap-3 mb-8">
                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-xl">🌐</span>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Ecological Systems</h2>
            </div>

            @if(count($systems) === 0)
                <div class="rounded-2xl border-2 border-dashed border-gray-300 p-10 text-center text-gray-400">
                    No systems available at the moment.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($systems as $system)
                        @php
                            $name = $system['name'] ?? 'Unknown';
                            $code = $system['code'] ?? '';
                            $lower = strtolower($name);

                            // Map system names to visual treatments
                            if (str_contains($lower, 'terrestrial')) {
                                $icon = '🌿';
                                $gradient = 'from-emerald-500 to-green-700';
                                $border = 'border-emerald-500';
                                $bg = 'bg-emerald-50';
                                $text = 'text-emerald-700';
                                $description = 'Land-based ecosystems and habitats';
                            } elseif (str_contains($lower, 'freshwater')) {
                                $icon = '💧';
                                $gradient = 'from-cyan-500 to-blue-600';
                                $border = 'border-cyan-500';
                                $bg = 'bg-cyan-50';
                                $text = 'text-cyan-700';
                                $description = 'Rivers, lakes, and wetland ecosystems';
                            } elseif (str_contains($lower, 'marine')) {
                                $icon = '🌊';
                                $gradient = 'from-blue-500 to-indigo-700';
                                $border = 'border-blue-500';
                                $bg = 'bg-blue-50';
                                $text = 'text-blue-700';
                                $description = 'Ocean and coastal ecosystems';
                            } else {
                                $icon = '🔬';
                                $gradient = 'from-violet-500 to-purple-700';
                                $border = 'border-violet-500';
                                $bg = 'bg-violet-50';
                                $text = 'text-violet-700';
                                $description = 'Explore species assessments';
                            }
                        @endphp

                        <a href="/assessments/system/{{ $code }}"
                           wire:navigate
                           class="group relative block rounded-2xl bg-white/80 backdrop-blur-sm shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1.5 overflow-hidden border border-gray-100 hover:border-gray-200">
                            {{-- Gradient accent bar at top --}}
                            <div class="h-1.5 bg-gradient-to-r {{ $gradient }}"></div>

                            <div class="p-7">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl {{ $bg }} flex items-center justify-center text-2xl shadow-sm group-hover:scale-110 transition-transform duration-500">
                                        {{ $icon }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 group-hover:{{ $text }} transition-colors duration-300">
                                            {{ $name }}
                                        </h3>
                                        <p class="mt-1.5 text-sm text-gray-500 leading-relaxed">{{ $description }}</p>
                                    </div>
                                </div>

                                <div class="mt-5 flex items-center {{ $text }} text-sm font-semibold opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 transition-all duration-500">
                                    <span>View assessments</span>
                                    <svg class="ml-2 w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Countries Section --}}
        <section>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 text-xl">🗺️</span>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Countries</h2>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ count($filteredCountries) }} / {{ count($countries) }}
                    </span>
                </div>

                {{-- Search Input --}}
                <div class="relative max-w-sm w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live="search"
                        placeholder="Search countries..."
                        class="block w-full rounded-2xl border border-gray-200 bg-white py-3 pl-11 pr-10 text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 focus:outline-none transition-all duration-300"
                    />
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            @if(count($filteredCountries) === 0)
                <div class="rounded-2xl border-2 border-dashed border-gray-300 p-14 text-center">
                    @if($search)
                        <p class="text-gray-400 text-lg">No countries matching "<span class="font-semibold text-gray-600">{{ $search }}</span>"</p>
                        <button wire:click="$set('search', '')" class="mt-4 text-sm text-emerald-600 hover:text-emerald-700 font-semibold underline underline-offset-2">
                            Clear search
                        </button>
                    @else
                        <p class="text-gray-400 text-lg">No countries available at the moment.</p>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @php
                    $countryFlag = function(string $code): string {
                        $code = strtoupper($code);
                        if (strlen($code) !== 2) return '🏳️';
                        return mb_chr(0x1F1E6 + ord($code[0]) - ord('A'))
                             . mb_chr(0x1F1E6 + ord($code[1]) - ord('A'));
                    };
                    @endphp
                    @foreach($filteredCountries as $country)
                        <a href="/assessments/country/{{ $country['code'] ?? '' }}"
                           wire:navigate
                           class="group flex items-center gap-3.5 rounded-2xl bg-white border border-gray-100 p-4 shadow-sm hover:shadow-lg hover:border-emerald-200 transition-all duration-300 hover:-translate-y-0.5">
                            {{-- Country flag + code --}}
                            <div class="flex-shrink-0 flex items-center gap-2.5">
                                <span class="text-2xl leading-none" title="{{ strtoupper($country['code'] ?? '') }}">{{ $countryFlag($country['code'] ?? '') }}</span>
                                <span class="text-xs font-bold tracking-wider text-gray-400 font-mono">{{ strtoupper($country['code'] ?? '??') }}</span>
                            </div>

                            {{-- Country name --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-emerald-700 transition-colors duration-300">
                                    {{ $country['name'] ?? 'Unknown' }}
                                </p>
                            </div>

                            {{-- Arrow icon --}}
                            <svg class="flex-shrink-0 w-4 h-4 text-gray-300 group-hover:text-emerald-500 transform group-hover:translate-x-0.5 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</div>
