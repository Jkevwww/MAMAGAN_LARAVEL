@extends('layouts.admin')

@section('content')
    @php
        $booking = $ticket->booking;
        $canCheckIn = $booking->payment_status === 'paid' && ! $ticket->checked_in_at;
        $isAlreadyCheckedIn = filled($ticket->checked_in_at);
        $isBlocked = $booking->payment_status !== 'paid';
        $panelTone = $canCheckIn
            ? 'border-emerald-200 bg-emerald-50 text-emerald-900'
            : ($isBlocked ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-slate-200 bg-slate-50 text-slate-800');
        $statusTone = [
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'approved' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
            'checked_in' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-200',
            'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'failed' => 'bg-rose-50 text-rose-700 ring-rose-200',
            'refunded' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ];
        $formatStatus = fn ($status) => str($status)->replace('_', ' ')->title();
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Ticket Lookup</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Check-In Review</h1>
            <p class="mt-1 text-sm text-slate-500">Confirm the guest details before marking the ticket as checked in.</p>
        </div>
        <a href="{{ route('admin.checkin.index') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
            Scan Another
        </a>
    </div>

    <section class="mt-6 rounded-xl border px-4 py-3 text-sm font-semibold {{ $panelTone }}">
        @if ($canCheckIn)
            Ticket is valid for check-in. Confirm only after matching the guest and booking details.
        @elseif ($isBlocked)
            This booking cannot be checked in because payment is not marked as paid.
        @else
            This ticket has already been checked in.
        @endif
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 p-5">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Ticket Reference</p>
                        <p class="mt-2 break-all font-mono text-2xl font-extrabold tracking-wide text-slate-950">{{ $ticket->reference_number }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full px-3 py-1 text-sm font-bold ring-1 {{ $statusTone[$booking->booking_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->booking_status) }}</span>
                        <span class="rounded-full px-3 py-1 text-sm font-bold ring-1 {{ $statusTone[$booking->payment_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->payment_status) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 p-5 md:grid-cols-2">
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Guest</p>
                    <p class="mt-2 font-extrabold text-slate-950">{{ $booking->user->name }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $booking->user->email }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Facility</p>
                    <p class="mt-2 font-extrabold text-slate-950">{{ $booking->facility->name }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $booking->facility->category }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Schedule</p>
                    <p class="mt-2 font-extrabold text-slate-950">{{ $booking->booking_date->format('M d, Y') }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $booking->start_time ? str($booking->start_time)->substr(0, 5) : 'Whole day' }}{{ $booking->end_time ? ' - '.str($booking->end_time)->substr(0, 5) : '' }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Guests / Quantity</p>
                    <p class="mt-2 font-extrabold text-slate-950">{{ $booking->guest_count }} guests</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $booking->quantity }} reserved</p>
                </div>
            </div>
        </section>

        <aside class="grid content-start gap-6">
            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <h2 class="font-bold text-slate-950">Check-In Action</h2>
                <div class="mt-4 grid gap-3 text-sm">
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2">
                        <span class="font-semibold text-slate-500">Checked in</span>
                        <span class="font-bold text-slate-950">{{ $ticket->checked_in_at?->format('M d, Y g:i A') ?: 'Not yet' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2">
                        <span class="font-semibold text-slate-500">Checked by</span>
                        <span class="font-bold text-slate-950">{{ $ticket->checker?->name ?: 'Not yet' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2">
                        <span class="font-semibold text-slate-500">Payment ref</span>
                        <span class="truncate font-mono text-xs font-bold text-slate-950">{{ $booking->payment?->reference_number ?: 'None' }}</span>
                    </div>
                </div>

                @if ($canCheckIn)
                    <form method="POST" action="{{ route('admin.checkin.confirm', $ticket) }}" class="mt-4" onsubmit="return confirm('Confirm check-in for {{ $booking->user->name }}?')">
                        @csrf
                        <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 text-sm font-bold text-white transition hover:bg-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z"/></svg>
                            Confirm Check-In
                        </button>
                    </form>
                @else
                    <button disabled class="mt-4 inline-flex h-11 w-full cursor-not-allowed items-center justify-center rounded-lg bg-slate-100 px-4 text-sm font-bold text-slate-400">
                        Check-In Unavailable
                    </button>
                @endif
            </section>

            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <h2 class="font-bold text-slate-950">Booking Amount</h2>
                <div class="mt-4 grid gap-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <span class="text-slate-500">Base</span>
                        <span class="font-bold text-slate-950">&#8369;{{ number_format($booking->base_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-slate-500">Discount</span>
                        <span class="font-bold text-slate-950">-&#8369;{{ number_format($booking->discount_amount, 2) }}</span>
                    </div>
                    <div class="mt-2 flex justify-between gap-3 border-t border-slate-100 pt-3">
                        <span class="font-bold text-slate-700">Total</span>
                        <span class="font-extrabold text-slate-950">&#8369;{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>
            </section>
        </aside>
    </div>
@endsection
