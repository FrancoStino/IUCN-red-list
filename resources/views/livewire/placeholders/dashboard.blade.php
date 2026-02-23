<div>
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
</div>
