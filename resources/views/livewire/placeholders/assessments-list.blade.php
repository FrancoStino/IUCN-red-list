<div>
    {{-- Header --}}
    <div class="mb-8 space-y-4">
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-emerald-700 hover:text-emerald-900 transition-colors group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Dashboard
        </a>

        {{-- Title area --}}
        <div class="relative rounded-2xl bg-emerald-950 overflow-hidden px-8 py-8 sm:px-10">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-emerald-400 blur-3xl"></div>
            </div>
            <div class="relative flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="animate-pulse">
                    <div class="h-10 w-64 bg-emerald-800/50 rounded-xl mb-3"></div>
                    <div class="h-5 w-40 bg-emerald-900/40 rounded-lg"></div>
                </div>
                <span class="inline-flex items-center rounded-full bg-emerald-800/50 border border-emerald-700/50 px-4 py-1.5 text-sm font-semibold text-emerald-300 animate-pulse">
                    Loading...
                </span>
            </div>
        </div>
    </div>

    {{-- Skeleton: Controls Bar --}}
    <div class="sticky top-[68px] z-10 bg-stone-50/90 backdrop-blur-xl border-b border-gray-200 py-4 mb-8 -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3 animate-pulse">
                <div class="h-10 w-32 bg-gray-200 rounded-xl"></div>
                <div class="h-10 w-36 bg-gray-200 rounded-xl"></div>
            </div>
            <div class="flex items-center gap-3 animate-pulse">
                <div class="h-10 w-40 bg-gray-200 rounded-xl"></div>
                <div class="h-10 w-48 bg-gray-200 rounded-xl"></div>
            </div>
        </div>
    </div>

    {{-- Skeleton: Table rows --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-100 rounded-2xl overflow-hidden">
        <div class="divide-y divide-gray-50">
            {{-- Skeleton header --}}
            <div class="bg-gray-50/80 px-6 py-4 flex gap-6 animate-pulse">
                <div class="h-3 w-12 bg-gray-200 rounded"></div>
                <div class="h-3 w-32 bg-gray-200 rounded"></div>
                <div class="h-3 w-24 bg-gray-200 rounded"></div>
                <div class="h-3 w-20 bg-gray-200 rounded"></div>
                <div class="h-3 w-12 bg-gray-200 rounded"></div>
            </div>
            @for($i = 0; $i < 8; $i++)
                <div class="px-6 py-4 flex items-center gap-6 animate-pulse">
                    <div class="h-4 w-12 bg-gray-200 rounded"></div>
                    <div class="h-4 w-40 bg-gray-200 rounded"></div>
                    <div class="h-4 w-24 bg-gray-100 rounded"></div>
                    <div class="h-6 w-16 bg-gray-200 rounded-lg"></div>
                    <div class="h-4 w-8 bg-gray-100 rounded"></div>
                    <div class="h-6 w-6 bg-gray-200 rounded-full ml-auto"></div>
                </div>
            @endfor
        </div>
    </div>
</div>
