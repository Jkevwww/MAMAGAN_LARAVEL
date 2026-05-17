<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">My Bookings</h2>
                <p class="mt-1 text-sm text-gray-500">Track payment verification, approvals, and QR tickets.</p>
            </div>
            <a href="{{ route('facilities.index') }}" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-600">Browse Facilities</a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
        <div class="grid gap-4">
            @forelse ($bookings as $booking)
                <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="font-bold text-slate-950">{{ $booking->facility->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $booking->booking_date->format('M d, Y') }} &middot; {{ $booking->start_time ?: 'Whole day' }} {{ $booking->end_time ? '- '.$booking->end_time : '' }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}</span>
                            <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $booking->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($booking->payment_status) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-3 text-sm sm:grid-cols-4">
                        <div><p class="text-slate-500">Quantity</p><p class="font-semibold">{{ $booking->quantity }}</p></div>
                        <div><p class="text-slate-500">Guests</p><p class="font-semibold">{{ $booking->guest_count }}</p></div>
                        <div><p class="text-slate-500">Total</p><p class="font-semibold">&#8369;{{ number_format($booking->total_amount, 2) }}</p></div>
                        <div class="sm:text-right"><a href="{{ route('bookings.show', $booking) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">View booking</a></div>
                    </div>
                </article>
            @empty
                <div class="rounded-xl bg-white p-8 text-center text-gray-500 shadow-sm ring-1 ring-slate-200">
                    No bookings yet.
                </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $bookings->links() }}</div>
    </div>
</x-app-layout>
