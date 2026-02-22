<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">⭐ My Favorites</h1>
            <p class="mt-2 text-sm text-gray-500">
                @if($favorites->count() > 0)
                    {{ $favorites->count() }} {{ Str::plural('species', $favorites->count()) }} saved
                @else
                    Your collection is empty
                @endif
            </p>
        </div>

        @if($favorites->count() > 0)
            {{-- Favorites Table --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Scientific Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Added
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($favorites as $favorite)
                            <tr wire:key="fav-{{ $favorite->taxon_id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <a 
                                        href="/species/{{ $favorite->taxon_id }}" 
                                        wire:navigate
                                        class="text-emerald-700 hover:text-emerald-900 font-medium italic transition-colors"
                                    >
                                        {{ $favorite->scientific_name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $favorite->added_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button 
                                        wire:click="removeFavorite({{ $favorite->taxon_id }})"
                                        wire:confirm="Remove {{ $favorite->scientific_name }} from favorites?"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                                    >
                                        ✕ Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-12 text-center">
                <div class="text-5xl mb-4">🔍</div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">No favorites yet</h2>
                <p class="text-gray-500 mb-6">Browse species and add some!</p>
                <a 
                    href="/" 
                    wire:navigate
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors"
                >
                    ← Explore Species
                </a>
            </div>
        @endif
    </div>
</div>
