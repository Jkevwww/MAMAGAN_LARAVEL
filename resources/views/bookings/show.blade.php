<x-app-layout>
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

    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Booking #{{ $booking->id }}</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">{{ $booking->facility->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $booking->booking_date->format('M d, Y') }} &middot; {{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</p>
            </div>
            <a href="{{ route('bookings.index') }}" class="inline-flex w-full justify-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-stone-50 md:w-auto">
                Back to Bookings
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (request('payment') === 'success' && $booking->payment_status !== 'paid')
            <div class="mb-4 rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-950">
                Payment is being confirmed by PayMongo. Your QR ticket will appear here once the webhook marks the booking as paid.
            </div>
        @elseif (request('payment') === 'cancelled')
            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                PayMongo checkout was cancelled. You can restart payment below.
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">{{ $errors->first() }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-stone-200 sm:p-6">
                <div class="flex flex-col gap-4 border-b border-stone-100 pb-5 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-950">Reservation Details</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $booking->facility->category }} &middot; {{ ucfirst($booking->facility->rental_type) }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full px-3 py-1 text-sm font-bold {{ $bookingStatusClasses }}">{{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}</span>
                        <span class="rounded-full px-3 py-1 text-sm font-bold {{ $paymentStatusClasses }}">{{ ucfirst($booking->payment_status) }}</span>
                    </div>
                </div>

                <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Date</dt><dd class="mt-1 font-extrabold text-slate-950">{{ $booking->booking_date->format('M d, Y') }}</dd></div>
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Time</dt><dd class="mt-1 font-extrabold text-slate-950">{{ $booking->start_time ?: 'Whole day' }} {{ $booking->end_time ? '- '.$booking->end_time : '' }}</dd></div>
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Quantity</dt><dd class="mt-1 font-extrabold text-slate-950">{{ $booking->quantity }}</dd></div>
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Guests</dt><dd class="mt-1 font-extrabold text-slate-950">{{ $booking->guest_count }}</dd></div>
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Promo</dt><dd class="mt-1 font-extrabold text-slate-950">{{ $booking->promotion?->code ?? 'None' }}</dd></div>
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Payment Reference</dt><dd class="mt-1 break-words font-extrabold text-slate-950">{{ $booking->payment?->reference_number ?: 'Pending payment' }}</dd></div>
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Ticket Reference</dt><dd class="mt-1 break-words font-extrabold text-slate-950">{{ $booking->ticket?->reference_number ?: 'Issued after payment' }}</dd></div>
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Base</dt><dd class="mt-1 font-extrabold text-slate-950">&#8369;{{ number_format($booking->base_amount, 2) }}</dd></div>
                    <div class="rounded-md bg-stone-50 p-3"><dt class="text-slate-500">Discount</dt><dd class="mt-1 font-extrabold text-slate-950">&#8369;{{ number_format($booking->discount_amount, 2) }}</dd></div>
                    <div class="rounded-md bg-slate-950 p-3 text-white"><dt class="text-slate-300">Total</dt><dd class="mt-1 text-xl font-extrabold">&#8369;{{ number_format($booking->total_amount, 2) }}</dd></div>
                </dl>

                @if ($booking->payment_status !== 'paid' && ! in_array($booking->booking_status, ['cancelled', 'checked_in'], true))
                    <form method="POST" action="{{ route('bookings.paymongo', $booking) }}" class="mt-6 rounded-lg border border-teal-100 bg-teal-50 p-4">
                        @csrf
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="font-extrabold text-teal-950">Pay securely with PayMongo</h4>
                                <p class="mt-1 text-sm leading-6 text-teal-900">After successful payment, the system will issue your QR ticket and unique reference number automatically.</p>
                            </div>
                            <button class="shrink-0 rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-teal-600">Pay with PayMongo</button>
                        </div>
                    </form>
                @endif
            </section>

            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-stone-200">
                    <h3 class="font-extrabold text-slate-950">Ticket and Actions</h3>
                    @if ($booking->ticket)
                        @php
                            $qrPayload = json_encode(['reference_number' => $booking->ticket->reference_number, 'booking_id' => $booking->id]);
                        @endphp
                        <div class="mt-4 print:border print:border-dashed print:p-4">
                            <p class="text-center text-xs font-bold uppercase tracking-wide text-slate-500">QR Ticket</p>
                            <div class="my-3 border-t border-dashed"></div>
                            <img class="mx-auto h-56 w-56" alt="Ticket QR Code" src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($qrPayload) }}">
                            <div class="mt-3 text-center">
                                <div class="text-sm text-slate-500">Ticket Reference</div>
                                <div class="text-lg font-extrabold text-slate-950">{{ $booking->ticket->reference_number }}</div>
                            </div>
                            <div class="my-3 border-t border-dashed"></div>
                        </div>
                        <button type="button" onclick="window.print()" data-no-loader="true" class="mt-4 w-full rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">Print Ticket</button>
                    @else
                        <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">Your QR ticket appears here after payment is verified.</div>
                    @endif

                    @if (! in_array($booking->booking_status, ['cancelled', 'checked_in'], true) && $booking->payment_status !== 'paid')
                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-4" onsubmit="return confirm('Cancel this booking?')">
                            @csrf
                            <button class="w-full rounded-md border border-red-300 px-4 py-2 text-sm font-bold text-red-700 transition hover:bg-red-50">Cancel Booking</button>
                        </form>
                    @elseif ($booking->booking_status === 'cancelled')
                        <form method="POST" action="{{ route('bookings.destroy', $booking) }}" class="mt-4" onsubmit="return confirm('Delete this cancelled booking permanently?')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full rounded-md border border-red-300 px-4 py-2 text-sm font-bold text-red-700 transition hover:bg-red-50">Delete Booking</button>
                        </form>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
