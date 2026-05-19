<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Create Booking</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">{{ $facility->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $facility->category }} &middot; {{ ucfirst($facility->rental_type) }} &middot; {{ $facility->inventory_count }} available</p>
            </div>
            <a href="{{ route('facilities.show', $facility) }}" class="inline-flex w-full justify-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-stone-50 md:w-auto">
                Back to Details
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <form method="POST" action="{{ route('bookings.store') }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-stone-200 sm:p-6">
                @csrf
                <input type="hidden" name="facility_id" value="{{ $facility->id }}">

                <div class="border-b border-stone-100 pb-5">
                    <h3 class="text-lg font-extrabold text-slate-950">Schedule</h3>
                    <p class="mt-1 text-sm text-slate-500">Choose the date and type of reservation.</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label class="grid gap-1 text-sm font-bold text-slate-700">
                            Booking Date
                            <input type="date" name="booking_date" min="{{ now()->toDateString() }}" value="{{ old('booking_date') }}" class="h-11 rounded-md border-stone-300 text-sm font-normal" required>
                            <x-input-error :messages="$errors->get('booking_date')" class="mt-1" />
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">
                            Booking Type
                            <select name="booking_type" class="h-11 rounded-md border-stone-300 text-sm font-normal">
                                <option value="day_use" @selected(old('booking_type') === 'day_use')>Day use</option>
                                <option value="overnight" @selected(old('booking_type') === 'overnight')>Overnight</option>
                                <option value="hourly" @selected(old('booking_type') === 'hourly')>Hourly</option>
                            </select>
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">
                            Start Time
                            <input type="time" name="start_time" value="{{ old('start_time') }}" class="h-11 rounded-md border-stone-300 text-sm font-normal">
                            <x-input-error :messages="$errors->get('start_time')" class="mt-1" />
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">
                            End Time
                            <input type="time" name="end_time" value="{{ old('end_time') }}" class="h-11 rounded-md border-stone-300 text-sm font-normal">
                            <x-input-error :messages="$errors->get('end_time')" class="mt-1" />
                        </label>
                    </div>
                </div>

                <div class="border-b border-stone-100 py-5">
                    <h3 class="text-lg font-extrabold text-slate-950">Guests and Quantity</h3>
                    <p class="mt-1 text-sm text-slate-500">Tell the resort how many units and guests to expect.</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label class="grid gap-1 text-sm font-bold text-slate-700">
                            Quantity
                            <input type="number" name="quantity" min="1" max="{{ $facility->inventory_count }}" value="{{ old('quantity', 1) }}" class="h-11 rounded-md border-stone-300 text-sm font-normal" required>
                            <x-input-error :messages="$errors->get('quantity')" class="mt-1" />
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">
                            Guest Count
                            <input type="number" name="guest_count" min="1" max="{{ $facility->capacity * $facility->inventory_count }}" value="{{ old('guest_count', 1) }}" class="h-11 rounded-md border-stone-300 text-sm font-normal" required>
                            <x-input-error :messages="$errors->get('guest_count')" class="mt-1" />
                        </label>
                    </div>
                </div>

                <div class="pt-5">
                    <h3 class="text-lg font-extrabold text-slate-950">Optional Details</h3>
                    <div class="mt-4 grid gap-4">
                        <label class="grid gap-1 text-sm font-bold text-slate-700">
                            Promo Code
                            <input name="promo_code" value="{{ old('promo_code') }}" class="h-11 rounded-md border-stone-300 text-sm font-normal uppercase" placeholder="Optional">
                            <x-input-error :messages="$errors->get('promo_code')" class="mt-1" />
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">
                            Notes
                            <textarea name="notes" rows="3" class="rounded-md border-stone-300 text-sm font-normal" placeholder="Special requests or arrival notes">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('facilities.show', $facility) }}" class="inline-flex justify-center rounded-md border border-stone-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-stone-50">Cancel</a>
                    <button class="rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-600">Create Booking</button>
                </div>
            </form>

            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-stone-200">
                    <div class="aspect-[16/10] bg-slate-200">
                        @if ($facility->image)
                            <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center bg-teal-800 px-5 text-center text-xl font-extrabold text-white">{{ $facility->category }}</div>
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="font-extrabold text-slate-950">Booking Summary</h3>
                        <dl class="mt-4 grid gap-3 text-sm">
                            <div class="flex justify-between gap-3 rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Facility</dt><dd class="text-right font-bold text-slate-950">{{ $facility->name }}</dd></div>
                            <div class="flex justify-between gap-3 rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Capacity</dt><dd class="font-bold text-slate-950">{{ $facility->capacity }}</dd></div>
                            <div class="flex justify-between gap-3 rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Inventory</dt><dd class="font-bold text-slate-950">{{ $facility->inventory_count }}</dd></div>
                            <div class="flex justify-between gap-3 rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Rate</dt><dd class="text-right font-bold text-slate-950">&#8369;{{ number_format($facility->price_min, 2) }} - &#8369;{{ number_format($facility->price_max, 2) }}</dd></div>
                        </dl>
                        <div class="mt-5 rounded-md border border-teal-100 bg-teal-50 p-4 text-sm leading-6 text-teal-950">
                            Final amount is calculated after seasonal rate and promo checks. PayMongo checkout starts after this form is submitted.
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
