@extends('layouts.admin')

@section('content')
    @php
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
        $schedule = $booking->booking_date->format('M d, Y').' - '.($booking->start_time ? str($booking->start_time)->substr(0, 5) : 'Whole day').($booking->end_time ? ' - '.str($booking->end_time)->substr(0, 5) : '');
        $detailItems = [
            ['label' => 'Guest', 'value' => $booking->user->name, 'caption' => $booking->user->email],
            ['label' => 'Facility', 'value' => $booking->facility->name, 'caption' => $booking->facility->category],
            ['label' => 'Schedule', 'value' => $booking->booking_date->format('M d, Y'), 'caption' => ($booking->start_time ? str($booking->start_time)->substr(0, 5) : 'Whole day').($booking->end_time ? ' - '.str($booking->end_time)->substr(0, 5) : '')],
            ['label' => 'Guests / Quantity', 'value' => $booking->guest_count.' guests', 'caption' => $booking->quantity.' reserved'],
            ['label' => 'Booking Type', 'value' => $formatStatus($booking->booking_type), 'caption' => 'Created '.$booking->created_at->format('M d, Y g:i A')],
            ['label' => 'Promotion', 'value' => $booking->promotion?->code ?? 'None', 'caption' => $booking->promotion?->name ?? 'No discount applied'],
        ];
        $amounts = [
            ['label' => 'Base amount', 'value' => $booking->base_amount],
            ['label' => 'Discount', 'value' => -1 * $booking->discount_amount],
            ['label' => 'Total due', 'value' => $booking->total_amount, 'strong' => true],
        ];
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Booking #{{ $booking->id }}</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">{{ $booking->user->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $booking->facility->name }} &middot; {{ $schedule }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold ring-1 {{ $statusTone[$booking->booking_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->booking_status) }}</span>
            <span class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold ring-1 {{ $statusTone[$booking->payment_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->payment_status) }}</span>
            <a href="{{ route('admin.bookings.index') }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">Back</a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <div class="grid gap-6">
            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-bold text-slate-950">Booking Details</h2>
                        <p class="text-xs text-slate-500">Guest, facility, schedule, and discount context.</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $booking->guest_count }} guests</span>
                </div>
                <dl class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($detailItems as $item)
                        <div class="rounded-lg border border-slate-200 p-3">
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $item['label'] }}</dt>
                            <dd class="mt-2 font-bold text-slate-950">{{ $item['value'] }}</dd>
                            <dd class="mt-1 text-xs text-slate-500">{{ $item['caption'] }}</dd>
                        </div>
                    @endforeach
                </dl>
                @if ($booking->notes)
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Guest Notes</p>
                        <p class="mt-2 text-sm text-amber-900">{{ $booking->notes }}</p>
                    </div>
                @endif
            </section>

            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-bold text-slate-950">Payment and Ticket</h2>
                        <p class="text-xs text-slate-500">References, proof, verification, and issued ticket.</p>
                    </div>
                    @if ($booking->ticket)
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Ticket issued</span>
                    @else
                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Ticket pending</span>
                    @endif
                </div>

                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <div class="rounded-lg border border-slate-200 p-4">
                        <h3 class="font-bold text-slate-950">Payment</h3>
                        <dl class="mt-3 grid gap-3 text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Method</dt>
                                <dd class="font-bold text-slate-900">{{ $booking->payment?->method ? str($booking->payment->method)->upper() : 'Not selected' }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Reference</dt>
                                <dd class="font-mono text-xs font-bold text-slate-900">{{ $booking->payment?->reference_number ?: 'Not submitted' }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Amount</dt>
                                <dd class="font-bold text-slate-900">&#8369;{{ number_format($booking->payment?->amount ?? $booking->total_amount, 2) }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Verified</dt>
                                <dd class="text-right font-bold text-slate-900">{{ $booking->payment?->verified_at?->format('M d, Y g:i A') ?: 'No' }}</dd>
                            </div>
                            @if ($booking->payment?->verifier)
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-slate-500">Verified by</dt>
                                    <dd class="text-right font-bold text-slate-900">{{ $booking->payment->verifier->name }}</dd>
                                </div>
                            @endif
                        </dl>
                        @if ($booking->payment?->proof_path)
                            <a href="{{ asset('storage/'.$booking->payment->proof_path) }}" target="_blank" class="mt-4 inline-flex h-10 items-center gap-2 rounded-lg border border-cyan-200 bg-cyan-50 px-3 text-sm font-bold text-cyan-800 transition hover:bg-cyan-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                                View Proof
                            </a>
                        @endif
                    </div>

                    <div class="rounded-lg border border-slate-200 p-4">
                        <h3 class="font-bold text-slate-950">Ticket</h3>
                        @if ($booking->ticket)
                            <p class="mt-3 font-mono text-lg font-extrabold text-slate-950">{{ $booking->ticket->reference_number }}</p>
                            <dl class="mt-3 grid gap-3 text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-slate-500">Issued</dt>
                                    <dd class="text-right font-bold text-slate-900">{{ $booking->ticket->issued_at?->format('M d, Y g:i A') ?: 'Not recorded' }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-slate-500">Checked in</dt>
                                    <dd class="text-right font-bold text-slate-900">{{ $booking->ticket->checked_in_at?->format('M d, Y g:i A') ?: 'No' }}</dd>
                                </div>
                            </dl>
                            <a href="{{ route('admin.checkin.index') }}" class="mt-4 inline-flex h-10 items-center gap-2 rounded-lg bg-emerald-700 px-3 text-sm font-bold text-white transition hover:bg-emerald-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7V5a1 1 0 0 1 1-1h2M17 4h2a1 1 0 0 1 1 1v2M20 17v2a1 1 0 0 1-1 1h-2M7 20H5a1 1 0 0 1-1-1v-2M8 8h8v8H8z"/></svg>
                                Open Check-In
                            </a>
                        @else
                            <p class="mt-3 rounded-lg bg-amber-50 p-4 text-sm font-semibold text-amber-800">A ticket will be generated when this booking is marked paid.</p>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <div class="grid content-start gap-6">
            <section class="rounded-xl bg-slate-950 p-4 text-white shadow-sm">
                <h2 class="font-bold">Amount Summary</h2>
                <div class="mt-4 grid gap-3">
                    @foreach ($amounts as $amount)
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-white/5 px-3 py-2">
                            <span class="text-sm font-semibold text-slate-300">{{ $amount['label'] }}</span>
                            <span class="{{ ! empty($amount['strong']) ? 'text-lg font-extrabold text-white' : 'font-bold text-slate-100' }}">
                                {{ $amount['value'] < 0 ? '-' : '' }}&#8369;{{ number_format(abs($amount['value']), 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <h2 class="font-bold text-slate-950">Admin Actions</h2>
                <p class="mt-1 text-xs text-slate-500">Update booking workflow and payment verification separately.</p>

                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="mt-4 grid gap-3">
                    @csrf
                    @method('PATCH')
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Booking Status
                        <select name="booking_status" class="h-10 rounded-lg border-slate-300 text-sm font-normal">
                            @foreach (['pending','approved','cancelled','checked_in'] as $status)
                                <option value="{{ $status }}" @selected($booking->booking_status === $status)>{{ $formatStatus($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="h-10 rounded-lg bg-slate-950 px-4 text-sm font-bold text-white transition hover:bg-slate-800">Update Booking</button>
                </form>

                <form method="POST" action="{{ route('admin.bookings.payment', $booking) }}" class="mt-5 grid gap-3 border-t border-slate-100 pt-5">
                    @csrf
                    @method('PATCH')
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Payment Status
                        <select name="payment_status" class="h-10 rounded-lg border-slate-300 text-sm font-normal">
                            @foreach (['pending','paid','failed','refunded'] as $status)
                                <option value="{{ $status }}" @selected($booking->payment_status === $status)>{{ $formatStatus($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="h-10 rounded-lg bg-cyan-700 px-4 text-sm font-bold text-white transition hover:bg-cyan-600">Verify Payment</button>
                </form>
            </section>

            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <h2 class="font-bold text-slate-950">Provider Details</h2>
                <dl class="mt-4 grid gap-3 text-sm">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">PayMongo Checkout</dt>
                        <dd class="mt-1 break-all font-mono text-xs font-bold text-slate-800">{{ $booking->payment?->paymongo_checkout_id ?: 'None' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">PayMongo Payment</dt>
                        <dd class="mt-1 break-all font-mono text-xs font-bold text-slate-800">{{ $booking->payment?->paymongo_payment_id ?: 'None' }}</dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
@endsection
