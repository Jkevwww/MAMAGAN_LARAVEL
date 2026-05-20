@extends('layouts.admin')

@section('content')
    @php
        $activeFilters = collect(['search', 'facility_id', 'date_from', 'date_to'])->filter(fn ($key) => filled(request($key)));
        $filtersOpen = $activeFilters->isNotEmpty() ? 'true' : 'false';
        $today = now()->startOfDay();
        $summaryCards = [
            ['label' => 'Rates', 'value' => number_format($summary['total']), 'caption' => 'Matching filters', 'tone' => 'bg-cyan-50 text-cyan-700 ring-cyan-100', 'icon' => 'M4 19V5M4 19h16M8 17v-6M12 17V7M16 17v-9'],
            ['label' => 'Active', 'value' => number_format($summary['active']), 'caption' => 'Running today', 'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'icon' => 'm9 12 2 2 4-4M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z'],
            ['label' => 'Upcoming', 'value' => number_format($summary['upcoming']), 'caption' => 'Scheduled ahead', 'tone' => 'bg-indigo-50 text-indigo-700 ring-indigo-100', 'icon' => 'M12 6v6l4 2M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z'],
            ['label' => 'Avg. Price', 'value' => '&#8369;'.number_format($summary['average_price'], 2), 'caption' => number_format($summary['expired']).' expired', 'tone' => 'bg-slate-100 text-slate-700 ring-slate-200', 'icon' => 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6'],
        ];
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Revenue</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Seasonal Rates</h1>
            <p class="mt-1 text-sm text-slate-500">Set temporary facility prices for holidays, peak dates, and special periods.</p>
        </div>
        <a href="#create-rate" class="inline-flex w-fit items-center gap-2 rounded-lg bg-cyan-700 px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
            New Rate
        </a>
    </div>

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <p class="font-bold">Please fix the seasonal rate details.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
        <section id="create-rate" class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-bold text-slate-950">Create Rate</h2>
                    <p class="mt-1 text-xs text-slate-500">Add one seasonal price for one facility and date range.</p>
                </div>
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-cyan-50 text-cyan-700 ring-1 ring-cyan-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                </span>
            </div>
            <form method="POST" action="{{ route('admin.seasonal-rates.store') }}" class="mt-4 grid gap-3">
                @csrf
                <label class="grid gap-1 text-sm font-bold text-slate-700">Facility
                    <select name="facility_id" class="h-10 rounded-lg border-slate-300 text-sm font-normal" required>
                        <option value="">Select facility</option>
                        @foreach ($facilities as $facility)
                            <option value="{{ $facility->id }}" @selected(old('facility_id') == $facility->id)>{{ $facility->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-1 text-sm font-bold text-slate-700">Season name
                    <input name="name" value="{{ old('name') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" placeholder="Summer peak rate" required>
                </label>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Starts
                        <input name="starts_at" type="date" value="{{ old('starts_at') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" required>
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Ends
                        <input name="ends_at" type="date" value="{{ old('ends_at') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" required>
                    </label>
                </div>
                <label class="grid gap-1 text-sm font-bold text-slate-700">Seasonal price
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">&#8369;</span>
                        <input name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" class="h-10 w-full rounded-lg border-slate-300 pl-8 text-sm font-normal" placeholder="0.00" required>
                    </div>
                </label>
                <button class="mt-1 inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-cyan-700 px-4 text-sm font-bold text-white transition hover:bg-cyan-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2ZM17 21v-8H7v8M7 3v5h8"/></svg>
                    Save Rate
                </button>
            </form>
        </section>

        <div class="grid content-start gap-6">
            <form class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200" x-data="{ filtersOpen: {{ $filtersOpen }} }">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row">
                        <div class="relative min-w-0 flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                            <input name="search" value="{{ request('search') }}" class="h-10 w-full rounded-lg border-slate-300 pl-9 pr-3 text-sm" placeholder="Search rate, facility, or category">
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
                            <a href="{{ route('admin.seasonal-rates.index') }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Reset</a>
                        @endif
                    </div>
                </div>

                <div x-show="filtersOpen" x-transition x-cloak class="mt-4 border-t border-slate-100 pt-4">
                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        <label class="grid gap-1 text-sm font-bold text-slate-700 xl:col-span-2">Facility
                            <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                                <option value="">All facilities</option>
                                @foreach ($facilities as $facility)
                                    <option value="{{ $facility->id }}" @selected(request('facility_id') == $facility->id)>{{ $facility->name }}</option>
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

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($summaryCards as $card)
                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                                <p class="mt-2 truncate text-2xl font-extrabold text-slate-950">{!! $card['value'] !!}</p>
                            </div>
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg ring-1 {{ $card['tone'] }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">{{ $card['caption'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <section class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-2 border-b border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-slate-950">Rate Schedule</h2>
                <p class="text-xs text-slate-500">Showing 5 compact rows per page. Open a row to edit.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Page {{ $rates->currentPage() }} of {{ $rates->lastPage() }}</span>
        </div>

        <div class="grid gap-2 p-3">
            @forelse ($rates as $rate)
                @php
                    $rateStatus = $rate->ends_at->lt($today) ? 'expired' : ($rate->starts_at->gt($today) ? 'upcoming' : 'active');
                    $statusClasses = [
                        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                        'upcoming' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
                        'expired' => 'bg-slate-100 text-slate-600 ring-slate-200',
                    ][$rateStatus];
                @endphp
                <details class="group rounded-lg border border-slate-200 bg-white px-3 py-2.5 transition open:border-cyan-200 open:bg-cyan-50/30">
                    <summary class="cursor-pointer list-none">
                        <div class="grid gap-2 lg:grid-cols-[minmax(0,1fr)_210px_130px_70px_20px] lg:items-center">
                            <div class="min-w-0">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-bold ring-1 {{ $statusClasses }}">{{ ucfirst($rateStatus) }}</span>
                                    <h3 class="truncate text-sm font-extrabold text-slate-950">{{ $rate->name }}</h3>
                                </div>
                                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $rate->facility->name }} &middot; {{ $rate->facility->category }}</p>
                            </div>
                            <div class="text-xs font-semibold text-slate-600">
                                {{ $rate->starts_at->format('M d, Y') }} - {{ $rate->ends_at->format('M d, Y') }}
                            </div>
                            <div class="text-sm">
                                <p class="font-extrabold text-slate-950">&#8369;{{ number_format($rate->price, 2) }}</p>
                            </div>
                            <div class="text-xs font-semibold text-slate-500">
                                {{ $rate->starts_at->diffInDays($rate->ends_at) + 1 }} days
                            </div>
                            <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180 group-open:text-cyan-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </summary>

                    <div class="mt-3 border-t border-slate-200 pt-3">
                        <form method="POST" action="{{ route('admin.seasonal-rates.update', $rate) }}" class="grid gap-2 lg:grid-cols-12 lg:items-end">
                            @csrf
                            @method('PATCH')
                            <label class="grid gap-1 text-xs font-bold text-slate-700 lg:col-span-3">Facility
                                <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm font-normal" required>
                                    @foreach ($facilities as $facility)
                                        <option value="{{ $facility->id }}" @selected($rate->facility_id === $facility->id)>{{ $facility->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700 lg:col-span-3">Season
                                <input name="name" value="{{ $rate->name }}" class="h-9 rounded-md border-slate-300 text-sm font-normal" required>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700 lg:col-span-2">Starts
                                <input name="starts_at" type="date" value="{{ $rate->starts_at->format('Y-m-d') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal" required>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700 lg:col-span-2">Ends
                                <input name="ends_at" type="date" value="{{ $rate->ends_at->format('Y-m-d') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal" required>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700">Price
                                <input name="price" type="number" step="0.01" min="0" value="{{ $rate->price }}" class="h-9 rounded-md border-slate-300 text-sm font-normal" required>
                            </label>
                            <button class="inline-flex h-9 items-center justify-center rounded-md bg-slate-950 px-3 text-sm font-bold text-white transition hover:bg-slate-800">Update</button>
                        </form>
                        <form method="POST" action="{{ route('admin.seasonal-rates.destroy', $rate) }}" class="mt-2 flex justify-end" onsubmit="return confirm('Delete seasonal rate?')">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex h-8 items-center gap-2 rounded-md border border-rose-200 bg-white px-3 text-xs font-bold text-rose-700 transition hover:bg-rose-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
                                Delete Rate
                            </button>
                        </form>
                    </div>
                </details>
            @empty
                <div class="rounded-xl bg-slate-50 p-8 text-center text-sm text-slate-500">No seasonal rates found.</div>
            @endforelse
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-slate-500">
                @if ($rates->total())
                    Showing {{ $rates->firstItem() }}-{{ $rates->lastItem() }} of {{ $rates->total() }}
                @else
                    Showing 0 of 0
                @endif
            </p>
            <div class="flex items-center gap-2">
                @if ($rates->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                        Previous
                    </span>
                @else
                    <a href="{{ $rates->previousPageUrl() }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                        Previous
                    </a>
                @endif

                <span class="inline-flex h-9 items-center rounded-lg bg-slate-100 px-3 text-sm font-bold text-slate-600">
                    Page {{ $rates->currentPage() }} of {{ $rates->lastPage() }}
                </span>

                @if ($rates->hasMorePages())
                    <a href="{{ $rates->nextPageUrl() }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-cyan-700 px-3 text-sm font-bold text-white transition hover:bg-cyan-600">
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
@endsection
