<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Client View</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">My Bookings</h2>
                <p class="mt-1 text-sm text-slate-500">Track approvals, payments, cancellations, and QR ticket status.</p>
            </div>
            <a href="{{ route('facilities.index') }}" class="inline-flex w-full justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-teal-600 md:w-auto">
                Browse Facilities
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-4">
            @forelse ($bookings as $booking)
                @php
                    $bookingStatusClasses = match ($booking->booking_status) {
                        'approved', 'checked_in' => 'bg-emerald-50 text-emerald-700',
                        'cancelled' => 'bg-red-50 text-red-700',
                        default => 'bg-slate-100 text-slate-700',
                    };

                    $paymentStatusClasses = match ($booking->payment_status) {
                        'paid' => 'bg-emerald-50 text-emerald-700',
                        'failed', 'refunded' => 'bg-red-50 text-red-700',
                        default => 'bg-amber-50 text-amber-700',
                    };
                @endphp

                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-extrabold text-slate-950">{{ $booking->facility->name }}</h3>
                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $bookingStatusClasses }}">{{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}</span>
                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $paymentStatusClasses }}">{{ ucfirst($booking->payment_status) }}</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ $booking->facility->category }} &middot;
                                {{ $booking->booking_date->format('M d, Y') }} &middot;
                                {{ $booking->start_time ?: 'Whole day' }} {{ $booking->end_time ? '- '.$booking->end_time : '' }}
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
                            <a href="{{ route('bookings.show', $booking) }}" class="inline-flex justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">
                                View Booking
                            </a>
                            @if ($booking->booking_status === 'cancelled')
                                <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm('Delete this cancelled booking permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex w-full justify-center rounded-md border border-red-300 px-4 py-2 text-sm font-bold text-red-700 transition hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-5">
                        <div class="rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Quantity</dt>
                            <dd class="mt-1 font-extrabold text-slate-950">{{ $booking->quantity }}</dd>
                        </div>
                        <div class="rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Guests</dt>
                            <dd class="mt-1 font-extrabold text-slate-950">{{ $booking->guest_count }}</dd>
                        </div>
                        <div class="rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Total</dt>
                            <dd class="mt-1 font-extrabold text-slate-950">&#8369;{{ number_format($booking->total_amount, 2) }}</dd>
                        </div>
                        <div class="rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">Reference</dt>
                            <dd class="mt-1 truncate font-extrabold text-slate-950">{{ $booking->payment?->reference_number ?: 'Pending' }}</dd>
                        </div>
                        <div class="rounded-md bg-stone-50 p-3">
                            <dt class="text-slate-500">QR Ticket</dt>
                            <dd class="mt-1 font-extrabold text-slate-950">{{ $booking->ticket ? 'Issued' : 'Not issued' }}</dd>
                        </div>
                    </dl>
                </article>
            @empty
                <div class="rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-extrabold text-slate-950">No bookings yet</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Start by choosing a facility. Your reservations, payment status, and QR tickets will appear here.</p>
                    <a href="{{ route('facilities.index') }}" class="mt-5 inline-flex rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-teal-600">
                        Browse Facilities
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $bookings->links() }}</div>
    </div>
</x-app-layout>
