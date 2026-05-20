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
            'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
            'close' => '<path d="M6 18 18 6M6 6l12 12"/>',
        ];

        $navGroups = [
            'Operations' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'dashboard'],
                ['label' => 'Bookings', 'route' => 'admin.bookings.index', 'match' => 'admin.bookings.*', 'icon' => 'calendar'],
                ['label' => 'Check-In', 'route' => 'admin.checkin.index', 'match' => 'admin.checkin.*', 'icon' => 'scan'],
                ['label' => 'Facilities', 'route' => 'admin.facilities.index', 'match' => 'admin.facilities.*', 'icon' => 'facility', 'adminOnly' => true],
            ],
            'Revenue' => [
                ['label' => 'Promotions', 'route' => 'admin.promotions.index', 'match' => 'admin.promotions.*', 'icon' => 'tag', 'adminOnly' => true],
                ['label' => 'Seasonal Rates', 'route' => 'admin.seasonal-rates.index', 'match' => 'admin.seasonal-rates.*', 'icon' => 'rate', 'adminOnly' => true],
                ['label' => 'Blackout Dates', 'route' => 'admin.blackout-dates.index', 'match' => 'admin.blackout-dates.*', 'icon' => 'blackout', 'adminOnly' => true],
                ['label' => 'Reports', 'route' => 'admin.reports.index', 'match' => 'admin.reports.*', 'icon' => 'report'],
            ],
            'Administration' => [
                ['label' => 'Users', 'route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'users', 'adminOnly' => true],
                ['label' => 'Settings', 'route' => 'admin.settings.edit', 'match' => 'admin.settings.*', 'icon' => 'settings', 'adminOnly' => true],
                ['label' => 'System Logs', 'route' => 'admin.logs.index', 'match' => 'admin.logs.*', 'icon' => 'log', 'adminOnly' => true],
            ],
        ];

        $user = Auth::user();
        $isAdmin = $user?->isAdmin() ?? false;
        $roleLabel = str($user?->role ?? 'staff')->replace('_', ' ')->title();
    @endphp

    <div class="min-h-screen lg:flex" x-data="{ collapsed: localStorage.getItem('adminSidebarCollapsed') === 'true', mobileOpen: false }" x-effect="localStorage.setItem('adminSidebarCollapsed', collapsed)">
        <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden" @click="mobileOpen = false" x-cloak></div>

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-slate-950 text-white shadow-2xl transition-all duration-300 ease-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none"
            :class="[
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                collapsed ? 'lg:w-20' : 'lg:w-72'
            ]"
        >
            <div class="flex h-16 items-center justify-between gap-3 border-b border-white/10 px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-lg bg-white">
                        <img src="{{ asset('assets/logo/mamagan.png') }}" alt="Mamagan" class="h-8 w-8 object-contain">
                    </span>
                    <span x-show="!collapsed" x-transition.opacity class="min-w-0">
                        <span class="block truncate text-sm font-extrabold leading-5">Mamagan Admin</span>
                        <span class="block truncate text-xs font-semibold text-slate-400">Resort operations</span>
                    </span>
                </a>
                <div class="flex items-center gap-1">
                    <button type="button" data-no-loader="true" @click="collapsed = !collapsed" class="hidden rounded-md p-2 text-slate-300 transition hover:bg-white/10 hover:text-white lg:inline-flex" :aria-label="collapsed ? 'Expand sidebar' : 'Minimize sidebar'">
                        <svg class="h-5 w-5 transition-transform" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                        </svg>
                    </button>
                    <button type="button" data-no-loader="true" @click="mobileOpen = false" class="rounded-md p-2 text-slate-300 transition hover:bg-white/10 hover:text-white lg:hidden" aria-label="Close sidebar">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['close'] !!}</svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-3 py-4">
                <nav class="grid gap-5 text-sm">
                    @foreach ($navGroups as $group => $links)
                        @php
                            $visibleLinks = collect($links)->filter(fn ($link) => Route::has($link['route']) && (empty($link['adminOnly']) || $isAdmin));
                        @endphp

                        @if ($visibleLinks->isNotEmpty())
                            <div>
                                <div x-show="!collapsed" x-transition.opacity class="mb-2 px-3 text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-500">{{ $group }}</div>
                                <div class="grid gap-1">
                                    @foreach ($visibleLinks as $link)
                                        @php
                                            $active = request()->routeIs($link['match']);
                                        @endphp
                                        <a
                                            href="{{ route($link['route']) }}"
                                            @click="mobileOpen = false"
                                            class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 transition duration-200 {{ $active ? 'bg-teal-500 text-white shadow-lg shadow-teal-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                                        >
                                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-md {{ $active ? 'bg-white/15' : 'bg-white/5 group-hover:bg-white/10' }}">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons[$link['icon']] !!}</svg>
                                            </span>
                                            <span x-show="!collapsed" x-transition.opacity class="truncate font-bold">{{ $link['label'] }}</span>
                                            @if ($active)
                                                <span x-show="!collapsed" class="ml-auto h-2 w-2 rounded-full bg-white"></span>
                                            @endif
                                            <span x-show="collapsed" class="pointer-events-none absolute left-full z-50 ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs font-bold text-white shadow-lg group-hover:block">{{ $link['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </nav>
            </div>

            <div class="border-t border-white/10 p-3">
                <div class="mb-2 rounded-lg bg-white/5 p-3" x-show="!collapsed" x-transition.opacity>
                    <div class="truncate text-sm font-extrabold">{{ $user?->name }}</div>
                    <div class="mt-1 truncate text-xs text-slate-400">{{ $user?->email }}</div>
                    <div class="mt-2 inline-flex rounded-full bg-teal-400/10 px-2 py-1 text-xs font-bold text-teal-200">{{ $roleLabel }}</div>
                </div>

                <div class="grid gap-1">
                    <a href="{{ route('facilities.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-300 transition duration-200 hover:bg-white/10 hover:text-white">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-md bg-white/5 group-hover:bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['site'] !!}</svg>
                        </span>
                        <span x-show="!collapsed" x-transition.opacity class="truncate text-sm font-bold">Client Site</span>
                        <span x-show="collapsed" class="pointer-events-none absolute left-full z-50 ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs font-bold text-white shadow-lg group-hover:block">Client Site</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-slate-300 transition duration-200 hover:bg-red-500/15 hover:text-white">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-md bg-white/5 group-hover:bg-red-500/20">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['logout'] !!}</svg>
                            </span>
                            <span x-show="!collapsed" x-transition.opacity class="truncate text-sm font-bold">Logout</span>
                            <span x-show="collapsed" class="pointer-events-none absolute left-full z-50 ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs font-bold text-white shadow-lg group-hover:block">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur lg:px-8">
                <button type="button" data-no-loader="true" @click="mobileOpen = true" class="rounded-md border border-slate-200 bg-white p-2 text-slate-700 shadow-sm transition hover:bg-slate-50 lg:hidden" aria-label="Open sidebar">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['menu'] !!}</svg>
                </button>
                <div class="min-w-0">
                    <p class="truncate text-sm font-extrabold text-slate-950">Admin Panel</p>
                    <p class="hidden text-xs text-slate-500 sm:block">Manage bookings, resort operations, and reporting.</p>
                </div>
                <a href="{{ route('facilities.index') }}" class="hidden rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 sm:inline-flex">
                    View Site
                </a>
            </header>

            <main class="p-4 transition-all duration-300 lg:p-8">
                @if (session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">{{ session('success') }}</div>
                @endif
                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-cyan-800 shadow-sm">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 shadow-sm">{{ $errors->first() }}</div>
                @endif
                <div class="animate-[fadeIn_.22s_ease-out]">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
