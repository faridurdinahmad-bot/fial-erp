<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-100 text-neutral-800 antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-56 shrink-0 border-r border-neutral-200 bg-white">
            <nav class="flex flex-col gap-0.5 p-3" aria-label="Main">
                <a href="{{ route('dashboard') }}" class="rounded px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100">
                    Dashboard
                </a>
                <span class="mt-2 px-3 text-xs font-semibold uppercase tracking-wider text-neutral-400">Catalog</span>
                <a href="{{ route('categories.index') }}" class="rounded px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100">Categories</a>
                <a href="#" class="rounded px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100">Product Groups</a>
                <a href="#" class="rounded px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100">Products</a>
                <span class="mt-2 px-3 text-xs font-semibold uppercase tracking-wider text-neutral-400">Master</span>
                <a href="{{ route('brands.index') }}" class="rounded px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100">Brands</a>
                <a href="{{ route('units.index') }}" class="rounded px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100">Units</a>
                <a href="{{ route('warranties.index') }}" class="rounded px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100">Warranties</a>
            </nav>
        </aside>

        <div class="flex flex-1 flex-col min-w-0">
            {{-- Topbar --}}
            <header class="shrink-0 border-b border-neutral-200 bg-white px-6 py-3">
                <div class="flex items-center justify-between">
                    <h1 class="text-sm font-semibold text-neutral-700">@yield('title', 'Dashboard')</h1>
                    <div class="text-sm text-neutral-500">{{-- User / company placeholder --}}</div>
                </div>
            </header>

            {{-- Main content --}}
            <main class="flex-1 overflow-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
