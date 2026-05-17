<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Book {{ $facility->name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $facility->category }} &middot; {{ $facility->rental_type }} &middot; {{ $facility->inventory_count }} available</p>
            </div>
            <a href="{{ route('facilities.show', $facility) }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">Back to details</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl p-4 sm:p-6 lg:p-8">
        <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
            <form method="POST" action="{{ route('bookings.store') }}" class="grid gap-5 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @csrf
                <input type="hidden" name="facility_id" value="{{ $facility->id }}">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold">Booking Date</label>
                        <input type="date" name="booking_date" min="{{ now()->toDateString() }}" value="{{ old('booking_date') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                        <x-input-error :messages="$errors->get('booking_date')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Booking Type</label>
                        <select name="booking_type" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="day_use" @selected(old('booking_type') === 'day_use')>Day use</option>
                            <option value="overnight" @selected(old('booking_type') === 'overnight')>Overnight</option>
                            <option value="hourly" @selected(old('booking_type') === 'hourly')>Hourly</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" class="mt-1 w-full rounded-lg border-gray-300">
                        <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" class="mt-1 w-full rounded-lg border-gray-300">
                        <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Quantity</label>
                        <input type="number" name="quantity" min="1" max="{{ $facility->inventory_count }}" value="{{ old('quantity', 1) }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Guest Count</label>
                        <input type="number" name="guest_count" min="1" max="{{ $facility->capacity * $facility->inventory_count }}" value="{{ old('guest_count', 1) }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                        <x-input-error :messages="$errors->get('guest_count')" class="mt-2" />
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold">Promo Code</label>
                    <input name="promo_code" value="{{ old('promo_code') }}" class="mt-1 w-full rounded-lg border-gray-300 uppercase" placeholder="Optional">
                    <x-input-error :messages="$errors->get('promo_code')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-semibold">Notes</label>
                    <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
                <button class="rounded-lg bg-cyan-700 px-5 py-3 font-semibold text-white transition hover:bg-cyan-600">Create Booking</button>
            </form>

            <aside class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h3 class="font-semibold">Booking Summary</h3>
                <dl class="mt-4 grid gap-3 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Facility</dt><dd class="text-right font-semibold">{{ $facility->name }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Capacity</dt><dd class="font-semibold">{{ $facility->capacity }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Inventory</dt><dd class="font-semibold">{{ $facility->inventory_count }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Rate</dt><dd class="font-semibold">&#8369;{{ number_format($facility->price_min, 2) }} - &#8369;{{ number_format($facility->price_max, 2) }}</dd></div>
                </dl>
                <div class="mt-5 rounded-lg bg-cyan-50 p-4 text-sm leading-6 text-cyan-900">
                    Final amount is calculated after seasonal rate and promo checks. Payment is verified by staff before a QR ticket is issued.
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
