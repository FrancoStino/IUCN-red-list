<div>
    <div class="animate-pulse">
        {{-- Back Link Skeleton --}}
        <div class="mb-6">
            <div class="h-4 w-16 bg-gray-200 rounded-lg"></div>
        </div>

        {{-- Header Skeleton --}}
        <div class="relative bg-emerald-950 rounded-3xl shadow-xl overflow-hidden p-8 sm:p-10 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div>
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <div class="h-7 w-36 bg-emerald-800/50 rounded-full"></div>
                        <div class="h-7 w-16 bg-amber-900/40 rounded-full"></div>
                    </div>
                    <div class="h-4 w-48 bg-emerald-800/40 rounded-lg"></div>
                </div>
                <div class="h-10 w-36 bg-emerald-800/50 rounded-2xl"></div>
            </div>
        </div>

        {{-- Conservation Status Skeleton --}}
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-9 h-9 bg-emerald-100 rounded-xl"></div>
                <div class="h-6 w-44 bg-gray-200 rounded-lg"></div>
            </div>
            <div class="flex flex-col sm:flex-row items-start gap-6">
                <div class="w-24 h-24 bg-gray-200 rounded-3xl"></div>
                <div class="flex-1 space-y-3">
                    <div class="h-7 w-48 bg-gray-200 rounded-lg"></div>
                    <div class="h-4 w-32 bg-gray-100 rounded-lg"></div>
                </div>
            </div>
        </div>

        {{-- Extinction Risk & Population Trend Skeleton --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-7">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 bg-red-100 rounded-xl"></div>
                    <div class="h-5 w-32 bg-gray-200 rounded-lg"></div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 rounded-2xl p-4 bg-gray-50 border border-gray-200">
                        <div class="w-10 h-10 bg-gray-200 rounded-xl"></div>
                        <div class="h-4 w-40 bg-gray-200 rounded-lg"></div>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl p-4 bg-gray-50 border border-gray-200">
                        <div class="w-10 h-10 bg-gray-200 rounded-xl"></div>
                        <div class="h-4 w-56 bg-gray-200 rounded-lg"></div>
                    </div>
                </div>
            </div>
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-7">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 bg-blue-100 rounded-xl"></div>
                    <div class="h-5 w-36 bg-gray-200 rounded-lg"></div>
                </div>
                <div class="flex items-center gap-5 rounded-2xl p-5 bg-gray-50 border border-gray-200">
                    <div class="w-14 h-14 bg-gray-200 rounded-2xl"></div>
                    <div class="space-y-2">
                        <div class="h-6 w-28 bg-gray-200 rounded-lg"></div>
                        <div class="h-3 w-24 bg-gray-100 rounded-lg"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Conservation Actions Skeleton --}}
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-9 h-9 bg-emerald-100 rounded-xl"></div>
                <div class="h-6 w-48 bg-gray-200 rounded-lg"></div>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="h-10 w-44 bg-gray-100 rounded-xl"></div>
                <div class="h-10 w-36 bg-gray-100 rounded-xl"></div>
                <div class="h-10 w-52 bg-gray-100 rounded-xl"></div>
                <div class="h-10 w-40 bg-gray-100 rounded-xl"></div>
            </div>
        </div>

        {{-- Documentation Skeleton --}}
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-9 h-9 bg-gray-100 rounded-xl"></div>
                <div class="h-6 w-36 bg-gray-200 rounded-lg"></div>
            </div>
            <div class="space-y-8">
                @for($i = 0; $i < 3; $i++)
                    <div>
                        <div class="h-5 w-40 bg-gray-200 rounded-lg mb-4 pb-2 border-b border-gray-100"></div>
                        <div class="space-y-2">
                            <div class="h-3 w-full bg-gray-100 rounded"></div>
                            <div class="h-3 w-11/12 bg-gray-100 rounded"></div>
                            <div class="h-3 w-4/5 bg-gray-100 rounded"></div>
                            <div class="h-3 w-full bg-gray-100 rounded"></div>
                            <div class="h-3 w-3/4 bg-gray-100 rounded"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
