<x-app-layout>
    @php
        $averageRating = number_format((float) ($facility->reviews->avg('rating') ?? 0), 1);
        $reviewCount = $facility->reviews->count();
        $rateLabel = $facility->price_min == $facility->price_max
            ? '&#8369;'.number_format($facility->price_min, 2)
            : '&#8369;'.number_format($facility->price_min, 2).' - &#8369;'.number_format($facility->price_max, 2);
        $recentReviews = $facility->reviews->sortByDesc('created_at')->take(8);
        $facilityImageUrl = $facility->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($facility->image)
            ? route('media.public', ['path' => $facility->image])
            : null;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">{{ $facility->category }}</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">{{ $facility->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ ucfirst($facility->rental_type) }} rental &middot; {{ $facility->capacity }} guest capacity &middot; {{ $facility->inventory_count }} available</p>
            </div>
            <a href="{{ route('facilities.index') }}" class="inline-flex w-full justify-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-stone-50 md:w-auto">
                Back to Facilities
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <section class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-stone-200">
                <div class="relative aspect-[16/9] bg-slate-200">
                    @if ($facilityImageUrl)
                        <img src="{{ $facilityImageUrl }}" alt="{{ $facility->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center bg-teal-800 px-6 text-center text-3xl font-extrabold text-white">{{ $facility->category }}</div>
                    @endif
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/80 to-transparent p-4 sm:p-6">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-md bg-white px-3 py-1 text-sm font-extrabold text-slate-950">{{ $facility->category }}</span>
                            <span class="rounded-md bg-teal-500 px-3 py-1 text-sm font-extrabold text-white">{{ ucfirst($facility->rental_type) }}</span>
                            <span class="rounded-md bg-amber-400 px-3 py-1 text-sm font-extrabold text-slate-950">&#9733; {{ $averageRating }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid gap-3 sm:grid-cols-4">
                        <div class="rounded-md bg-stone-50 p-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Capacity</p>
                            <p class="mt-1 font-extrabold text-slate-950">{{ $facility->capacity }} guests</p>
                        </div>
                        <div class="rounded-md bg-stone-50 p-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Inventory</p>
                            <p class="mt-1 font-extrabold text-slate-950">{{ $facility->inventory_count }} units</p>
                        </div>
                        <div class="rounded-md bg-stone-50 p-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Rating</p>
                            <p class="mt-1 font-extrabold text-amber-600">&#9733; {{ $averageRating }}</p>
                        </div>
                        <div class="rounded-md bg-stone-50 p-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Reviews</p>
                            <p class="mt-1 font-extrabold text-slate-950">{{ $reviewCount }}</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-950">Overview</h3>
                            <p class="mt-2 max-w-3xl leading-7 text-slate-600">{{ $facility->description ?: 'No description provided.' }}</p>
                        </div>
                        <div class="rounded-lg border border-teal-100 bg-teal-50 p-4">
                            <p class="text-sm font-bold uppercase tracking-wide text-teal-800">Good to Know</p>
                            <ul class="mt-3 space-y-2 text-sm leading-6 text-teal-950">
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1.5 w-1.5 rounded-full bg-teal-700"></span>
                                    <span>Bookable online while inventory is available.</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1.5 w-1.5 rounded-full bg-teal-700"></span>
                                    <span>Final totals can change with seasonal rates and promos.</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1.5 w-1.5 rounded-full bg-teal-700"></span>
                                    <span>Approved bookings generate a QR ticket for check-in.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-stone-200">
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500">Rate Range</p>
                    <div class="mt-2 text-3xl font-extrabold text-slate-950">{!! $rateLabel !!}</div>
                    <p class="mt-2 text-sm leading-6 text-slate-500">The booking form checks the selected date, time, quantity, and any promo code before payment.</p>

                    <dl class="mt-5 grid gap-3 text-sm">
                        <div class="flex items-center justify-between gap-3 rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Facility type</dt>
                            <dd class="text-right font-extrabold text-slate-950">{{ $facility->category }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Rental</dt>
                            <dd class="font-extrabold text-slate-950">{{ ucfirst($facility->rental_type) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Capacity</dt>
                            <dd class="font-extrabold text-slate-950">{{ $facility->capacity }} guests</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Availability</dt>
                            <dd class="font-extrabold text-emerald-700">{{ $facility->inventory_count }} units</dd>
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
            <div class="flex flex-col gap-3 border-b border-stone-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-950">Guest Reviews</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $reviewCount ? 'Recent feedback from resort guests.' : 'This facility has not been reviewed yet.' }}</p>
                </div>
                <div class="inline-flex w-fit items-center gap-2 rounded-md bg-amber-50 px-3 py-2 text-sm font-extrabold text-amber-700">
                    <span>&#9733; {{ $averageRating }}</span>
                    <span class="text-slate-400">/</span>
                    <span class="text-slate-600">{{ $reviewCount }} {{ $reviewCount === 1 ? 'review' : 'reviews' }}</span>
                </div>
            </div>

            <div class="mt-5 grid gap-4">
                @forelse ($recentReviews as $review)
                    <article class="rounded-lg border border-stone-200 p-4">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <strong class="text-sm text-slate-950">{{ $review->user->name }}</strong>
                                <p class="mt-0.5 text-xs font-semibold text-slate-500">{{ $review->created_at?->format('M d, Y') }}</p>
                            </div>
                            <span class="text-sm font-bold text-amber-600">&#9733; {{ $review->rating }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $review->comment ?: 'No written comment.' }}</p>

                        @if ($review->media->isNotEmpty())
                            <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                                @foreach ($review->media as $media)
                                    @php
                                        $mediaUrl = $media->path && \Illuminate\Support\Facades\Storage::disk('public')->exists($media->path)
                                            ? route('media.public', ['path' => $media->path])
                                            : null;
                                    @endphp
                                    @if ($mediaUrl && str_starts_with($media->media_type, 'image'))
                                        <img src="{{ $mediaUrl }}" alt="Review media for {{ $facility->name }}" class="h-20 w-24 shrink-0 rounded-md object-cover ring-1 ring-stone-200">
                                    @elseif ($mediaUrl)
                                        <a href="{{ $mediaUrl }}" class="inline-flex h-20 w-24 shrink-0 items-center justify-center rounded-md bg-stone-100 px-3 text-center text-xs font-bold text-slate-600 ring-1 ring-stone-200">View media</a>
                                    @else
                                        <span class="inline-flex h-20 w-24 shrink-0 items-center justify-center rounded-md bg-stone-100 px-3 text-center text-xs font-bold text-slate-500 ring-1 ring-stone-200">Media unavailable</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="rounded-md bg-stone-50 p-4 text-sm text-slate-600">No reviews yet.</div>
                @endforelse
            </div>

            @auth
                <form method="POST" action="{{ route('facilities.reviews.store', $facility) }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-lg border border-stone-200 bg-stone-50 p-4">
                    @csrf
                    <div class="grid gap-3 sm:grid-cols-[180px_1fr]">
                        <label>
                            <span class="sr-only">Rating</span>
                            <select name="rating" class="h-10 w-full rounded-md border-stone-300 text-sm" required>
                                <option value="">Rating</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </label>
                        <label>
                            <span class="sr-only">Review media</span>
                            <input type="file" name="media[]" multiple class="w-full rounded-md border border-stone-300 bg-white p-2 text-sm">
                        </label>
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
