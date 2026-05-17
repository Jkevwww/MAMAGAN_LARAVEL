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
    <div class="min-h-screen lg:flex">
        <aside class="bg-slate-950 text-white lg:w-72 p-5">
            <a href="{{ route('admin.dashboard') }}" class="block text-xl font-bold">Mamagan Admin</a>
            <nav class="mt-8 grid gap-1 text-sm">
                @php
                    $links = [
                        ['Dashboard', 'admin.dashboard'],
                        ['Bookings', 'admin.bookings.index'],
                        ['Check-In', 'admin.checkin.index'],
                        ['Facilities', 'admin.facilities.index'],
                        ['Promotions', 'admin.promotions.index'],
                        ['Seasonal Rates', 'admin.seasonal-rates.index'],
                        ['Blackout Dates', 'admin.blackout-dates.index'],
                        ['Users', 'admin.users.index'],
                        ['Reports', 'admin.reports.index'],
                        ['Settings', 'admin.settings.edit'],
                        ['System Logs', 'admin.logs.index'],
                    ];
                @endphp
                @foreach ($links as [$label, $route])
                    @if (Route::has($route))
                        <a href="{{ route($route) }}" class="rounded-md px-3 py-2 {{ request()->routeIs($route) ? 'bg-cyan-600' : 'hover:bg-white/10' }}">{{ $label }}</a>
                    @endif
                @endforeach
                <a href="{{ route('facilities.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10">Client Site</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-3">
                    @csrf
                    <button class="w-full text-left rounded-md px-3 py-2 hover:bg-white/10">Logout</button>
                </form>
            </nav>
        </aside>
        <main class="flex-1 p-4 lg:p-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800">{{ session('success') }}</div>
            @endif
            @if (session('status'))
                <div class="mb-4 rounded-md bg-cyan-50 border border-cyan-200 px-4 py-3 text-cyan-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-red-800">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
