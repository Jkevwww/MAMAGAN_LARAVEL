@extends('layouts.admin')

@section('content')
    @php
        $booking = $ticket->booking;
        $canCheckIn = $booking->payment_status === 'paid' && ! $ticket->checked_in_at;
    @endphp

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Ticket Lookup</h1>
            <p class="mt-1 text-sm text-slate-500">Review the booking before confirming check-in.</p>
        </div>
        <a href="{{ route('admin.checkin.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Scan Another</a>
    </div>

    <section class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-sm text-slate-500">Ticket Reference</p>
                <p class="mt-1 text-2xl font-bold tracking-wide">{{ $ticket->reference_number }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $booking->booking_status === 'checked_in' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}</span>
                <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $booking->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($booking->payment_status) }}</span>
            </div>
        </div>

        <dl class="mt-5 grid gap-4 text-sm md:grid-cols-2">
            <div><dt class="text-slate-500">Tourist</dt><dd class="font-semibold">{{ $booking->user->name }}</dd></div>
            <div><dt class="text-slate-500">Email</dt><dd class="font-semibold">{{ $booking->user->email }}</dd></div>
            <div><dt class="text-slate-500">Facility</dt><dd class="font-semibold">{{ $booking->facility->name }}</dd></div>
            <div><dt class="text-slate-500">Date / Time</dt><dd class="font-semibold">{{ $booking->booking_date->format('M d, Y') }} {{ $booking->start_time ?: 'Whole day' }} {{ $booking->end_time ? '- '.$booking->end_time : '' }}</dd></div>
            <div><dt class="text-slate-500">Guests / Quantity</dt><dd class="font-semibold">{{ $booking->guest_count }} guests &middot; {{ $booking->quantity }} reserved</dd></div>
            <div><dt class="text-slate-500">Payment Reference</dt><dd class="font-semibold">{{ $booking->payment?->reference_number ?: 'None recorded' }}</dd></div>
            <div><dt class="text-slate-500">Checked In</dt><dd class="font-semibold">{{ $ticket->checked_in_at?->format('M d, Y g:i A') ?: 'Not yet' }}</dd></div>
            <div><dt class="text-slate-500">Checked In By</dt><dd class="font-semibold">{{ $ticket->checker?->name ?: 'Not yet' }}</dd></div>
        </dl>

        @if ($booking->payment_status !== 'paid')
            <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                This booking cannot be checked in until payment is marked paid.
            </div>
        @elseif ($ticket->checked_in_at)
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                This ticket has already been checked in.
            </div>
        @else
            <form method="POST" action="{{ route('admin.checkin.confirm', $ticket) }}" class="mt-6" onsubmit="return confirm('Confirm check-in for {{ $booking->user->name }}?')">
                @csrf
                <button class="rounded-lg bg-emerald-700 px-5 py-2 font-semibold text-white transition hover:bg-emerald-600">Confirm Check-In</button>
            </form>
        @endif
    </section>
@endsection
