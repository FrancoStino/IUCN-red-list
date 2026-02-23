<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Header --}}
        <div class="mb-10">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">⭐ My Favorites</h1>
                    <p class="mt-2 text-gray-500">Your personal collection of species to watch and track.</p>
                </div>
                @if($favorites->count() > 0)
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-full border border-emerald-200 shrink-0">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        {{ $favorites->count() }} {{ Str::plural('species', $favorites->count()) }} saved
                    </span>
                @endif
            </div>
        </div>

        @if($favorites->count() > 0)
            {{-- Favorites Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($favorites as $favorite)
                    <div
                        wire:key="fav-{{ $favorite->taxon_id }}"
                        class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 overflow-hidden"
                    >
                        {{-- Card Top Accent --}}
                        <div class="h-1 bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-500"></div>

                        <div class="p-6">
                            {{-- Species Name --}}
                            <div class="mb-4">
                                <a
                                    href="/species/{{ $favorite->taxon_id }}"
                                    wire:navigate
                                    class="text-lg font-semibold italic text-emerald-700 hover:text-emerald-900 transition-colors leading-snug"
                                >
                                    {{ $favorite->scientific_name }}
                                </a>
                            </div>

                            {{-- Meta Row --}}
                            <div class="flex flex-wrap items-center gap-2 mb-5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-50 text-gray-500 text-xs font-mono rounded-lg border border-gray-200">
                                    ID {{ $favorite->taxon_id }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-50 text-gray-400 text-xs rounded-lg border border-gray-200">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    {{ $favorite->added_at->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <a
                                    href="/species/{{ $favorite->taxon_id }}"
                                    wire:navigate
                                    class="inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-800 font-medium transition-colors"
                                >
                                    View details
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                </a>
                                <button
                                    wire:click="removeFavorite({{ $favorite->taxon_id }})"
                                    wire:confirm="Remove {{ $favorite->scientific_name }} from favorites?"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-100 hover:border-red-200"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="max-w-sm mx-auto">
                    <div class="w-20 h-20 mx-auto mb-6 bg-emerald-50 rounded-2xl flex items-center justify-center border border-emerald-100">
                        <span class="text-4xl">🌿</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Start your collection</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed">
                        Discover endangered and threatened species across the globe. Save the ones you care about to track their conservation status.
                    </p>
                    <a
                        href="/"
                        wire:navigate
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 shadow-sm hover:shadow-md transition-all duration-300"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        Explore Species
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
