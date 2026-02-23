<div class="min-h-screen bg-stone-50">
    {{-- Dark Header --}}
    <div class="relative bg-emerald-950 overflow-hidden">
        {{-- Decorative blurred circles --}}
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-emerald-800/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-teal-800/20 rounded-full blur-3xl"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-amber-500/20 border border-amber-400/20">
                            <svg class="w-6 h-6 text-amber-400" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" /></svg>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">My Favorites</h1>
                    </div>
                    <p class="text-emerald-300/70 text-sm sm:text-base max-w-lg">Your personal collection of species to watch and track.</p>
                </div>
                @if($favorites->count() > 0)
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-900/50 text-emerald-300 text-sm font-semibold rounded-xl border border-emerald-700/40 shrink-0 backdrop-blur-sm">
                        <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        {{ $favorites->count() }} {{ Str::plural('species', $favorites->count()) }} saved
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if($favorites->count() > 0)
            {{-- Favorites Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($favorites as $favorite)
                    <div
                        wire:key="fav-{{ $favorite->taxon_id }}"
                        class="group bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                    >
                        {{-- Card Top Accent --}}
                        <div class="h-1.5 bg-gradient-to-r from-amber-400 via-rose-400 to-emerald-400"></div>

                        <div class="p-6">
                            {{-- Species Name --}}
                            <div class="mb-4">
                                <a
                                    href="/species/{{ $favorite->taxon_id }}"
                                    wire:navigate
                                    class="text-lg font-semibold italic text-emerald-800 hover:text-emerald-600 transition-colors duration-300 leading-snug"
                                >
                                    {{ $favorite->scientific_name }}
                                </a>
                            </div>

                            {{-- Meta Row --}}
                            <div class="flex flex-wrap items-center gap-2 mb-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-mono rounded-lg border border-emerald-100">
                                    ID {{ $favorite->taxon_id }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-stone-50 text-stone-500 text-xs rounded-lg border border-stone-200">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    {{ $favorite->added_at->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <a
                                    href="/species/{{ $favorite->taxon_id }}"
                                    wire:navigate
                                    class="inline-flex items-center gap-1.5 text-sm text-emerald-600 hover:text-emerald-800 font-medium transition-colors duration-300"
                                >
                                    View details
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                </a>
                                <button
                                    wire:click="removeFavorite({{ $favorite->taxon_id }})"
                                    wire:confirm="Remove {{ $favorite->scientific_name }} from favorites?"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl transition-all duration-300 border border-rose-100 hover:border-rose-200 hover:shadow-sm"
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
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="max-w-sm mx-auto">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-emerald-100 to-teal-50 rounded-2xl flex items-center justify-center border border-emerald-100 shadow-sm">
                        <span class="text-4xl">🌿</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Start your collection</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed">
                        Discover endangered and threatened species across the globe. Save the ones you care about to track their conservation status.
                    </p>
                    <a
                        href="/"
                        wire:navigate
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        Explore Species
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
