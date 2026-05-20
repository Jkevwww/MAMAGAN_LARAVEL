<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Client View</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Facilities</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Compare cottages, cabanas, rooms, and resort equipment before creating a booking.</p>
            </div>
            <div class="grid grid-cols-2 gap-2 text-sm sm:grid-cols-4 lg:min-w-[520px]">
                <div class="rounded-lg bg-stone-50 p-3 ring-1 ring-stone-200">
                    <div class="font-extrabold text-slate-950">{{ number_format($stats['facility_count']) }}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Facilities</div>
                </div>
                <div class="rounded-lg bg-stone-50 p-3 ring-1 ring-stone-200">
                    <div class="font-extrabold text-slate-950">{{ number_format($stats['category_count']) }}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Categories</div>
                </div>
                <div class="rounded-lg bg-stone-50 p-3 ring-1 ring-stone-200">
                    <div class="font-extrabold text-slate-950">{{ number_format($stats['available_units']) }}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Units</div>
                </div>
                <div class="rounded-lg bg-stone-50 p-3 ring-1 ring-stone-200">
                    <div class="font-extrabold text-slate-950">{{ number_format($stats['guest_capacity']) }}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Capacity</div>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $filterKeys = ['search', 'category', 'guest_count', 'min_price', 'max_price', 'sort'];
        $filtersActive = collect($filterKeys)->contains(fn ($key) => filled(request($key)) && ! ($key === 'sort' && request($key) === 'featured'));
        $resultStart = $facilities->total() ? $facilities->firstItem() : 0;
        $resultEnd = $facilities->total() ? $facilities->lastItem() : 0;
        $allCategoryQuery = request()->except(['category', 'page']);
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <form class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5" x-data="{ filtersOpen: {{ request()->hasAny(['category', 'guest_count', 'min_price', 'max_price']) ? 'true' : 'false' }} }">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto] lg:items-center">
                <label class="relative min-w-0">
                    <span class="sr-only">Search facilities</span>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                    </svg>
                    <input name="search" value="{{ request('search') }}" placeholder="Search by facility name" class="h-11 w-full rounded-md border-slate-300 pl-9 pr-3 text-sm">
                </label>

                <label>
                    <span class="sr-only">Sort facilities</span>
                    <select name="sort" class="h-11 w-full rounded-md border-slate-300 text-sm">
                        <option value="featured" @selected($sort === 'featured')>Featured order</option>
                        <option value="price_low" @selected($sort === 'price_low')>Lowest price</option>
                        <option value="price_high" @selected($sort === 'price_high')>Highest price</option>
                        <option value="capacity" @selected($sort === 'capacity')>Largest capacity</option>
                        <option value="rating" @selected($sort === 'rating')>Top rated</option>
                    </select>
                </label>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="inline-flex h-11 items-center justify-center gap-2 rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/>
                        </svg>
                        Filters
                        @if ($filtersActive)
                            <span class="h-2 w-2 rounded-full bg-teal-600"></span>
                        @endif
                    </button>
                    <button class="h-11 rounded-md bg-teal-700 px-5 text-sm font-bold text-white transition hover:bg-teal-600">Apply</button>
                    @if ($filtersActive)
                        <a href="{{ route('facilities.index') }}" class="inline-flex h-11 items-center justify-center rounded-md px-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Reset</a>
                    @endif
                </div>
            </div>

            <div x-show="filtersOpen" x-transition x-cloak class="mt-5 border-t border-slate-100 pt-5">
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

        <section class="mt-5">
            <div class="flex gap-2 overflow-x-auto pb-1">
                <a href="{{ route('facilities.index', $allCategoryQuery) }}" class="inline-flex shrink-0 items-center gap-2 rounded-md px-3 py-2 text-sm font-bold transition {{ request('category') ? 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' : 'bg-slate-950 text-white' }}">
                    All
                    <span class="rounded bg-white/15 px-1.5 py-0.5 text-xs">{{ number_format($stats['facility_count']) }}</span>
                </a>
                @foreach ($categoryStats as $category)
                    @php
                        $categoryQuery = array_merge(request()->except(['category', 'page']), ['category' => $category->category]);
                        $isActiveCategory = request('category') === $category->category;
                    @endphp
                    <a href="{{ route('facilities.index', $categoryQuery) }}" class="inline-flex shrink-0 items-center gap-2 rounded-md px-3 py-2 text-sm font-bold transition {{ $isActiveCategory ? 'bg-teal-700 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                        {{ $category->category }}
                        <span class="rounded px-1.5 py-0.5 text-xs {{ $isActiveCategory ? 'bg-white/15 text-white' : 'bg-stone-100 text-slate-500' }}">{{ $category->facility_count }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-slate-600">
                Showing {{ number_format($resultStart) }}-{{ number_format($resultEnd) }} of {{ number_format($facilities->total()) }} facilities
            </p>
            @if (request('category'))
                <p class="text-sm text-slate-500">Filtered to <span class="font-bold text-slate-700">{{ request('category') }}</span></p>
            @endif
        </div>

        <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($facilities as $facility)
                @php
                    $rating = number_format($facility->reviews_avg_rating ?? 0, 1);
                    $rateLabel = $facility->price_min == $facility->price_max
                        ? '&#8369;'.number_format($facility->price_min, 2)
                        : '&#8369;'.number_format($facility->price_min, 2).' - &#8369;'.number_format($facility->price_max, 2);
                    $facilityImageUrl = $facility->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($facility->image)
                        ? route('media.public', ['path' => $facility->image])
                        : null;
                @endphp
                <article class="group overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="relative aspect-[16/10] overflow-hidden bg-slate-200">
                        @if ($facilityImageUrl)
                            <img src="{{ $facilityImageUrl }}" alt="{{ $facility->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center bg-teal-800 px-4 text-center text-xl font-extrabold text-white">{{ $facility->category }}</div>
                        @endif
                        <div class="absolute inset-x-0 top-0 flex items-start justify-between gap-2 p-3">
                            <span class="rounded-md bg-white/95 px-2.5 py-1 text-xs font-extrabold text-slate-800 shadow-sm">{{ $facility->category }}</span>
                            <span class="rounded-md bg-amber-400 px-2.5 py-1 text-xs font-extrabold text-slate-950 shadow-sm">&#9733; {{ $rating }}</span>
                        </div>
                    </div>

                    <div class="flex min-h-[276px] flex-col p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-base font-extrabold text-slate-950">{{ $facility->name }}</h3>
                                <p class="mt-1 text-sm font-semibold text-slate-500">{{ ucfirst($facility->rental_type) }} rental</p>
                            </div>
                            <span class="shrink-0 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">{{ $facility->inventory_count }} left</span>
                        </div>

                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $facility->description ?: 'No description provided.' }}</p>

                        <dl class="mt-4 grid grid-cols-2 gap-2 text-sm">
                            <div class="rounded-md bg-stone-50 p-3">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Capacity</dt>
                                <dd class="mt-1 font-extrabold text-slate-950">{{ $facility->capacity }} guests</dd>
                            </div>
                            <div class="rounded-md bg-stone-50 p-3">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Reviews</dt>
                                <dd class="mt-1 font-extrabold text-slate-950">{{ $facility->reviews_count ?: 'No' }} {{ $facility->reviews_count === 1 ? 'review' : 'reviews' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-auto pt-4">
                            <div class="flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Rate</p>
                                    <p class="mt-1 text-lg font-extrabold text-slate-950">{!! $rateLabel !!}</p>
                                </div>
                                <span class="text-right text-xs font-semibold leading-5 text-slate-500">Final total is checked during booking.</span>
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
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-md bg-teal-50 text-teal-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-extrabold text-slate-950">No facilities found</h3>
                    <p class="mt-2 text-sm text-slate-500">Try removing filters or searching for a different facility name.</p>
                    <a href="{{ route('facilities.index') }}" class="mt-5 inline-flex rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-teal-600">View all facilities</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $facilities->links() }}</div>
    </div>
</x-app-layout>
