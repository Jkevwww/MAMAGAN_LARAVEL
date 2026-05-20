@extends('layouts.admin')

@section('content')
    @php
        $filtersActive = request()->hasAny(['search', 'facility_id', 'date_from', 'date_to']);
        $calendarDays = collect();

        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $calendarDays->push($day->copy());
        }

        $previousMonthUrl = route('admin.blackout-dates.index', array_merge(request()->except(['calendar_month', 'page']), [
            'calendar_month' => $calendarDate->copy()->subMonth()->format('Y-m'),
        ]));
        $nextMonthUrl = route('admin.blackout-dates.index', array_merge(request()->except(['calendar_month', 'page']), [
            'calendar_month' => $calendarDate->copy()->addMonth()->format('Y-m'),
        ]));
        $currentMonthUrl = route('admin.blackout-dates.index', request()->except(['calendar_month', 'page']));
    @endphp

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Blackout Dates</h1>
            <p class="mt-1 text-sm text-slate-500">Block booking dates for all facilities or specific bookable items.</p>
        </div>
        <div class="grid grid-cols-4 overflow-hidden rounded-lg bg-white text-center shadow-sm ring-1 ring-slate-200 md:min-w-[460px]">
            <div class="border-r border-slate-100 px-3 py-2">
                <div class="text-lg font-bold text-slate-950">{{ number_format($summary['total']) }}</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Total</div>
            </div>
            <div class="border-r border-slate-100 px-3 py-2">
                <div class="text-lg font-bold text-amber-600">{{ number_format($summary['active_today']) }}</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Today</div>
            </div>
            <div class="border-r border-slate-100 px-3 py-2">
                <div class="text-lg font-bold text-cyan-700">{{ number_format($summary['this_month']) }}</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ $calendarDate->format('M') }}</div>
            </div>
            <div class="px-3 py-2">
                <div class="text-lg font-bold text-slate-950">{{ number_format($summary['all_facilities']) }}</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">All</div>
            </div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-[420px_minmax(0,1fr)]">
        <aside class="grid gap-3 xl:sticky xl:top-20 xl:self-start">
            <section class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-2 border-b border-slate-100 px-3 py-2">
                    <a href="{{ $previousMonthUrl }}" class="grid h-7 w-7 place-items-center rounded-md border border-slate-300 text-slate-700 transition hover:bg-slate-50" aria-label="Previous month">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div class="text-sm font-bold text-slate-950">{{ $calendarDate->format('F Y') }}</div>
                    <div class="flex items-center gap-1">
                        <a href="{{ $currentMonthUrl }}" class="rounded-md border border-slate-300 px-2 py-1 text-[11px] font-semibold text-slate-700 transition hover:bg-slate-50">Today</a>
                        <a href="{{ $nextMonthUrl }}" class="grid h-7 w-7 place-items-center rounded-md border border-slate-300 text-slate-700 transition hover:bg-slate-50" aria-label="Next month">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                    @foreach (['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $weekday)
                        <div class="px-1 py-1.5">{{ $weekday }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7">
                    @foreach ($calendarDays as $day)
                        @php
                            $dayBlackouts = $calendarBlackouts->filter(fn ($blackout) => $blackout->starts_at->lte($day) && $blackout->ends_at->gte($day));
                            $isCurrentMonth = $day->isSameMonth($calendarDate);
                            $isToday = $day->isToday();
                        @endphp
                        <div class="group relative min-h-16 border-r border-t border-slate-100 p-1.5 {{ $isCurrentMonth ? 'bg-white' : 'bg-slate-50 text-slate-400' }}">
                            <div class="flex items-start justify-between gap-1">
                                <span class="text-xs font-bold {{ $isToday ? 'grid h-6 w-6 place-items-center rounded-full bg-cyan-700 text-white' : 'text-slate-700' }}">{{ $day->day }}</span>
                                @if ($dayBlackouts->isNotEmpty())
                                    <span class="rounded-full bg-amber-100 px-1.5 py-0.5 text-[11px] font-bold text-amber-800">{{ $dayBlackouts->count() }}</span>
                                @endif
                            </div>
                            @if ($dayBlackouts->isNotEmpty())
                                <div class="mt-2 flex gap-1">
                                    @foreach ($dayBlackouts->take(3) as $blackout)
                                        <span class="h-2 w-2 rounded-full bg-amber-500" title="{{ $blackout->title }} - {{ $blackout->facility?->name ?? 'All facilities' }}"></span>
                                    @endforeach
                                </div>
                                <div class="pointer-events-none absolute left-1 top-full z-20 hidden w-52 rounded-md bg-slate-950 p-2 text-xs text-white shadow-lg group-hover:block">
                                    @foreach ($dayBlackouts->take(4) as $blackout)
                                        <div class="truncate font-semibold">{{ $blackout->title }}</div>
                                        <div class="mb-1 truncate text-slate-300">{{ $blackout->facility?->name ?? 'All facilities' }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <aside class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-bold text-slate-950">Upcoming</h2>
                    <span class="text-xs font-semibold text-slate-500">{{ $upcomingBlackouts->count() }}</span>
                </div>
                <div class="mt-2 grid gap-2">
                    @forelse ($upcomingBlackouts->take(3) as $blackout)
                        <div class="rounded-md border border-amber-200 bg-amber-50 px-2.5 py-2">
                            <div class="truncate text-xs font-bold text-amber-950">{{ $blackout->title }}</div>
                            <div class="mt-0.5 truncate text-[11px] text-amber-800">{{ $blackout->facility?->name ?? 'All facilities' }}</div>
                            <div class="mt-1 text-[11px] font-semibold text-amber-700">{{ $blackout->starts_at->format('M d') }} - {{ $blackout->ends_at->format('M d') }}</div>
                        </div>
                    @empty
                        <div class="rounded-md bg-slate-50 p-3 text-sm text-slate-500">No upcoming blocks.</div>
                    @endforelse
                </div>
            </aside>
        </aside>

        <div class="min-w-0">
        <details id="create-blackout" class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <summary class="cursor-pointer list-none">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-950">Create blackout window</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Expand only when adding a new closure.</p>
                    </div>
                    <span class="rounded-md bg-cyan-700 px-3 py-1.5 text-xs font-semibold text-white">New</span>
                </div>
            </summary>

            <form method="POST" action="{{ route('admin.blackout-dates.store') }}" class="mt-4 grid gap-3 lg:grid-cols-6">
                @csrf
                <input name="title" value="{{ old('title') }}" class="h-9 rounded-md border-slate-300 text-sm" placeholder="Title" required>
                <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm">
                    <option value="">All facilities</option>
                    @foreach ($facilities as $facility)
                        <option value="{{ $facility->id }}" @selected(old('facility_id') == $facility->id)>{{ $facility->name }}</option>
                    @endforeach
                </select>
                <input name="starts_at" type="date" value="{{ old('starts_at') }}" class="h-9 rounded-md border-slate-300 text-sm" required>
                <input name="ends_at" type="date" value="{{ old('ends_at') }}" class="h-9 rounded-md border-slate-300 text-sm" required>
                <input name="reason" value="{{ old('reason') }}" class="h-9 rounded-md border-slate-300 text-sm lg:col-span-2" placeholder="Reason">
                <button class="h-9 rounded-md bg-cyan-700 px-4 text-sm font-semibold text-white transition hover:bg-cyan-600 lg:col-span-6 lg:w-fit">Save Blackout</button>
            </form>
        </details>

    <details class="mt-3 rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200" {{ $filtersActive ? 'open' : '' }}>
        <summary class="cursor-pointer list-none">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-slate-950">Search and filters</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Filters apply to the calendar and table.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($filtersActive)
                        <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-700">Active</span>
                    @endif
                    <span class="text-xs font-semibold text-slate-500">Expand</span>
                </div>
            </div>
        </summary>

        <form class="mt-3 grid gap-3 lg:grid-cols-6">
            <div class="relative lg:col-span-2">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                <input name="search" value="{{ request('search') }}" class="h-9 w-full rounded-md border-slate-300 pl-9 pr-3 text-sm" placeholder="Search title, reason, facility">
            </div>
            <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm">
                <option value="">Any facility</option>
                <option value="all" @selected(request('facility_id') === 'all')>All facilities only</option>
                @foreach ($facilities as $facility)
                    <option value="{{ $facility->id }}" @selected(request('facility_id') == $facility->id)>{{ $facility->name }}</option>
                @endforeach
            </select>
            <input name="date_from" type="date" value="{{ request('date_from') }}" class="h-9 rounded-md border-slate-300 text-sm">
            <input name="date_to" type="date" value="{{ request('date_to') }}" class="h-9 rounded-md border-slate-300 text-sm">
            <div class="flex gap-2">
                <button class="h-9 flex-1 rounded-md bg-cyan-700 px-3 text-sm font-semibold text-white transition hover:bg-cyan-600">Apply</button>
                @if ($filtersActive)
                    <a href="{{ route('admin.blackout-dates.index') }}" class="inline-flex h-9 items-center rounded-md border border-slate-300 px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                @endif
            </div>
        </form>
    </details>

    <section class="mt-3 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3">
            <div>
                <h2 class="text-base font-bold text-slate-950">Blackout Windows</h2>
                <p class="mt-0.5 text-xs text-slate-500">Expand a row to edit.</p>
            </div>
            <div class="text-xs font-semibold text-slate-500">{{ $blackouts->total() }} result{{ $blackouts->total() === 1 ? '' : 's' }}</div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($blackouts as $blackout)
                @php
                    $status = $blackout->ends_at->lt(now()->startOfDay())
                        ? ['Past', 'bg-slate-100 text-slate-600']
                        : ($blackout->starts_at->gt(now()->endOfDay()) ? ['Upcoming', 'bg-cyan-50 text-cyan-700'] : ['Active', 'bg-amber-50 text-amber-700']);
                @endphp
                <details class="group">
                    <summary class="cursor-pointer list-none px-4 py-2.5 transition hover:bg-slate-50">
                        <div class="grid gap-2 lg:grid-cols-[minmax(0,1fr)_180px_150px_72px] lg:items-center">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate text-sm font-bold text-slate-950">{{ $blackout->title }}</h3>
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $status[1] }}">{{ $status[0] }}</span>
                                </div>
                                @if ($blackout->reason)
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $blackout->reason }}</p>
                                @endif
                            </div>
                            <div class="truncate text-sm text-slate-600">{{ $blackout->facility?->name ?? 'All facilities' }}</div>
                            <div class="text-xs font-semibold text-slate-500">{{ $blackout->starts_at->format('M d, Y') }} - {{ $blackout->ends_at->format('M d, Y') }}</div>
                            <div class="text-xs font-bold text-cyan-700">Edit</div>
                        </div>
                    </summary>

                    <div class="bg-slate-50 px-4 py-3">
                        <form method="POST" action="{{ route('admin.blackout-dates.update', $blackout) }}" class="grid gap-2 lg:grid-cols-6">
                            @csrf
                            @method('PATCH')
                            <input name="title" value="{{ $blackout->title }}" class="h-9 rounded-md border-slate-300 text-sm" required>
                            <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm">
                                <option value="">All facilities</option>
                                @foreach ($facilities as $facility)
                                    <option value="{{ $facility->id }}" @selected($blackout->facility_id === $facility->id)>{{ $facility->name }}</option>
                                @endforeach
                            </select>
                            <input name="starts_at" type="date" value="{{ $blackout->starts_at->format('Y-m-d') }}" class="h-9 rounded-md border-slate-300 text-sm" required>
                            <input name="ends_at" type="date" value="{{ $blackout->ends_at->format('Y-m-d') }}" class="h-9 rounded-md border-slate-300 text-sm" required>
                            <input name="reason" value="{{ $blackout->reason }}" class="h-9 rounded-md border-slate-300 text-sm lg:col-span-2" placeholder="Reason">
                            <button class="h-9 rounded-md bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800 lg:w-fit">Update</button>
                        </form>
                        <form method="POST" action="{{ route('admin.blackout-dates.destroy', $blackout) }}" class="mt-2" onsubmit="return confirm('Delete blackout window?')">
                            @csrf
                            @method('DELETE')
                            <button class="h-9 rounded-md border border-red-300 px-4 text-sm font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                        </form>
                    </div>
                </details>
            @empty
                <div class="p-8 text-center">
                    <h3 class="text-base font-bold text-slate-950">No blackout dates found</h3>
                    <p class="mt-1 text-sm text-slate-500">Create a blackout window or adjust the current filters.</p>
                </div>
            @endforelse
        </div>
    </section>

    <div class="mt-4">{{ $blackouts->links() }}</div>
        </div>
    </div>
@endsection
