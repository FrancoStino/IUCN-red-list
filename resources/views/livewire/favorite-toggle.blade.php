<button
    wire:click="toggle"
    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-105 active:scale-95 border
        {{ $isFavorite
            ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100 hover:border-rose-300'
            : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300' }}"
    title="{{ $isFavorite ? 'Remove from favorites' : 'Add to favorites' }}"
>
    @if($isFavorite)
        <svg class="w-5 h-5 text-rose-500" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" /></svg>
        Favorited
    @else
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 7.875c-.278-2.908-2.754-5.125-5.752-5.125A5.5 5.5 0 0 0 12 5.052 5.5 5.5 0 0 0 7.688 2.75C4.714 2.75 2.25 5.072 2.25 8c0 3.925 2.438 7.111 4.739 9.256a25.175 25.175 0 0 0 4.244 3.17c.138.082.27.157.383.218l.022.012.007.004.003.001a.752.752 0 0 0 .704 0l.003-.001.007-.004.022-.012a15.247 15.247 0 0 0 .383-.218 25.18 25.18 0 0 0 4.244-3.17C19.312 15.111 21.75 11.925 21.75 8c0-.042 0-.083-.002-.125Z" /></svg>
        Add to Favorites
    @endif
</button>
