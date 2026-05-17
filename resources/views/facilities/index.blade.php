<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Facilities</h2>
                <p class="mt-1 text-sm text-gray-500">Browse cottages, cabanas, rooms, and beach equipment.</p>
            </div>
            @auth
                <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">My Bookings</a>
            @endauth
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8" x-data="{ filtersOpen: {{ request('category') ? 'true' : 'false' }} }">
        <form class="mb-6 rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200 transition duration-200">
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                    </svg>
                    <input name="search" value="{{ request('search') }}" placeholder="Search facilities" class="w-full rounded-lg border-slate-300 pl-10 pr-3">
                </div>
                <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-slate-300 text-slate-700 transition hover:bg-slate-50" aria-label="Toggle filters">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/>
                    </svg>
                </button>
                <button class="rounded-lg bg-cyan-700 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600">Search</button>
            </div>
            <div x-show="filtersOpen" x-transition x-cloak class="mt-3 grid gap-3 border-t border-slate-100 pt-3 md:grid-cols-[1fr_auto]">
                <select name="category" class="rounded-lg border-slate-300">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                <a href="{{ route('facilities.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
            </div>
        </form>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($facilities as $facility)
                <article class="group overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="aspect-[16/10] bg-slate-200">
                        @if ($facility->image)
                            <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center bg-cyan-700 text-white">{{ $facility->category }}</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-slate-950">{{ $facility->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $facility->category }} &middot; {{ $facility->rental_type }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">{{ $facility->inventory_count }} available</span>
                        </div>
                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-gray-600">{{ $facility->description }}</p>
                        <div class="mt-4 flex items-center justify-between gap-3 text-sm">
                            <span class="font-semibold">&#8369;{{ number_format($facility->price_min, 2) }} - &#8369;{{ number_format($facility->price_max, 2) }}</span>
                            <span class="text-amber-600">&#9733; {{ number_format($facility->reviews_avg_rating ?? 0, 1) }} <span class="text-slate-500">({{ $facility->reviews_count }})</span></span>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('facilities.show', $facility) }}" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-center text-sm font-semibold transition hover:bg-slate-50">Details</a>
                            <a href="{{ route('bookings.create', $facility) }}" class="flex-1 rounded-lg bg-cyan-700 px-3 py-2 text-center text-sm font-semibold text-white transition hover:bg-cyan-600">Book</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-xl bg-white p-8 text-center text-gray-600 shadow-sm ring-1 ring-slate-200 sm:col-span-2 lg:col-span-3">No facilities found.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $facilities->links() }}</div>
    </div>
</x-app-layout>
