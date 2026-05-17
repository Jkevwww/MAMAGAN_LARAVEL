@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Ticket Lookup</h1>
    <section class="mt-6 rounded-lg bg-white p-6 shadow-sm">
        <dl class="grid gap-4 text-sm md:grid-cols-2">
            <div><dt class="text-slate-500">Tourist</dt><dd class="font-semibold">{{ $ticket->booking->user->name }}</dd></div>
            <div><dt class="text-slate-500">Facility</dt><dd class="font-semibold">{{ $ticket->booking->facility->name }}</dd></div>
            <div><dt class="text-slate-500">Date / Time</dt><dd class="font-semibold">{{ $ticket->booking->booking_date->format('M d, Y') }} {{ $ticket->booking->start_time }} {{ $ticket->booking->end_time ? '- '.$ticket->booking->end_time : '' }}</dd></div>
            <div><dt class="text-slate-500">Reference</dt><dd class="font-semibold">{{ $ticket->reference_number }}</dd></div>
            <div><dt class="text-slate-500">Booking Status</dt><dd class="font-semibold">{{ ucfirst($ticket->booking->booking_status) }}</dd></div>
            <div><dt class="text-slate-500">Payment Status</dt><dd class="font-semibold">{{ ucfirst($ticket->booking->payment_status) }}</dd></div>
        </dl>
        <form method="POST" action="{{ route('admin.checkin.confirm', $ticket) }}" class="mt-6">
            @csrf
            <button class="rounded-md bg-emerald-700 px-5 py-2 font-semibold text-white" @disabled($ticket->booking->payment_status !== 'paid')>Confirm Check-In</button>
        </form>
    </section>
@endsection
