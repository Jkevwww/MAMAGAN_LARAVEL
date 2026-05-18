<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Booking #{{ $booking->id }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $booking->facility->name }}</p>
            </div>
            <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">Back to bookings</a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-6xl p-4 sm:p-6 lg:p-8">
        @if (request('payment') === 'success' && $booking->payment_status !== 'paid')
            <div class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-900">
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

        <div class="grid gap-6 lg:grid-cols-[1fr_380px]">
            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-3 border-b border-slate-100 pb-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $booking->facility->name }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $booking->booking_type }} &middot; {{ $booking->facility->category }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}</span>
                        <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $booking->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($booking->payment_status) }}</span>
                    </div>
                </div>

                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Date</dt><dd class="font-semibold">{{ $booking->booking_date->format('M d, Y') }}</dd></div>
                    <div><dt class="text-gray-500">Time</dt><dd class="font-semibold">{{ $booking->start_time ?: 'Whole day' }} {{ $booking->end_time ? '- '.$booking->end_time : '' }}</dd></div>
                    <div><dt class="text-gray-500">Quantity</dt><dd class="font-semibold">{{ $booking->quantity }}</dd></div>
                    <div><dt class="text-gray-500">Guests</dt><dd class="font-semibold">{{ $booking->guest_count }}</dd></div>
                    <div><dt class="text-gray-500">Promo</dt><dd class="font-semibold">{{ $booking->promotion?->code ?? 'None' }}</dd></div>
                    <div><dt class="text-gray-500">Payment Reference</dt><dd class="font-semibold">{{ $booking->payment?->reference_number ?: 'Pending PayMongo payment' }}</dd></div>
                    <div><dt class="text-gray-500">Ticket Reference</dt><dd class="font-semibold">{{ $booking->ticket?->reference_number ?: 'Issued after payment' }}</dd></div>
                    <div><dt class="text-gray-500">Base</dt><dd class="font-semibold">&#8369;{{ number_format($booking->base_amount, 2) }}</dd></div>
                    <div><dt class="text-gray-500">Discount</dt><dd class="font-semibold">&#8369;{{ number_format($booking->discount_amount, 2) }}</dd></div>
                    <div><dt class="text-gray-500">Total</dt><dd class="text-xl font-bold">&#8369;{{ number_format($booking->total_amount, 2) }}</dd></div>
                </dl>

                @if ($booking->payment_status !== 'paid' && ! in_array($booking->booking_status, ['cancelled', 'checked_in'], true))
                    <form method="POST" action="{{ route('bookings.paymongo', $booking) }}" class="mt-6 rounded-xl border border-cyan-100 bg-cyan-50 p-4">
                        @csrf
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="font-semibold text-cyan-950">Pay securely with PayMongo</h4>
                                <p class="mt-1 text-sm leading-6 text-cyan-900">After successful payment, the system will issue your QR ticket and unique reference number automatically.</p>
                            </div>
                            <button class="shrink-0 rounded-lg bg-cyan-700 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600">Pay with PayMongo</button>
                        </div>
                    </form>
                @endif
            </section>

            <aside class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @if ($booking->ticket)
                    @php
                        $qrPayload = json_encode(['reference_number' => $booking->ticket->reference_number, 'booking_id' => $booking->id]);
                    @endphp
                    <div class="print:border print:border-dashed print:p-4">
                        <p class="text-center text-xs uppercase tracking-wide text-gray-500">Cut here</p>
                        <div class="my-3 border-t border-dashed"></div>
                        <img class="mx-auto h-56 w-56" alt="Ticket QR Code" src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($qrPayload) }}">
                        <div class="mt-3 text-center">
                            <div class="text-sm text-gray-500">Ticket Reference</div>
                            <div class="text-lg font-bold">{{ $booking->ticket->reference_number }}</div>
                            <div class="mt-1 text-xs text-gray-500">Use this for manual audit if scanning is unavailable.</div>
                        </div>
                        <div class="my-3 border-t border-dashed"></div>
                    </div>
                    <button type="button" onclick="window.print()" data-no-loader="true" class="mt-4 w-full rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">Print Ticket</button>
                @else
                    <div class="rounded-lg bg-amber-50 p-4 text-sm leading-6 text-amber-900">Your QR ticket appears here after staff/admin verifies payment.</div>
                @endif

                @if (! in_array($booking->booking_status, ['cancelled', 'checked_in'], true) && $booking->payment_status !== 'paid')
                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-4" onsubmit="return confirm('Cancel this booking?')">
                        @csrf
                        <button class="w-full rounded-lg border border-red-300 px-4 py-2 font-semibold text-red-700 transition hover:bg-red-50">Cancel Booking</button>
                    </form>
                @endif
            </aside>
        </div>
    </div>
</x-app-layout>
