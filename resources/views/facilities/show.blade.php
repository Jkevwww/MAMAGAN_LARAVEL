<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">{{ $facility->name }}</h2></x-slot>
    <div class="mx-auto max-w-6xl p-4 sm:p-6 lg:p-8">
        <div class="grid gap-6 lg:grid-cols-[1.3fr_.7fr]">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="aspect-[16/9] bg-slate-200">
                    @if ($facility->image)
                        <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center bg-cyan-700 text-2xl font-bold text-white">{{ $facility->category }}</div>
                    @endif
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-500">{{ $facility->category }} · Capacity {{ $facility->capacity }} · {{ $facility->inventory_count }} inventory</p>
                    <p class="mt-4 text-gray-700">{{ $facility->description }}</p>
                </div>
            </div>
            <aside class="rounded-lg bg-white p-5 shadow-sm">
                <div class="text-2xl font-bold">₱{{ number_format($facility->price_min, 2) }}</div>
                <p class="text-sm text-gray-500">Price range up to ₱{{ number_format($facility->price_max, 2) }}</p>
                <a href="{{ route('bookings.create', $facility) }}" class="mt-5 block rounded-md bg-cyan-700 px-4 py-3 text-center font-semibold text-white">Book this facility</a>
                <div class="mt-5 rounded-md bg-slate-50 p-3 text-sm">
                    <strong>Rating:</strong> ★ {{ number_format($facility->averageRating(), 1) }} · {{ $facility->reviews->count() }} reviews
                </div>
            </aside>
        </div>

        <section class="mt-8 rounded-lg bg-white p-5 shadow-sm">
            <h3 class="font-semibold">Reviews</h3>
            <div class="mt-4 space-y-4">
                @forelse ($facility->reviews->sortByDesc('created_at')->take(8) as $review)
                    <div class="border-b pb-4">
                        <div class="flex justify-between text-sm">
                            <strong>{{ $review->user->name }}</strong>
                            <span>★ {{ $review->rating }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-700">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No reviews yet.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('facilities.reviews.store', $facility) }}" enctype="multipart/form-data" class="mt-6 grid gap-3">
                @csrf
                <select name="rating" class="rounded-md border-gray-300" required>
                    <option value="">Rate this facility</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
                <textarea name="comment" rows="3" class="rounded-md border-gray-300" placeholder="Comment"></textarea>
                <input type="file" name="media[]" multiple class="rounded-md border border-gray-300 p-2">
                <button class="w-fit rounded-md bg-slate-900 px-4 py-2 text-white">Submit Review</button>
            </form>
        </section>
    </div>
</x-app-layout>
