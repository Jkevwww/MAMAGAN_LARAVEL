@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Blackout Dates</h1>
            <p class="mt-1 text-sm text-slate-500">Block dates for all facilities or specific bookable items.</p>
        </div>
    </div>

    <details class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200" open>
        <summary class="cursor-pointer font-semibold text-slate-950">Create blackout window</summary>
        <form method="POST" action="{{ route('admin.blackout-dates.store') }}" class="mt-4 grid gap-3 md:grid-cols-4">
            @csrf
            <input name="title" class="rounded-md border-gray-300 text-sm" placeholder="Title" required>
            <select name="facility_id" class="rounded-md border-gray-300 text-sm"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}">{{ $facility->name }}</option>@endforeach</select>
            <input name="starts_at" type="date" class="rounded-md border-gray-300 text-sm" required>
            <input name="ends_at" type="date" class="rounded-md border-gray-300 text-sm" required>
            <textarea name="reason" class="rounded-md border-gray-300 text-sm md:col-span-3" placeholder="Reason"></textarea>
            <button class="rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-600">Save Blackout</button>
        </form>
    </details>

    <form class="mt-6 flex flex-col items-end gap-2" x-data="{ filtersOpen: {{ request()->hasAny(['facility_id', 'date_from', 'date_to']) ? 'true' : 'false' }} }">
        <div class="inline-flex w-full items-center gap-1.5 rounded-lg bg-white p-1.5 shadow-sm ring-1 ring-slate-200 sm:w-auto">
            <div class="relative min-w-0 flex-1 sm:flex-none">
                <svg class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                <input name="search" value="{{ request('search') }}" class="h-8 w-full rounded-md border-slate-300 py-1 pl-8 pr-2 text-sm sm:w-72" placeholder="Search blackout dates">
            </div>
            <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="relative grid h-8 w-8 shrink-0 place-items-center rounded-md border border-slate-300 text-slate-700 transition hover:bg-slate-50" aria-label="Toggle filters">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/></svg>
                @if (request()->hasAny(['facility_id', 'date_from', 'date_to']))
                    <span class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-cyan-600 ring-2 ring-white"></span>
                @endif
            </button>
            <button class="h-8 rounded-md bg-cyan-700 px-3 text-sm font-semibold text-white transition hover:bg-cyan-600">Search</button>
        </div>
        <div x-show="filtersOpen" x-transition x-cloak class="w-full rounded-lg bg-white p-4 shadow-lg ring-1 ring-slate-200 sm:w-[560px]">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                <div><h2 class="text-sm font-bold text-slate-950">Filter blackout dates</h2><p class="mt-1 text-xs text-slate-500">Filter by facility and date range overlap.</p></div>
                <button type="button" data-no-loader="true" @click="filtersOpen = false" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Close filters"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <label class="grid gap-1 text-sm font-semibold text-slate-700">Facility
                    <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm font-normal"><option value="">Any facility</option><option value="all" @selected(request('facility_id') === 'all')>All facilities only</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}" @selected(request('facility_id') == $facility->id)>{{ $facility->name }}</option>@endforeach</select>
                </label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">From <input name="date_from" type="date" value="{{ request('date_from') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal"></label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">To <input name="date_to" type="date" value="{{ request('date_to') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal"></label>
            </div>
            <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('admin.blackout-dates.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                <button class="rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-600">Apply filters</button>
            </div>
        </div>
    </form>

    <div class="mt-6 grid gap-4">
        @forelse ($blackouts as $blackout)
            <details class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <summary class="cursor-pointer list-none">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div><h2 class="font-bold text-slate-950">{{ $blackout->title }}</h2><p class="mt-1 text-sm text-slate-500">{{ $blackout->facility?->name ?? 'All facilities' }} &middot; {{ $blackout->starts_at->format('Y-m-d') }} to {{ $blackout->ends_at->format('Y-m-d') }}</p></div>
                        <span class="w-fit rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700">Blocked</span>
                    </div>
                </summary>
                <form method="POST" action="{{ route('admin.blackout-dates.update', $blackout) }}" class="mt-4 grid gap-3 border-t border-slate-100 pt-4 md:grid-cols-4">
                    @csrf @method('PATCH')
                    <input name="title" value="{{ $blackout->title }}" class="rounded-md border-gray-300 text-sm" required>
                    <select name="facility_id" class="rounded-md border-gray-300 text-sm"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}" @selected($blackout->facility_id === $facility->id)>{{ $facility->name }}</option>@endforeach</select>
                    <input name="starts_at" type="date" value="{{ $blackout->starts_at->format('Y-m-d') }}" class="rounded-md border-gray-300 text-sm" required>
                    <input name="ends_at" type="date" value="{{ $blackout->ends_at->format('Y-m-d') }}" class="rounded-md border-gray-300 text-sm" required>
                    <textarea name="reason" class="rounded-md border-gray-300 text-sm md:col-span-3">{{ $blackout->reason }}</textarea>
                    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Update</button>
                </form>
                <form method="POST" action="{{ route('admin.blackout-dates.destroy', $blackout) }}" class="mt-3" onsubmit="return confirm('Delete blackout window?')">@csrf @method('DELETE') <button class="text-sm font-semibold text-red-700 hover:text-red-800">Delete blackout</button></form>
            </details>
        @empty
            <div class="rounded-xl bg-white p-8 text-center text-slate-500 shadow-sm ring-1 ring-slate-200">No blackout dates found.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $blackouts->links() }}</div>
@endsection
