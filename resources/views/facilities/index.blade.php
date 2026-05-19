<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Client View</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Facilities</h2>
                <p class="mt-1 text-sm text-slate-500">Find cottages, cabanas, rooms, and beach equipment available for booking.</p>
            </div>
            @auth
                <a href="{{ route('bookings.index') }}" class="inline-flex w-full justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800 md:w-auto">
                    My Bookings
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex w-full justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800 md:w-auto">
                    Log in to book
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <form class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200" x-data="{ filtersOpen: {{ request()->hasAny(['category', 'guest_count', 'min_price', 'max_price']) ? 'true' : 'false' }} }">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative min-w-0 flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                    </svg>
                    <input name="search" value="{{ request('search') }}" placeholder="Search by facility name" class="h-10 w-full rounded-md border-slate-300 pl-9 pr-3 text-sm">
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/>
                        </svg>
                        Filters
                        @if (request()->hasAny(['category', 'guest_count', 'min_price', 'max_price']))
                            <span class="h-2 w-2 rounded-full bg-teal-600"></span>
                        @endif
                    </button>
                    <button class="h-10 rounded-md bg-teal-700 px-5 text-sm font-bold text-white transition hover:bg-teal-600">Search</button>
                    @if (request()->hasAny(['search', 'category', 'guest_count', 'min_price', 'max_price']))
                        <a href="{{ route('facilities.index') }}" class="inline-flex h-10 items-center justify-center rounded-md px-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Reset</a>
                    @endif
                </div>
            </div>

            <div x-show="filtersOpen" x-transition x-cloak class="mt-4 border-t border-slate-100 pt-4">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="grid gap-1 text-sm font-bold text-slate-700">
                        Category
                        <select name="category" class="h-10 rounded-md border-slate-300 text-sm font-normal">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">
                        Guest capacity
                        <select name="guest_count" class="h-10 rounded-md border-slate-300 text-sm font-normal">
                            <option value="">Any capacity</option>
                            @foreach ($capacityOptions as $capacity)
                                <option value="{{ $capacity }}" @selected((string) request('guest_count') === (string) $capacity)>{{ $capacity }}+ guests</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">
                        Min price
                        <input name="min_price" type="number" min="0" step="50" value="{{ request('min_price') }}" placeholder="0" class="h-10 rounded-md border-slate-300 text-sm font-normal">
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">
                        Max price
                        <input name="max_price" type="number" min="0" step="50" value="{{ request('max_price') }}" placeholder="No limit" class="h-10 rounded-md border-slate-300 text-sm font-normal">
                    </label>
                </div>
            </div>
        </form>

        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($facilities as $facility)
                <article class="group flex overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex w-full flex-col">
                        <div class="aspect-[16/10] bg-slate-200">
                            @if ($facility->image)
                                <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center bg-teal-700 px-4 text-center text-lg font-extrabold text-white">{{ $facility->category }}</div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate font-extrabold text-slate-950">{{ $facility->name }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $facility->category }} &middot; {{ ucfirst($facility->rental_type) }} &middot; {{ $facility->capacity }} guests</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">{{ $facility->inventory_count }} left</span>
                            </div>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $facility->description ?: 'No description provided.' }}</p>
                            <div class="mt-4 flex items-center justify-between gap-3 text-sm">
                                <span class="font-extrabold text-slate-950">&#8369;{{ number_format($facility->price_min, 2) }}</span>
                                <span class="font-bold text-amber-600">&#9733; {{ number_format($facility->reviews_avg_rating ?? 0, 1) }} <span class="font-semibold text-slate-500">({{ $facility->reviews_count }})</span></span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="{{ route('facilities.show', $facility) }}" class="rounded-md border border-slate-300 px-3 py-2 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-50">Details</a>
                                <a href="{{ route('bookings.create', $facility) }}" class="rounded-md bg-teal-700 px-3 py-2 text-center text-sm font-bold text-white transition hover:bg-teal-600">Book</a>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-slate-200 sm:col-span-2 lg:col-span-3">
                    <h3 class="text-lg font-extrabold text-slate-950">No facilities found</h3>
                    <p class="mt-2 text-sm text-slate-500">Try removing filters or searching for a different facility name.</p>
                    <a href="{{ route('facilities.index') }}" class="mt-5 inline-flex rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-teal-600">View all facilities</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $facilities->links() }}</div>
    </div>
</x-app-layout>
