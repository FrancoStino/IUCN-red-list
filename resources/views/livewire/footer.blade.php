<footer class="bg-gray-800 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-wrap justify-between items-center text-sm">
            <div class="flex space-x-6">
                <span>🔢 Species: <strong class="text-white">{{ number_format($speciesCount) }}</strong></span>
                <span>📋 Red List Version: <strong class="text-white">{{ $redListVersion }}</strong></span>
                <span>🔌 API: <strong class="text-white">{{ $apiVersion }}</strong></span>
            </div>
            <div class="text-gray-400">
                Data from <a href="https://www.iucnredlist.org" target="_blank" class="text-red-400 hover:text-red-300">IUCN Red List</a>
            </div>
        </div>
    </div>
</footer>
