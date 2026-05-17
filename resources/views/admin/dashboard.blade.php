@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([
            'Total Bookings' => $summary['total_bookings'],
            'Total Revenue' => '₱'.number_format($summary['total_revenue'], 2),
            'Pending' => $summary['pending_bookings'],
            'Paid' => $summary['paid_bookings'],
            'Active Clients' => $summary['active_clients'],
        ] as $label => $value)
            <div class="rounded-lg bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow-sm"><canvas id="revenueChart" height="130"></canvas></div>
        <div class="rounded-lg bg-white p-5 shadow-sm"><canvas id="facilityChart" height="130"></canvas></div>
        <div class="rounded-lg bg-white p-5 shadow-sm"><canvas id="bookingStatusChart" height="130"></canvas></div>
        <div class="rounded-lg bg-white p-5 shadow-sm"><canvas id="paymentStatusChart" height="130"></canvas></div>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <div class="border-b p-4 font-semibold">Recent Bookings</div>
        <table class="w-full text-left text-sm">
            <tbody>
                @foreach ($recentBookings as $booking)
                    <tr class="border-b">
                        <td class="p-3">{{ $booking->user->name }}</td>
                        <td class="p-3">{{ $booking->facility->name }}</td>
                        <td class="p-3">{{ ucfirst($booking->booking_status) }}</td>
                        <td class="p-3">{{ ucfirst($booking->payment_status) }}</td>
                        <td class="p-3 text-right"><a href="{{ route('admin.bookings.show', $booking) }}" class="text-cyan-700 font-semibold">Open</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const makeChart = (id, type, labels, data) => new Chart(document.getElementById(id), {
    type,
    data: { labels, datasets: [{ label: id, data, borderColor: '#0891b2', backgroundColor: ['#0891b2','#10b981','#f59e0b','#ef4444','#6366f1'], tension: .35 }] },
    options: { responsive: true, plugins: { legend: { display: type !== 'line' } } }
});
makeChart('revenueChart', 'line', @json($monthlyRevenue->keys()), @json($monthlyRevenue->values()));
makeChart('facilityChart', 'bar', @json($facilityUsage->keys()), @json($facilityUsage->values()));
makeChart('bookingStatusChart', 'doughnut', @json($bookingStatuses->keys()), @json($bookingStatuses->values()));
makeChart('paymentStatusChart', 'doughnut', @json($paymentStatuses->keys()), @json($paymentStatuses->values()));
</script>
@endpush
