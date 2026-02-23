<footer class="bg-gray-900 text-gray-300 mt-auto">
    {{-- Top accent --}}
    <div class="h-0.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

    {{-- Stats Row --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 rounded-xl border border-gray-700/50 text-sm">
                <span class="flex items-center justify-center w-7 h-7 bg-emerald-900/50 rounded-lg text-emerald-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" /></svg>
                </span>
                <span class="text-gray-400">Species</span>
                <strong class="text-white font-semibold">{{ number_format($speciesCount) }}</strong>
            </span>
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 rounded-xl border border-gray-700/50 text-sm">
                <span class="flex items-center justify-center w-7 h-7 bg-emerald-900/50 rounded-lg text-emerald-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                </span>
                <span class="text-gray-400">Red List</span>
                <strong class="text-white font-semibold">{{ $redListVersion }}</strong>
            </span>
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 rounded-xl border border-gray-700/50 text-sm">
                <span class="flex items-center justify-center w-7 h-7 bg-emerald-900/50 rounded-lg text-emerald-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" /></svg>
                </span>
                <span class="text-gray-400">API</span>
                <strong class="text-white font-semibold">{{ $apiVersion }}</strong>
            </span>
        </div>
    </div>

    {{-- Bottom Credits --}}
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-gray-500">
                <span>Powered by the <a href="https://api.iucnredlist.org/" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:text-emerald-300 transition-colors font-medium">IUCN Red List API</a></span>
                <span>Data from <a href="https://www.iucnredlist.org" target="_blank" rel="noopener noreferrer" class="text-red-400 hover:text-red-300 transition-colors font-medium">IUCN Red List of Threatened Species™</a></span>
            </div>
        </div>
    </div>
</footer>
