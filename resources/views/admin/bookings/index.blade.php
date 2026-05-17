@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Bookings</h1>
            <p class="mt-1 text-sm text-slate-500">Search guests, facilities, and ticket references.</p>
        </div>
    </div>

    <form class="mt-6 rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200" x-data="{ filtersOpen: {{ request('booking_status') || request('payment_status') ? 'true' : 'false' }} }">
        <div class="flex gap-2">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                </svg>
                <input name="search" value="{{ request('search') }}" class="w-full rounded-lg border-slate-300 pl-10" placeholder="Search booking">
            </div>
            <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-300 transition hover:bg-slate-50" aria-label="Toggle filters">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/></svg>
            </button>
            <button class="rounded-lg bg-cyan-700 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600">Search</button>
        </div>
        <div x-show="filtersOpen" x-transition x-cloak class="mt-3 grid gap-3 border-t border-slate-100 pt-3 md:grid-cols-3">
            <select name="booking_status" class="rounded-lg border-slate-300">
                <option value="">All booking statuses</option>
                @foreach (['pending','approved','cancelled','checked_in'] as $status)
                    <option value="{{ $status }}" @selected(request('booking_status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="rounded-lg border-slate-300">
                <option value="">All payment statuses</option>
                @foreach (['pending','paid','failed','refunded'] as $status)
                    <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.bookings.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600"><tr><th class="p-3">Tourist</th><th class="p-3">Facility</th><th class="p-3">Date</th><th class="p-3">Booking</th><th class="p-3">Payment</th><th class="p-3">Total</th><th class="p-3"></th></tr></thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr class="border-t transition hover:bg-slate-50">
                        <td class="p-3">{{ $booking->user->name }}</td>
                        <td class="p-3">{{ $booking->facility->name }}</td>
                        <td class="p-3">{{ $booking->booking_date->format('M d, Y') }}</td>
                        <td class="p-3">{{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}</td>
                        <td class="p-3">{{ ucfirst($booking->payment_status) }}</td>
                        <td class="p-3">&#8369;{{ number_format($booking->total_amount, 2) }}</td>
                        <td class="p-3 text-right"><a href="{{ route('admin.bookings.show', $booking) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Manage</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-500">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $bookings->links() }}</div>
@endsection
