<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IUCN Red List Explorer' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        {{-- Top accent bar --}}
        <div class="h-0.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-8">
                    {{-- Logo --}}
                    <a href="/" wire:navigate class="flex items-center gap-2 group">
                        <span class="text-2xl">🌍</span>
                        <span class="text-lg font-bold text-gray-900 tracking-tight group-hover:text-emerald-700 transition-colors">
                            IUCN Red List <span class="text-emerald-600">Explorer</span>
                        </span>
                    </a>

                    {{-- Nav Links --}}
                    <div class="hidden sm:flex items-center gap-1">
                        <a
                            href="/"
                            wire:navigate
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                {{ request()->is('/')
                                    ? 'text-emerald-700 bg-emerald-50'
                                    : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                            Dashboard
                        </a>
                        <a
                            href="/favorites"
                            wire:navigate
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                {{ request()->is('favorites')
                                    ? 'text-emerald-700 bg-emerald-50'
                                    : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                        >
                            <svg class="w-4 h-4" fill="{{ request()->is('favorites') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 7.875c-.278-2.908-2.754-5.125-5.752-5.125A5.5 5.5 0 0 0 12 5.052 5.5 5.5 0 0 0 7.688 2.75C4.714 2.75 2.25 5.072 2.25 8c0 3.925 2.438 7.111 4.739 9.256a25.175 25.175 0 0 0 4.244 3.17c.138.082.27.157.383.218l.022.012.007.004.003.001a.752.752 0 0 0 .704 0l.003-.001.007-.004.022-.012a15.247 15.247 0 0 0 .383-.218 25.18 25.18 0 0 0 4.244-3.17C19.312 15.111 21.75 11.925 21.75 8c0-.042 0-.083-.002-.125Z" /></svg>
                            Favorites
                        </a>
                    </div>
                </div>

                {{-- Mobile nav --}}
                <div class="flex sm:hidden items-center gap-2">
                    <a
                        href="/"
                        wire:navigate
                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg transition-colors
                            {{ request()->is('/') ? 'text-emerald-700 bg-emerald-50' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}"
                        title="Dashboard"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                    </a>
                    <a
                        href="/favorites"
                        wire:navigate
                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg transition-colors
                            {{ request()->is('favorites') ? 'text-emerald-700 bg-emerald-50' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}"
                        title="Favorites"
                    >
                        <svg class="w-5 h-5" fill="{{ request()->is('favorites') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 7.875c-.278-2.908-2.754-5.125-5.752-5.125A5.5 5.5 0 0 0 12 5.052 5.5 5.5 0 0 0 7.688 2.75C4.714 2.75 2.25 5.072 2.25 8c0 3.925 2.438 7.111 4.739 9.256a25.175 25.175 0 0 0 4.244 3.17c.138.082.27.157.383.218l.022.012.007.004.003.001a.752.752 0 0 0 .704 0l.003-.001.007-.004.022-.012a15.247 15.247 0 0 0 .383-.218 25.18 25.18 0 0 0 4.244-3.17C19.312 15.111 21.75 11.925 21.75 8c0-.042 0-.083-.002-.125Z" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <livewire:footer />
</body>
</html>
