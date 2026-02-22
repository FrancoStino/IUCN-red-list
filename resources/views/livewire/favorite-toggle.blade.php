<button 
    wire:click="toggle" 
    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
        {{ $isFavorite 
            ? 'bg-red-100 text-red-700 hover:bg-red-200' 
            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
    title="{{ $isFavorite ? 'Remove from favorites' : 'Add to favorites' }}"
>
    @if($isFavorite)
        ★ Favorited
    @else
        ☆ Favorite
    @endif
</button>
