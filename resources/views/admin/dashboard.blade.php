@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">Booking, payment, facility, and guest activity overview.</p>
        </div>
        <div class="text-sm text-slate-500">{{ now()->format('F d, Y') }}</div>
    </div>

    @php
        $cards = [
            ['Total Bookings', $summary['total_bookings'], 'All reservations recorded', 'calendar', 'bg-cyan-50 text-cyan-700'],
            ['Total Revenue', '&#8369;'.number_format($summary['total_revenue'], 2), 'Verified paid payments', 'revenue', 'bg-emerald-50 text-emerald-700'],
            ['Pending Bookings', $summary['pending_bookings'], 'Waiting for action', 'pending', 'bg-amber-50 text-amber-700'],
            ['Paid Bookings', $summary['paid_bookings'], 'Ready for QR check-in', 'paid', 'bg-indigo-50 text-indigo-700'],
            ['Active Clients', $summary['active_clients'], 'Verified guest accounts', 'users', 'bg-slate-100 text-slate-700'],
        ];
        $iconPaths = [
            'calendar' => '<path d="M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>',
            'revenue' => '<path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>',
            'pending' => '<path d="M12 6v6l4 2M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z"/>',
            'paid' => '<path d="m9 12 2 2 4-4M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z"/>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87"/>',
        ];
    @endphp

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($cards as [$label, $value, $caption, $icon, $tone])
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{!! $value !!}</p>
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-lg {{ $tone }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $iconPaths[$icon] !!}</svg>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-500">{{ $caption }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Today&apos;s Bookings</p>
            <p class="mt-2 text-3xl font-bold">{{ $summary['today_bookings'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Bookable Facilities</p>
            <p class="mt-2 text-3xl font-bold">{{ $summary['bookable_facilities'] }} <span class="text-base font-medium text-slate-400">/ {{ $summary['facilities'] }}</span></p>
        </div>
        <a href="{{ route('admin.checkin.index') }}" class="rounded-xl bg-slate-950 p-4 text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-slate-900 hover:shadow-lg">
            <p class="text-sm text-slate-300">Quick Action</p>
            <p class="mt-2 text-xl font-bold">Open QR Check-In</p>
        </a>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Monthly Revenue</h2>
                <span class="text-xs text-slate-500">Last 6 months</span>
            </div>
            <div class="relative h-72"><canvas id="revenueChart"></canvas></div>
        </section>
        <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Facility Usage</h2>
                <span class="text-xs text-slate-500">Top facilities</span>
            </div>
            <div class="relative h-72"><canvas id="facilityChart"></canvas></div>
        </section>
        <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="mb-4 font-semibold">Booking Status</h2>
            <div class="relative h-72"><canvas id="bookingStatusChart"></canvas></div>
        </section>
        <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="mb-4 font-semibold">Payment Status</h2>
            <div class="relative h-72"><canvas id="paymentStatusChart"></canvas></div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.4fr_.8fr]">
        <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 p-4">
                <h2 class="font-semibold">Recent Bookings</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">View all</a>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr><th class="p-3">Guest</th><th class="p-3">Facility</th><th class="p-3">Status</th><th class="p-3">Payment</th><th class="p-3"></th></tr>
                </thead>
                <tbody>
                    @forelse ($recentBookings as $booking)
                        <tr class="border-t border-slate-100 transition hover:bg-slate-50">
                            <td class="p-3">{{ $booking->user->name }}</td>
                            <td class="p-3">{{ $booking->facility->name }}</td>
                            <td class="p-3">{{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}</td>
                            <td class="p-3">{{ ucfirst($booking->payment_status) }}</td>
                            <td class="p-3 text-right"><a href="{{ route('admin.bookings.show', $booking) }}" class="font-semibold text-cyan-700">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-slate-500">No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="font-semibold">Facility Inventory</h2>
            <div class="mt-4 grid gap-3">
                @forelse ($lowInventoryFacilities as $facility)
                    <div class="rounded-lg border border-slate-200 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold">{{ $facility->name }}</p>
                                <p class="text-sm text-slate-500">{{ $facility->category }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-sm font-semibold">{{ $facility->inventory_count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No facilities found.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    const palette = ['#0891b2', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#64748b', '#14b8a6', '#8b5cf6'];
    const emptyPlugin = {
        id: 'emptyState',
        afterDraw(chart) {
            const values = chart.data.datasets.flatMap((dataset) => dataset.data || []);
            const hasData = values.some((value) => Number(value) > 0);

            if (hasData) {
                return;
            }

            const { ctx, chartArea } = chart;
            ctx.save();
            ctx.fillStyle = '#64748b';
            ctx.textAlign = 'center';
            ctx.font = '500 14px Figtree, sans-serif';
            ctx.fillText('No data yet', (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
            ctx.restore();
        }
    };

    Chart.register(emptyPlugin);

    const makeChart = (id, type, labels, data, label) => {
        const canvas = document.getElementById(id);

        if (!canvas) {
            return;
        }

        new Chart(canvas, {
            type,
            data: {
                labels,
                datasets: [{
                    label,
                    data: data.map((value) => Number(value)),
                    borderColor: '#0891b2',
                    backgroundColor: type === 'line' ? 'rgba(8, 145, 178, .14)' : palette,
                    fill: type === 'line',
                    tension: .35,
                    borderWidth: 2
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: { display: type !== 'line' && type !== 'bar', position: 'bottom' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: type === 'doughnut' ? {} : {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    };

    makeChart('revenueChart', 'line', @json($monthlyRevenue->keys()->values()), @json($monthlyRevenue->values()->values()), 'Revenue');
    makeChart('facilityChart', 'bar', @json($facilityUsage->keys()->values()), @json($facilityUsage->values()->values()), 'Bookings');
    makeChart('bookingStatusChart', 'doughnut', @json($bookingStatuses->keys()->values()->map(fn ($status) => ucfirst(str_replace('_', ' ', $status)))), @json($bookingStatuses->values()->values()), 'Bookings');
    makeChart('paymentStatusChart', 'doughnut', @json($paymentStatuses->keys()->values()->map(fn ($status) => ucfirst($status))), @json($paymentStatuses->values()->values()), 'Payments');
})();
</script>
@endpush
