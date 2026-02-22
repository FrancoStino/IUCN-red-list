<div>
    {{-- Hero Section --}}
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
            Explore Biodiversity
        </h1>
        <p class="mt-3 text-lg text-gray-500 max-w-2xl">
            Navigate the IUCN Red List by ecological system or country. Discover species assessments and conservation status worldwide.
        </p>
    </div>

    {{-- Systems Section --}}
    <section class="mb-14">
        <div class="flex items-center gap-3 mb-6">
            <span class="text-2xl">🌐</span>
            <h2 class="text-2xl font-bold text-gray-900">Systems</h2>
        </div>

        @if(count($systems) === 0)
            <div class="rounded-xl border-2 border-dashed border-gray-300 p-8 text-center text-gray-400">
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
                       class="group relative block rounded-2xl border-l-4 {{ $border }} bg-white shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                        {{-- Gradient accent bar at top --}}
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $gradient }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-14 h-14 rounded-xl {{ $bg }} flex items-center justify-center text-2xl shadow-sm">
                                    {{ $icon }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:{{ $text }} transition-colors duration-200">
                                        {{ $name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center {{ $text }} text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span>View assessments</span>
                                <svg class="ml-1.5 w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🗺️</span>
                <h2 class="text-2xl font-bold text-gray-900">Countries</h2>
                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                    {{ count($filteredCountries) }} / {{ count($countries) }}
                </span>
            </div>

            {{-- Search Input --}}
            <div class="relative max-w-sm w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Search countries..."
                    class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:outline-none transition-all duration-200"
                />
                @if($search)
                    <button
                        wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        @if(count($filteredCountries) === 0)
            <div class="rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                @if($search)
                    <p class="text-gray-400 text-lg">No countries matching "<span class="font-semibold text-gray-600">{{ $search }}</span>"</p>
                    <button wire:click="$set('search', '')" class="mt-3 text-sm text-red-600 hover:text-red-700 font-medium underline underline-offset-2">
                        Clear search
                    </button>
                @else
                    <p class="text-gray-400 text-lg">No countries available at the moment.</p>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($filteredCountries as $country)
                    <a href="/assessments/country/{{ $country['code'] ?? '' }}"
                       wire:navigate
                       class="group flex items-center gap-3.5 rounded-xl bg-white border border-gray-100 p-4 shadow-sm hover:shadow-lg hover:border-red-200 transition-all duration-300 hover:-translate-y-0.5">
                        {{-- Country code badge --}}
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-50 text-red-700 flex items-center justify-center text-xs font-bold tracking-wider group-hover:bg-red-600 group-hover:text-white transition-colors duration-300">
                            {{ strtoupper($country['code'] ?? '??') }}
                        </div>

                        {{-- Country name --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-red-700 transition-colors duration-200">
                                {{ $country['name'] ?? 'Unknown' }}
                            </p>
                        </div>

                        {{-- Arrow icon --}}
                        <svg class="flex-shrink-0 w-4 h-4 text-gray-300 group-hover:text-red-500 transform group-hover:translate-x-0.5 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>
