@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Reports</h1>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white">Export CSV</a>
    </div>
    <form class="mt-6 grid gap-3 rounded-lg bg-white p-5 shadow-sm md:grid-cols-3 xl:grid-cols-6">
        <input name="date_from" type="date" value="{{ request('date_from') }}" class="rounded-md border-gray-300">
        <input name="date_to" type="date" value="{{ request('date_to') }}" class="rounded-md border-gray-300">
        <select name="facility_id" class="rounded-md border-gray-300"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}" @selected(request('facility_id') == $facility->id)>{{ $facility->name }}</option>@endforeach</select>
        <select name="category" class="rounded-md border-gray-300"><option value="">All categories</option>@foreach (['Cottage','Cabana / Room','Beach Equipment'] as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
        <select name="booking_status" class="rounded-md border-gray-300"><option value="">Booking status</option>@foreach (['pending','approved','cancelled','checked_in'] as $status)<option value="{{ $status }}" @selected(request('booking_status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
        <select name="payment_status" class="rounded-md border-gray-300"><option value="">Payment status</option>@foreach (['pending','paid','failed','refunded'] as $status)<option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
        <button class="rounded-md bg-slate-900 px-4 py-2 text-white xl:col-span-6">Run Report</button>
    </form>
    <div class="mt-6 grid gap-4 sm:grid-cols-4">
        <div class="rounded-lg bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Bookings</p><p class="text-2xl font-bold">{{ $totals['bookings'] }}</p></div>
        <div class="rounded-lg bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Revenue</p><p class="text-2xl font-bold">₱{{ number_format($totals['revenue'], 2) }}</p></div>
        <div class="rounded-lg bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Paid</p><p class="text-2xl font-bold">{{ $totals['paid'] }}</p></div>
        <div class="rounded-lg bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Unpaid</p><p class="text-2xl font-bold">{{ $totals['unpaid'] }}</p></div>
    </div>
    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3">Ref</th><th class="p-3">Tourist</th><th class="p-3">Facility</th><th class="p-3">Date</th><th class="p-3">Status</th><th class="p-3">Total</th></tr></thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr class="border-t">
                        <td class="p-3">{{ $booking->ticket?->reference_number }}</td>
                        <td class="p-3">{{ $booking->user->name }}</td>
                        <td class="p-3">{{ $booking->facility->name }}</td>
                        <td class="p-3">{{ $booking->booking_date->format('Y-m-d') }}</td>
                        <td class="p-3">{{ $booking->booking_status }} / {{ $booking->payment_status }}</td>
                        <td class="p-3">₱{{ number_format($booking->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $bookings->links() }}</div>
@endsection
