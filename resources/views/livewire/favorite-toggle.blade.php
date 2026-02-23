@if($compact)
    {{-- Compact mode: small inline heart for use in lists/cards --}}
    <button
        wire:click="toggle"
        class="inline-flex items-center justify-center w-8 h-8 rounded-full transition-all duration-300 active:scale-90
            {{ $isFavorite
                ? 'text-rose-500 hover:text-rose-600 bg-rose-50 hover:bg-rose-100'
                : 'text-gray-300 hover:text-rose-400 bg-transparent hover:bg-rose-50' }}"
        title="{{ $isFavorite ? 'Remove from favorites' : 'Add to favorites' }}"
    >
        @if($isFavorite)
            <svg class="w-4.5 h-4.5 drop-shadow-sm" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" /></svg>
        @else
            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 7.875c-.278-2.908-2.754-5.125-5.752-5.125A5.5 5.5 0 0 0 12 5.052 5.5 5.5 0 0 0 7.688 2.75C4.714 2.75 2.25 5.072 2.25 8c0 3.925 2.438 7.111 4.739 9.256a25.175 25.175 0 0 0 4.244 3.17c.138.082.27.157.383.218l.022.012.007.004.003.001a.752.752 0 0 0 .704 0l.003-.001.007-.004.022-.012a15.247 15.247 0 0 0 .383-.218 25.18 25.18 0 0 0 4.244-3.17C19.312 15.111 21.75 11.925 21.75 8c0-.042 0-.083-.002-.125Z" /></svg>
        @endif
    </button>
@else
    {{-- Full mode: large button for species detail page --}}
    <button
        wire:click="toggle"
        class="group relative inline-flex items-center justify-center w-14 h-14 rounded-2xl transition-all duration-300 active:scale-90
            {{ $isFavorite
                ? 'bg-gradient-to-br from-rose-500 to-pink-600 text-white shadow-lg shadow-rose-500/40 hover:shadow-xl hover:shadow-rose-500/50 hover:from-rose-600 hover:to-pink-700 ring-2 ring-rose-300/50'
                : 'bg-white text-gray-400 border-2 border-gray-200 hover:border-rose-300 hover:text-rose-400 hover:bg-rose-50 hover:shadow-md shadow-sm' }}"
        title="{{ $isFavorite ? 'Remove from favorites' : 'Add to favorites' }}"
    >
        {{-- Glow ring on favorited state --}}
        @if($isFavorite)
            <span class="absolute inset-0 rounded-2xl animate-ping bg-rose-400/20 pointer-events-none"></span>
        @endif

        @if($isFavorite)
            <svg class="w-7 h-7 relative z-10 drop-shadow-sm" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" /></svg>
        @else
            <svg class="w-7 h-7 group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 7.875c-.278-2.908-2.754-5.125-5.752-5.125A5.5 5.5 0 0 0 12 5.052 5.5 5.5 0 0 0 7.688 2.75C4.714 2.75 2.25 5.072 2.25 8c0 3.925 2.438 7.111 4.739 9.256a25.175 25.175 0 0 0 4.244 3.17c.138.082.27.157.383.218l.022.012.007.004.003.001a.752.752 0 0 0 .704 0l.003-.001.007-.004.022-.012a15.247 15.247 0 0 0 .383-.218 25.18 25.18 0 0 0 4.244-3.17C19.312 15.111 21.75 11.925 21.75 8c0-.042 0-.083-.002-.125Z" /></svg>
        @endif
    </button>
@endif
