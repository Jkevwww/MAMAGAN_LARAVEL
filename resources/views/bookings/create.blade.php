<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Book {{ $facility->name }}</h2></x-slot>
    <div class="mx-auto max-w-4xl p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('bookings.store') }}" class="grid gap-5 rounded-lg bg-white p-6 shadow-sm">
            @csrf
            <input type="hidden" name="facility_id" value="{{ $facility->id }}">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold">Booking Date</label>
                    <input type="date" name="booking_date" value="{{ old('booking_date') }}" class="mt-1 w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="text-sm font-semibold">Booking Type</label>
                    <select name="booking_type" class="mt-1 w-full rounded-md border-gray-300">
                        <option value="day_use">Day use</option>
                        <option value="overnight">Overnight</option>
                        <option value="hourly">Hourly</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" class="mt-1 w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" class="mt-1 w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Quantity</label>
                    <input type="number" name="quantity" min="1" max="{{ $facility->inventory_count }}" value="{{ old('quantity', 1) }}" class="mt-1 w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="text-sm font-semibold">Guest Count</label>
                    <input type="number" name="guest_count" min="1" value="{{ old('guest_count', 1) }}" class="mt-1 w-full rounded-md border-gray-300" required>
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold">Promo Code</label>
                <input name="promo_code" value="{{ old('promo_code') }}" class="mt-1 w-full rounded-md border-gray-300 uppercase" placeholder="Optional">
            </div>
            <div>
                <label class="text-sm font-semibold">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-gray-300">{{ old('notes') }}</textarea>
            </div>
            <div class="rounded-md bg-cyan-50 p-4 text-sm text-cyan-900">
                Base rate starts at <strong>₱{{ number_format($facility->price_min, 2) }}</strong>. Final amount is calculated after seasonal rate and promo checks.
            </div>
            <button class="rounded-md bg-cyan-700 px-5 py-3 font-semibold text-white">Create Booking</button>
        </form>
    </div>
</x-app-layout>
