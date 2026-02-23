<div>
    {{-- Skeleton: Species Detail --}}
    <div class="mb-6">
        <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
    </div>

    {{-- Skeleton: Header --}}
    <div class="relative bg-emerald-950 rounded-3xl shadow-xl overflow-hidden p-8 sm:p-10 mb-8 animate-pulse">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
            <div class="flex-1 min-w-0 space-y-4">
                <div class="h-10 bg-emerald-800/50 rounded-xl w-3/4"></div>
                <div class="flex flex-wrap gap-2">
                    @for($i = 0; $i < 5; $i++)
                        <div class="h-7 w-28 bg-emerald-900/50 rounded-xl"></div>
                    @endfor
                </div>
                <div class="h-7 w-24 bg-emerald-800/50 rounded-full"></div>
            </div>
            <div class="flex-shrink-0">
                <div class="h-12 w-40 bg-emerald-800/50 rounded-2xl"></div>
            </div>
        </div>
    </div>

    {{-- Skeleton: Common Names --}}
    <section class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 animate-pulse">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 rounded-xl bg-amber-100"></div>
            <div class="h-6 w-36 bg-gray-200 rounded-lg"></div>
        </div>
        <div class="flex flex-wrap gap-2">
            @for($i = 0; $i < 6; $i++)
                <div class="h-9 w-24 bg-gray-100 rounded-xl border border-gray-200"></div>
            @endfor
        </div>
    </section>

    {{-- Skeleton: Assessments --}}
    <section class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 animate-pulse">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 rounded-xl bg-emerald-100"></div>
            <div class="h-6 w-32 bg-gray-200 rounded-lg"></div>
        </div>
        <div class="space-y-3">
            @for($i = 0; $i < 3; $i++)
                <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5">
                    <div class="w-14 h-14 rounded-2xl bg-gray-200"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-5 bg-gray-200 rounded-lg w-1/2"></div>
                        <div class="h-4 bg-gray-100 rounded-lg w-2/3"></div>
                    </div>
                </div>
            @endfor
        </div>
    </section>
</div>
