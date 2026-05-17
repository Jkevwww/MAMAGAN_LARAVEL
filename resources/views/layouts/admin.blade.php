<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mamagan Resort') }} Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="page-loader" aria-hidden="true">
        <div class="skeleton-card">
            <div class="skeleton-line h-4 w-32"></div>
            <div class="mt-5 grid gap-3">
                <div class="skeleton-tile h-16"></div>
                <div class="skeleton-line h-3 w-11/12"></div>
                <div class="skeleton-line h-3 w-8/12"></div>
            </div>
        </div>
    </div>

    @php
        $icons = [
            'dashboard' => '<path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/>',
            'calendar' => '<path d="M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>',
            'scan' => '<path d="M4 7V5a1 1 0 0 1 1-1h2M17 4h2a1 1 0 0 1 1 1v2M20 17v2a1 1 0 0 1-1 1h-2M7 20H5a1 1 0 0 1-1-1v-2M8 8h8v8H8z"/>',
            'facility' => '<path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>',
            'tag' => '<path d="M20 13 11 4H4v7l9 9 7-7Z"/><path d="M7.5 7.5h.01"/>',
            'rate' => '<path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>',
            'blackout' => '<path d="M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="m9 14 6 4M15 14l-6 4"/>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
            'report' => '<path d="M4 19.5V4a2 2 0 0 1 2-2h9l5 5v12.5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/><path d="M14 2v6h6M8 13h8M8 17h6"/>',
            'settings' => '<path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6c.39-.16.72-.42 1-.6.28-.18.4-.68.4-1.1V3a2 2 0 1 1 4 0v.1c0 .42.12.92.4 1.1.28.18.61.44 1 .6a1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.16.39.42.72.6 1 .18.28.68.4 1.1.4h.1a2 2 0 1 1 0 4h-.1c-.42 0-.92.12-1.1.4-.18.28-.44.61-.6 1Z"/>',
            'log' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8M8 9h2"/>',
            'site' => '<path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/><path d="M3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>',
            'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        ];
        $links = [
            ['Dashboard', 'admin.dashboard', 'dashboard'],
            ['Bookings', 'admin.bookings.index', 'calendar'],
            ['Check-In', 'admin.checkin.index', 'scan'],
            ['Facilities', 'admin.facilities.index', 'facility'],
            ['Promotions', 'admin.promotions.index', 'tag'],
            ['Seasonal Rates', 'admin.seasonal-rates.index', 'rate'],
            ['Blackout Dates', 'admin.blackout-dates.index', 'blackout'],
            ['Users', 'admin.users.index', 'users'],
            ['Reports', 'admin.reports.index', 'report'],
            ['Settings', 'admin.settings.edit', 'settings'],
            ['System Logs', 'admin.logs.index', 'log'],
        ];
    @endphp

    <div class="min-h-screen lg:flex" x-data="{ collapsed: localStorage.getItem('adminSidebarCollapsed') === 'true' }" x-effect="localStorage.setItem('adminSidebarCollapsed', collapsed)">
        <aside class="bg-slate-950 text-white transition-all duration-300 ease-out lg:sticky lg:top-0 lg:h-screen" :class="collapsed ? 'lg:w-20' : 'lg:w-72'">
            <div class="flex items-center justify-between gap-3 p-4">
                <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-cyan-500 font-bold">M</span>
                    <span x-show="!collapsed" x-transition.opacity class="truncate text-lg font-bold">Mamagan Admin</span>
                </a>
                <button type="button" data-no-loader="true" @click="collapsed = !collapsed" class="hidden rounded-lg p-2 text-slate-300 transition hover:bg-white/10 hover:text-white lg:inline-flex" :aria-label="collapsed ? 'Expand sidebar' : 'Minimize sidebar'">
                    <svg class="h-5 w-5 transition-transform" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                    </svg>
                </button>
            </div>

            <nav class="grid gap-1 px-3 pb-4 text-sm">
                @foreach ($links as [$label, $route, $icon])
                    @if (Route::has($route))
                        <a href="{{ route($route) }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 transition duration-200 {{ request()->routeIs($route) ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons[$icon] !!}</svg>
                            <span x-show="!collapsed" x-transition.opacity class="truncate">{{ $label }}</span>
                            <span x-show="collapsed" class="pointer-events-none absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white shadow-lg group-hover:block">{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
                <div class="my-2 border-t border-white/10"></div>
                <a href="{{ route('facilities.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-300 transition duration-200 hover:bg-white/10 hover:text-white">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['site'] !!}</svg>
                    <span x-show="!collapsed" x-transition.opacity class="truncate">Client Site</span>
                    <span x-show="collapsed" class="pointer-events-none absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white shadow-lg group-hover:block">Client Site</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-slate-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['logout'] !!}</svg>
                        <span x-show="!collapsed" x-transition.opacity class="truncate">Logout</span>
                        <span x-show="collapsed" class="pointer-events-none absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white shadow-lg group-hover:block">Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        <main class="flex-1 p-4 transition-all duration-300 lg:p-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-cyan-50 border border-cyan-200 px-4 py-3 text-cyan-800 shadow-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800 shadow-sm">{{ $errors->first() }}</div>
            @endif
            <div class="animate-[fadeIn_.22s_ease-out]">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
