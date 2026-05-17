@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Booking #{{ $booking->id }}</h1>
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-lg bg-white p-6 shadow-sm">
            <dl class="grid gap-4 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">Tourist</dt><dd class="font-semibold">{{ $booking->user->name }}<br>{{ $booking->user->email }}</dd></div>
                <div><dt class="text-slate-500">Facility</dt><dd class="font-semibold">{{ $booking->facility->name }}</dd></div>
                <div><dt class="text-slate-500">Date / Time</dt><dd class="font-semibold">{{ $booking->booking_date->format('M d, Y') }} {{ $booking->start_time ?: '' }} {{ $booking->end_time ? '- '.$booking->end_time : '' }}</dd></div>
                <div><dt class="text-slate-500">Guests / Qty</dt><dd class="font-semibold">{{ $booking->guest_count }} guests · {{ $booking->quantity }} qty</dd></div>
                <div><dt class="text-slate-500">Total</dt><dd class="text-xl font-bold">₱{{ number_format($booking->total_amount, 2) }}</dd></div>
                <div><dt class="text-slate-500">Promo</dt><dd class="font-semibold">{{ $booking->promotion?->code ?? 'None' }}</dd></div>
            </dl>
        </section>
        <section class="rounded-lg bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="grid gap-3">
                @csrf @method('PATCH')
                <label class="text-sm font-semibold">Booking Status</label>
                <select name="booking_status" class="rounded-md border-gray-300">
                    @foreach (['pending','approved','cancelled','checked_in'] as $status)
                        <option value="{{ $status }}" @selected($booking->booking_status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
                <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Update Booking</button>
            </form>

            <form method="POST" action="{{ route('admin.bookings.payment', $booking) }}" class="mt-6 grid gap-3">
                @csrf @method('PATCH')
                <label class="text-sm font-semibold">Payment Status</label>
                <select name="payment_status" class="rounded-md border-gray-300">
                    @foreach (['pending','paid','failed','refunded'] as $status)
                        <option value="{{ $status }}" @selected($booking->payment_status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="rounded-md bg-cyan-700 px-4 py-2 text-white">Verify Payment</button>
            </form>
        </section>
    </div>

    <section class="mt-6 rounded-lg bg-white p-6 shadow-sm">
        <h2 class="font-semibold">Payment and Ticket</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div class="text-sm">
                <p><strong>Method:</strong> {{ $booking->payment?->method }}</p>
                <p><strong>Reference:</strong> {{ $booking->payment?->reference_number ?: 'Not submitted' }}</p>
                <p><strong>Verified:</strong> {{ $booking->payment?->verified_at?->format('M d, Y g:i A') ?: 'No' }}</p>
                @if ($booking->payment?->proof_path)
                    <a href="{{ asset('storage/'.$booking->payment->proof_path) }}" target="_blank" class="font-semibold text-cyan-700">View proof</a>
                @endif
            </div>
            <div>
                @if ($booking->ticket)
                    <p class="text-sm text-slate-500">Ticket Reference</p>
                    <p class="text-xl font-bold">{{ $booking->ticket->reference_number }}</p>
                @else
                    <p class="text-sm text-amber-700">Ticket will be generated when payment is marked paid.</p>
                @endif
            </div>
        </div>
    </section>
@endsection
