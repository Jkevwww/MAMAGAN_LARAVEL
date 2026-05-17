<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Facilities</h2>
            <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-cyan-700">My Bookings</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
        <form class="mb-6 grid gap-3 rounded-lg bg-white p-4 shadow-sm md:grid-cols-3">
            <input name="search" value="{{ request('search') }}" placeholder="Search facilities" class="rounded-md border-gray-300">
            <select name="category" class="rounded-md border-gray-300">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Filter</button>
        </form>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($facilities as $facility)
                <article class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="aspect-[16/10] bg-slate-200">
                        @if ($facility->image)
                            <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center bg-cyan-700 text-white">{{ $facility->category }}</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold">{{ $facility->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $facility->category }} · {{ $facility->rental_type }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2 py-1 text-xs text-emerald-700">{{ $facility->inventory_count }} available</span>
                        </div>
                        <p class="mt-3 line-clamp-2 text-sm text-gray-600">{{ $facility->description }}</p>
                        <div class="mt-4 flex items-center justify-between text-sm">
                            <span class="font-semibold">₱{{ number_format($facility->price_min, 2) }} - ₱{{ number_format($facility->price_max, 2) }}</span>
                            <span>★ {{ number_format($facility->reviews_avg_rating ?? 0, 1) }} ({{ $facility->reviews_count }})</span>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('facilities.show', $facility) }}" class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-center text-sm font-semibold">Details</a>
                            <a href="{{ route('bookings.create', $facility) }}" class="flex-1 rounded-md bg-cyan-700 px-3 py-2 text-center text-sm font-semibold text-white">Book</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg bg-white p-8 text-center text-gray-600 sm:col-span-2 lg:col-span-3">No facilities found.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $facilities->links() }}</div>
    </div>
</x-app-layout>
