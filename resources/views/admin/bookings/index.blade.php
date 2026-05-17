@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Bookings</h1>
    <form class="mt-6 grid gap-3 rounded-lg bg-white p-4 shadow-sm md:grid-cols-3">
        <select name="booking_status" class="rounded-md border-gray-300">
            <option value="">All booking statuses</option>
            @foreach (['pending','approved','cancelled','checked_in'] as $status)
                <option value="{{ $status }}" @selected(request('booking_status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="rounded-md border-gray-300">
            <option value="">All payment statuses</option>
            @foreach (['pending','paid','failed','refunded'] as $status)
                <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Filter</button>
    </form>
    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3">Tourist</th><th class="p-3">Facility</th><th class="p-3">Date</th><th class="p-3">Booking</th><th class="p-3">Payment</th><th class="p-3">Total</th><th class="p-3"></th></tr></thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr class="border-t">
                        <td class="p-3">{{ $booking->user->name }}</td>
                        <td class="p-3">{{ $booking->facility->name }}</td>
                        <td class="p-3">{{ $booking->booking_date->format('M d, Y') }}</td>
                        <td class="p-3">{{ ucfirst($booking->booking_status) }}</td>
                        <td class="p-3">{{ ucfirst($booking->payment_status) }}</td>
                        <td class="p-3">₱{{ number_format($booking->total_amount, 2) }}</td>
                        <td class="p-3 text-right"><a href="{{ route('admin.bookings.show', $booking) }}" class="font-semibold text-cyan-700">Manage</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $bookings->links() }}</div>
@endsection
