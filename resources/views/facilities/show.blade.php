<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">{{ $facility->category }}</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">{{ $facility->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ ucfirst($facility->rental_type) }} rental &middot; {{ $facility->capacity }} guest capacity</p>
            </div>
            <a href="{{ route('facilities.index') }}" class="inline-flex w-full justify-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-stone-50 md:w-auto">
                Back to Facilities
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <section class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-stone-200">
                <div class="aspect-[16/9] bg-slate-200">
                    @if ($facility->image)
                        <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center bg-teal-800 px-6 text-center text-3xl font-extrabold text-white">{{ $facility->category }}</div>
                    @endif
                </div>
                <div class="p-5 sm:p-6">
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-sm font-bold text-slate-700">{{ $facility->category }}</span>
                        <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-bold text-teal-700">{{ ucfirst($facility->rental_type) }}</span>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700">{{ $facility->inventory_count }} available</span>
                    </div>
                    <h3 class="mt-5 text-lg font-extrabold text-slate-950">Overview</h3>
                    <p class="mt-2 max-w-3xl leading-7 text-slate-600">{{ $facility->description ?: 'No description provided.' }}</p>
                </div>
            </section>

            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-stone-200">
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500">Starting Rate</p>
                    <div class="mt-2 text-3xl font-extrabold text-slate-950">&#8369;{{ number_format($facility->price_min, 2) }}</div>
                    <p class="mt-1 text-sm text-slate-500">Up to &#8369;{{ number_format($facility->price_max, 2) }} depending on rate rules.</p>

                    <dl class="mt-5 grid gap-3 text-sm">
                        <div class="flex items-center justify-between gap-3 rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Capacity</dt>
                            <dd class="font-extrabold text-slate-950">{{ $facility->capacity }} guests</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Inventory</dt>
                            <dd class="font-extrabold text-slate-950">{{ $facility->inventory_count }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Rating</dt>
                            <dd class="font-extrabold text-amber-600">&#9733; {{ number_format($facility->averageRating(), 1) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Reviews</dt>
                            <dd class="font-extrabold text-slate-950">{{ $facility->reviews->count() }}</dd>
                        </div>
                    </dl>

                    @if ($facility->is_active && $facility->is_bookable)
                        <a href="{{ route('bookings.create', $facility) }}" class="mt-5 block rounded-md bg-teal-700 px-4 py-3 text-center font-bold text-white transition hover:bg-teal-600">
                            Book This Facility
                        </a>
                    @else
                        <div class="mt-5 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900">This facility is currently unavailable for booking.</div>
                    @endif
                </div>
            </aside>
        </div>

        <section class="mt-6 rounded-lg bg-white p-5 shadow-sm ring-1 ring-stone-200 sm:p-6">
            <div class="flex flex-col gap-2 border-b border-stone-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-950">Guest Reviews</h3>
                    <p class="mt-1 text-sm text-slate-500">Recent feedback from resort guests.</p>
                </div>
                <div class="text-sm font-bold text-amber-600">&#9733; {{ number_format($facility->averageRating(), 1) }}</div>
            </div>

            <div class="mt-5 grid gap-4">
                @forelse ($facility->reviews->sortByDesc('created_at')->take(8) as $review)
                    <article class="border-b border-stone-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <strong class="text-sm text-slate-950">{{ $review->user->name }}</strong>
                            <span class="text-sm font-bold text-amber-600">&#9733; {{ $review->rating }}</span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $review->comment ?: 'No written comment.' }}</p>
                    </article>
                @empty
                    <div class="rounded-md bg-stone-50 p-4 text-sm text-slate-600">No reviews yet.</div>
                @endforelse
            </div>

            @auth
                <form method="POST" action="{{ route('facilities.reviews.store', $facility) }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-lg border border-stone-200 bg-stone-50 p-4">
                    @csrf
                    <div class="grid gap-3 sm:grid-cols-[180px_1fr]">
                        <select name="rating" class="h-10 rounded-md border-stone-300 text-sm" required>
                            <option value="">Rating</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        <input type="file" name="media[]" multiple class="rounded-md border border-stone-300 bg-white p-2 text-sm">
                    </div>
                    <textarea name="comment" rows="3" class="rounded-md border-stone-300 text-sm" placeholder="Share your experience"></textarea>
                    <button class="w-full rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800 sm:w-fit">Submit Review</button>
                </form>
            @else
                <div class="mt-6 rounded-md bg-stone-50 p-4 text-sm text-slate-600">
                    Log in after booking to leave a review.
                </div>
            @endauth
        </section>
    </div>
</x-app-layout>
