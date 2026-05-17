<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Booking #{{ $booking->id }}</h2></x-slot>
    <div class="mx-auto max-w-5xl p-4 sm:p-6 lg:p-8">
        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold">{{ $booking->facility->name }}</h3>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Date</dt><dd class="font-semibold">{{ $booking->booking_date->format('M d, Y') }}</dd></div>
                    <div><dt class="text-gray-500">Time</dt><dd class="font-semibold">{{ $booking->start_time ?: 'Whole day' }} {{ $booking->end_time ? '- '.$booking->end_time : '' }}</dd></div>
                    <div><dt class="text-gray-500">Quantity</dt><dd class="font-semibold">{{ $booking->quantity }}</dd></div>
                    <div><dt class="text-gray-500">Guests</dt><dd class="font-semibold">{{ $booking->guest_count }}</dd></div>
                    <div><dt class="text-gray-500">Booking Status</dt><dd class="font-semibold">{{ ucfirst($booking->booking_status) }}</dd></div>
                    <div><dt class="text-gray-500">Payment Status</dt><dd class="font-semibold">{{ ucfirst($booking->payment_status) }}</dd></div>
                    <div><dt class="text-gray-500">Base</dt><dd class="font-semibold">₱{{ number_format($booking->base_amount, 2) }}</dd></div>
                    <div><dt class="text-gray-500">Discount</dt><dd class="font-semibold">₱{{ number_format($booking->discount_amount, 2) }}</dd></div>
                    <div><dt class="text-gray-500">Total</dt><dd class="text-xl font-bold">₱{{ number_format($booking->total_amount, 2) }}</dd></div>
                </dl>

                @if ($booking->payment_status !== 'paid')
                    <form method="POST" action="{{ route('bookings.payment.record', $booking) }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-md bg-slate-50 p-4">
                        @csrf
                        <h4 class="font-semibold">Record Payment Reference</h4>
                        <select name="method" class="rounded-md border-gray-300">
                            <option value="gcash">GCash</option>
                            <option value="bank">Bank transfer</option>
                            <option value="cash">Cash</option>
                        </select>
                        <input name="reference_number" value="{{ old('reference_number', $booking->payment?->reference_number) }}" class="rounded-md border-gray-300" placeholder="GCash/payment reference number" required>
                        <input type="file" name="proof" class="rounded-md border border-gray-300 p-2">
                        <button class="w-fit rounded-md bg-cyan-700 px-4 py-2 text-white">Submit Reference</button>
                    </form>
                @endif
            </section>

            <aside class="rounded-lg bg-white p-6 shadow-sm">
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
                        </div>
                        <div class="my-3 border-t border-dashed"></div>
                    </div>
                    <button onclick="window.print()" class="mt-4 w-full rounded-md bg-slate-900 px-4 py-2 text-white">Print Ticket</button>
                @else
                    <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-900">Your QR ticket appears here after staff/admin verifies payment.</div>
                @endif

                @if (! in_array($booking->booking_status, ['cancelled', 'checked_in'], true))
                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-4" onsubmit="return confirm('Cancel this booking?')">
                        @csrf
                        <button class="w-full rounded-md border border-red-300 px-4 py-2 text-red-700">Cancel Booking</button>
                    </form>
                @endif
            </aside>
        </div>
    </div>
</x-app-layout>
