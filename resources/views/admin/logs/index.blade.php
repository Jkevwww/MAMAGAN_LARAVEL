@extends('layouts.admin')

@section('content')
    @php
        $activeFilters = collect(['search', 'action', 'date_from', 'date_to'])->filter(fn ($key) => filled(request($key)));
        $filtersOpen = $activeFilters->isNotEmpty() ? 'true' : 'false';
        $maxActionCount = max(1, $actionBreakdown->max() ?? 0);
        $summaryCards = [
            ['label' => 'Logs', 'value' => number_format($summary['total']), 'caption' => 'Matching current filters', 'tone' => 'bg-cyan-50 text-cyan-700 ring-cyan-100', 'icon' => 'M4 5h16M4 12h16M4 19h16'],
            ['label' => 'Today', 'value' => number_format($summary['today']), 'caption' => 'Activity since midnight', 'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'icon' => 'M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z'],
            ['label' => 'Actors', 'value' => number_format($summary['actors']), 'caption' => 'Users in this result', 'tone' => 'bg-indigo-50 text-indigo-700 ring-indigo-100', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87'],
            ['label' => 'System', 'value' => number_format($summary['system']), 'caption' => 'Automated events', 'tone' => 'bg-slate-100 text-slate-700 ring-slate-200', 'icon' => 'M12 3v3M12 18v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M3 12h3M18 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12'],
        ];
        $actionTone = fn ($action) => str($action)->contains(['deleted', 'failed', 'cancelled'])
            ? 'bg-rose-50 text-rose-700 ring-rose-200'
            : (str($action)->contains(['created', 'checked_in', 'paid', 'updated'])
                ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                : 'bg-slate-100 text-slate-700 ring-slate-200');
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Administration</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">System Logs</h1>
            <p class="mt-1 text-sm text-slate-500">Audit user actions, system events, targets, and IP activity.</p>
        </div>
        <div class="rounded-lg bg-white px-3 py-2 text-sm font-bold text-slate-600 shadow-sm ring-1 ring-slate-200">
            Showing 10 logs per page
        </div>
    </div>

    <form class="mt-6 rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200" x-data="{ filtersOpen: {{ $filtersOpen }} }">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row">
                <div class="relative min-w-0 flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                    </svg>
                    <input name="search" value="{{ request('search') }}" class="h-10 w-full rounded-lg border-slate-300 pl-9 pr-3 text-sm" placeholder="Search user, action, target, or IP address">
                </div>
                <button class="h-10 rounded-lg bg-slate-950 px-4 text-sm font-bold text-white transition hover:bg-slate-800">Search</button>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="relative inline-flex h-10 items-center gap-2 rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/></svg>
                    Filters
                    @if ($activeFilters->isNotEmpty())
                        <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-cyan-700 px-1 text-[10px] font-extrabold text-white ring-2 ring-white">{{ $activeFilters->count() }}</span>
                    @endif
                </button>
                @if ($activeFilters->isNotEmpty())
                    <a href="{{ route('admin.logs.index') }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Reset</a>
                @endif
            </div>
        </div>

        <div x-show="filtersOpen" x-transition x-cloak class="mt-4 border-t border-slate-100 pt-4">
            <div class="grid gap-3 md:grid-cols-3">
                <label class="grid gap-1 text-sm font-bold text-slate-700">Action
                    <select name="action" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                        <option value="">All actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ str($action)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-1 text-sm font-bold text-slate-700">From
                    <input name="date_from" type="date" value="{{ request('date_from') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                </label>
                <label class="grid gap-1 text-sm font-bold text-slate-700">To
                    <input name="date_to" type="date" value="{{ request('date_to') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                </label>
            </div>
        </div>
    </form>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($summaryCards as $card)
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $card['value'] }}</p>
                    </div>
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg ring-1 {{ $card['tone'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-slate-500">{{ $card['caption'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-slate-950">Audit Trail</h2>
                    <p class="text-xs text-slate-500">Latest matching events, limited to 10 rows per page.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="p-3">Time</th>
                            <th class="p-3">Actor</th>
                            <th class="p-3">Action</th>
                            <th class="p-3">Target</th>
                            <th class="p-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-t border-slate-100 transition hover:bg-slate-50">
                                <td class="p-3">
                                    <p class="font-bold text-slate-950">{{ $log->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-slate-500">{{ $log->created_at->format('g:i A') }}</p>
                                </td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-950">{{ $log->user?->name ?? 'System' }}</p>
                                    <p class="text-xs text-slate-500">{{ $log->user?->email ?? 'Automated event' }}</p>
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-bold ring-1 {{ $actionTone($log->action) }}">
                                        {{ str($log->action)->replace(['.', '_'], ' ')->title() }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    @if ($log->target_type)
                                        <p class="font-semibold text-slate-800">{{ class_basename($log->target_type) }}</p>
                                        <p class="font-mono text-xs text-slate-500">#{{ $log->target_id }}</p>
                                    @else
                                        <span class="text-slate-500">None</span>
                                    @endif
                                </td>
                                <td class="p-3 font-mono text-xs font-semibold text-slate-600">{{ $log->ip_address ?: 'None' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-8 text-center text-slate-500">No system logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm font-semibold text-slate-500">
                    @if ($logs->total())
                        Showing {{ $logs->firstItem() }}-{{ $logs->lastItem() }} of {{ $logs->total() }}
                    @else
                        Showing 0 of 0
                    @endif
                </p>
                <div class="flex items-center gap-2">
                    @if ($logs->onFirstPage())
                        <span class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                            Previous
                        </span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                            Previous
                        </a>
                    @endif

                    <span class="inline-flex h-9 items-center rounded-lg bg-slate-100 px-3 text-sm font-bold text-slate-600">
                        Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
                    </span>

                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-cyan-700 px-3 text-sm font-bold text-white transition hover:bg-cyan-600">
                            Next
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                        </a>
                    @else
                        <span class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-400">
                            Next
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                        </span>
                    @endif
                </div>
            </div>
        </section>

        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <h2 class="font-bold text-slate-950">Action Breakdown</h2>
            <p class="mt-1 text-xs text-slate-500">Most frequent actions in the current result.</p>
            <div class="mt-4 grid gap-3">
                @forelse ($actionBreakdown as $action => $count)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                            <span class="truncate font-semibold text-slate-700">{{ str($action)->replace(['.', '_'], ' ')->title() }}</span>
                            <span class="font-bold text-slate-950">{{ $count }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-cyan-600" style="width: {{ ($count / $maxActionCount) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No action data found.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
