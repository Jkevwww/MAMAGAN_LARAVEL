@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Facilities</h1>
            <p class="mt-1 text-sm text-slate-500">Create, update, deactivate, block booking, and delete unused facilities.</p>
        </div>
        <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
            New Facility
        </a>
    </div>

    <form class="mt-6 rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200" x-data="{ filtersOpen: {{ request('category') || request('status') ? 'true' : 'false' }} }">
        <div class="flex gap-2">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                </svg>
                <input name="search" value="{{ request('search') }}" class="w-full rounded-lg border-slate-300 pl-10" placeholder="Search facilities">
            </div>
            <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-300 transition hover:bg-slate-50" aria-label="Toggle filters">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/></svg>
            </button>
            <button class="rounded-lg bg-cyan-700 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600">Search</button>
        </div>
        <div x-show="filtersOpen" x-transition x-cloak class="mt-3 grid gap-3 border-t border-slate-100 pt-3 md:grid-cols-3">
            <select name="category" class="rounded-lg border-slate-300">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border-slate-300">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                <option value="bookable" @selected(request('status') === 'bookable')>Bookable</option>
                <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
            </select>
            <a href="{{ route('admin.facilities.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="mt-6 grid gap-4">
        @forelse ($facilities as $facility)
            <article class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <div class="grid gap-4 p-4 lg:grid-cols-[180px_1fr_auto] lg:items-center">
                    <div class="aspect-[16/10] overflow-hidden rounded-lg bg-slate-100">
                        @if ($facility->image)
                            <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="grid h-full place-items-center bg-cyan-700 text-sm font-semibold text-white">{{ $facility->category }}</div>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="truncate text-lg font-bold text-slate-950">{{ $facility->name }}</h2>
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $facility->category }}</span>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $facility->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $facility->is_active ? 'Active' : 'Inactive' }}</span>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $facility->is_bookable ? 'bg-cyan-50 text-cyan-700' : 'bg-amber-50 text-amber-700' }}">{{ $facility->is_bookable ? 'Bookable' : 'Blocked' }}</span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $facility->description ?: 'No description provided.' }}</p>
                        <div class="mt-3 grid gap-2 text-sm sm:grid-cols-4">
                            <div><span class="text-slate-500">Rate</span><br><strong>&#8369;{{ number_format($facility->price_min, 2) }} - &#8369;{{ number_format($facility->price_max, 2) }}</strong></div>
                            <div><span class="text-slate-500">Capacity</span><br><strong>{{ $facility->capacity }}</strong></div>
                            <div><span class="text-slate-500">Inventory</span><br><strong>{{ $facility->inventory_count }}</strong></div>
                            <div><span class="text-slate-500">Usage</span><br><strong>{{ $facility->bookings_count }} bookings &middot; {{ number_format($facility->reviews_avg_rating ?? 0, 1) }} stars</strong></div>
                        </div>
                    </div>

                    <div class="flex gap-2 lg:flex-col lg:items-stretch">
                        <a href="{{ route('admin.facilities.edit', $facility) }}" class="rounded-lg bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-slate-800">Edit</a>
                        <a href="{{ route('facilities.show', $facility) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">View</a>
                        <form method="POST" action="{{ route('admin.facilities.destroy', $facility) }}" onsubmit="return confirm('Delete this facility? Facilities with bookings cannot be deleted.')">
                            @csrf @method('DELETE')
                            <button class="w-full rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl bg-white p-8 text-center text-slate-500 shadow-sm ring-1 ring-slate-200">No facilities found.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $facilities->links() }}</div>
@endsection
