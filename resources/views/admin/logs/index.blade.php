@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">System Logs</h1>
            <p class="mt-1 text-sm text-slate-500">Search audit events by user, action, target, and IP address.</p>
        </div>
    </div>

    <form class="mt-6 flex flex-col items-end gap-2" x-data="{ filtersOpen: {{ request()->hasAny(['action', 'date_from', 'date_to']) ? 'true' : 'false' }} }">
        <div class="inline-flex w-full items-center gap-1.5 rounded-lg bg-white p-1.5 shadow-sm ring-1 ring-slate-200 sm:w-auto">
            <div class="relative min-w-0 flex-1 sm:flex-none">
                <svg class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                </svg>
                <input name="search" value="{{ request('search') }}" class="h-8 w-full rounded-md border-slate-300 py-1 pl-8 pr-2 text-sm sm:w-72" placeholder="Search logs">
            </div>
            <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="relative grid h-8 w-8 shrink-0 place-items-center rounded-md border border-slate-300 text-slate-700 transition hover:bg-slate-50" aria-label="Toggle filters">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/></svg>
                @if (request()->hasAny(['action', 'date_from', 'date_to']))
                    <span class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-cyan-600 ring-2 ring-white"></span>
                @endif
            </button>
            <button class="h-8 rounded-md bg-cyan-700 px-3 text-sm font-semibold text-white transition hover:bg-cyan-600">Search</button>
        </div>

        <div x-show="filtersOpen" x-transition x-cloak class="w-full rounded-lg bg-white p-4 shadow-lg ring-1 ring-slate-200 sm:w-[560px]">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-950">Filter logs</h2>
                    <p class="mt-1 text-xs text-slate-500">Review audit activity by action and date range.</p>
                </div>
                <button type="button" data-no-loader="true" @click="filtersOpen = false" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Close filters">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <label class="grid gap-1 text-sm font-semibold text-slate-700">Action
                    <select name="action" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                        <option value="">All actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">From
                    <input name="date_from" type="date" value="{{ request('date_from') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                </label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">To
                    <input name="date_to" type="date" value="{{ request('date_to') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                </label>
            </div>
            <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('admin.logs.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                <button class="rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-600">Apply filters</button>
            </div>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600"><tr><th class="p-3">Date</th><th class="p-3">User</th><th class="p-3">Action</th><th class="p-3">Target</th><th class="p-3">IP</th></tr></thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t transition hover:bg-slate-50">
                        <td class="p-3">{{ $log->created_at->format('M d, Y g:i A') }}</td>
                        <td class="p-3">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="p-3">{{ $log->action }}</td>
                        <td class="p-3">{{ $log->target_type ? class_basename($log->target_type).' #'.$log->target_id : 'None' }}</td>
                        <td class="p-3">{{ $log->ip_address ?: 'None' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">No system logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
