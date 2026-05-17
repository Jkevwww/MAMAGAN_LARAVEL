<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $facility->name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $facility->category }} &middot; {{ $facility->rental_type }}</p>
            </div>
            <a href="{{ route('facilities.index') }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">Back to facilities</a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-6xl p-4 sm:p-6 lg:p-8">
        <div class="grid gap-6 lg:grid-cols-[1.3fr_.7fr]">
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="aspect-[16/9] bg-slate-200">
                    @if ($facility->image)
                        <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center bg-cyan-700 text-2xl font-bold text-white">{{ $facility->category }}</div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{{ $facility->category }}</span>
                        <span class="rounded-full bg-cyan-50 px-3 py-1 text-sm font-semibold text-cyan-700">{{ ucfirst($facility->rental_type) }}</span>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">{{ $facility->inventory_count }} available</span>
                    </div>
                    <p class="mt-4 leading-7 text-gray-700">{{ $facility->description ?: 'No description provided.' }}</p>
                </div>
            </div>
            <aside class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="text-2xl font-bold">&#8369;{{ number_format($facility->price_min, 2) }}</div>
                <p class="text-sm text-gray-500">Price range up to &#8369;{{ number_format($facility->price_max, 2) }}</p>
                <dl class="mt-5 grid gap-3 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Capacity</dt><dd class="font-semibold">{{ $facility->capacity }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Inventory</dt><dd class="font-semibold">{{ $facility->inventory_count }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Rating</dt><dd class="font-semibold text-amber-600">&#9733; {{ number_format($facility->averageRating(), 1) }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Reviews</dt><dd class="font-semibold">{{ $facility->reviews->count() }}</dd></div>
                </dl>
                @if ($facility->is_active && $facility->is_bookable)
                    <a href="{{ route('bookings.create', $facility) }}" class="mt-5 block rounded-lg bg-cyan-700 px-4 py-3 text-center font-semibold text-white transition hover:bg-cyan-600">Book this facility</a>
                @else
                    <div class="mt-5 rounded-lg bg-amber-50 p-4 text-sm text-amber-900">This facility is currently unavailable for booking.</div>
                @endif
            </aside>
        </div>

        <section class="mt-8 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h3 class="font-semibold">Reviews</h3>
            <div class="mt-4 space-y-4">
                @forelse ($facility->reviews->sortByDesc('created_at')->take(8) as $review)
                    <div class="border-b border-slate-100 pb-4">
                        <div class="flex justify-between gap-3 text-sm">
                            <strong>{{ $review->user->name }}</strong>
                            <span class="text-amber-600">&#9733; {{ $review->rating }}</span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-gray-700">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No reviews yet.</p>
                @endforelse
            </div>
            @auth
                <form method="POST" action="{{ route('facilities.reviews.store', $facility) }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-lg bg-slate-50 p-4">
                    @csrf
                    <select name="rating" class="rounded-lg border-gray-300" required>
                        <option value="">Rate this facility</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                    <textarea name="comment" rows="3" class="rounded-lg border-gray-300" placeholder="Comment"></textarea>
                    <input type="file" name="media[]" multiple class="rounded-lg border border-gray-300 bg-white p-2">
                    <button class="w-fit rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">Submit Review</button>
                </form>
            @else
                <div class="mt-6 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                    Log in after booking to leave a review.
                </div>
            @endauth
        </section>
    </div>
</x-app-layout>
