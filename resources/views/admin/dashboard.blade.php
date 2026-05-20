@extends('layouts.admin')

@section('content')
    @php
        $isAdmin = auth()->user()?->isAdmin() ?? false;
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
        $kpis = [
            ['label' => 'Revenue', 'value' => '&#8369;'.number_format($summary['total_revenue'], 2), 'caption' => 'Verified paid payments', 'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'icon' => 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6'],
            ['label' => 'Bookings', 'value' => number_format($summary['total_bookings']), 'caption' => 'Total reservations', 'tone' => 'bg-cyan-50 text-cyan-700 ring-cyan-100', 'icon' => 'M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z'],
            ['label' => 'Active Clients', 'value' => number_format($summary['active_clients']), 'caption' => 'Guest accounts', 'tone' => 'bg-indigo-50 text-indigo-700 ring-indigo-100', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87'],
            ['label' => 'Facilities', 'value' => $summary['bookable_facilities'].' / '.$summary['facilities'], 'caption' => 'Bookable and total', 'tone' => 'bg-slate-100 text-slate-700 ring-slate-200', 'icon' => 'M3 21h18M5 21V7l8-4 6 4v14M9 21v-6h6v6M9 10h.01M13 10h.01M17 10h.01'],
        ];
        $workItems = [
            ['label' => 'Pending bookings', 'value' => $summary['pending_bookings'], 'href' => route('admin.bookings.index', ['booking_status' => 'pending']), 'tone' => 'bg-amber-50 text-amber-700', 'bar' => 'bg-amber-500'],
            ['label' => 'Pending payments', 'value' => $summary['pending_payments'], 'href' => route('admin.bookings.index', ['payment_status' => 'pending']), 'tone' => 'bg-orange-50 text-orange-700', 'bar' => 'bg-orange-500'],
            ['label' => 'Today bookings', 'value' => $summary['today_bookings'], 'href' => route('admin.bookings.index'), 'tone' => 'bg-cyan-50 text-cyan-700', 'bar' => 'bg-cyan-600'],
            ['label' => 'Low inventory', 'value' => $summary['low_inventory'], 'href' => $isAdmin ? route('admin.facilities.index') : route('admin.dashboard'), 'tone' => 'bg-rose-50 text-rose-700', 'bar' => 'bg-rose-500'],
        ];
        $quickActions = collect([
            ['label' => 'Manage Bookings', 'href' => route('admin.bookings.index'), 'caption' => 'Review approvals and payments', 'icon' => 'M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z', 'visible' => true],
            ['label' => 'QR Check-In', 'href' => route('admin.checkin.index'), 'caption' => 'Validate paid guest tickets', 'icon' => 'M3 5h6v6H3V5Zm12 0h6v6h-6V5ZM3 17h6v2H5v2H3v-4Zm14 0h4v4h-6v-2h2v-2ZM7 7H5v2h2V7Zm12 0h-2v2h2V7Z', 'visible' => true],
            ['label' => 'Reports', 'href' => route('admin.reports.index'), 'caption' => 'Export booking and revenue data', 'icon' => 'M4 19.5V4a2 2 0 0 1 2-2h9l5 5v12.5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2ZM14 2v6h6M8 13h8M8 17h5', 'visible' => true],
            ['label' => 'Facilities', 'href' => $isAdmin ? route('admin.facilities.index') : '#', 'caption' => 'Update availability and inventory', 'icon' => 'M3 21h18M5 21V7l8-4 6 4v14M9 21v-6h6v6M9 10h.01M13 10h.01M17 10h.01', 'visible' => $isAdmin],
        ])->filter(fn ($action) => $action['visible']);
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">{{ now()->format('l, F d, Y') }}</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">A compact view of reservations, payments, check-ins, and facility stock.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.bookings.index', ['booking_status' => 'pending']) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z"/></svg>
                Review Queue
            </a>
            <a href="{{ route('admin.checkin.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-cyan-700 px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h6v6H3V5Zm12 0h6v6h-6V5ZM3 17h6v2H5v2H3v-4Zm14 0h4v4h-6v-2h2v-2Z"/></svg>
                QR Check-In
            </a>
        </div>
    </div>

    <div class="mt-6 grid gap-4 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($kpis as $kpi)
                    <div class="rounded-lg border border-slate-100 bg-slate-50/60 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $kpi['label'] }}</p>
                                <p class="mt-2 truncate text-2xl font-extrabold text-slate-950">{!! $kpi['value'] !!}</p>
                            </div>
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg ring-1 {{ $kpi['tone'] }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $kpi['icon'] }}"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">{{ $kpi['caption'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-xl bg-slate-950 p-4 text-white shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold">Operations Queue</h2>
                    <p class="mt-1 text-xs text-slate-400">Items that may need action today.</p>
                </div>
                <span class="rounded-full bg-white/10 px-2.5 py-1 text-xs font-bold text-cyan-100">{{ $summary['approved_bookings'] }} approved</span>
            </div>
            <div class="mt-4 grid gap-2">
                @foreach ($workItems as $item)
                    <a href="{{ $item['href'] }}" class="group relative overflow-hidden rounded-lg bg-white/5 p-3 transition hover:bg-white/10">
                        <span class="absolute inset-y-0 left-0 w-1 {{ $item['bar'] }}"></span>
                        <span class="flex items-center justify-between gap-3 pl-2">
                            <span class="text-sm font-semibold text-slate-200">{{ $item['label'] }}</span>
                            <span class="rounded-full px-2 py-1 text-sm font-extrabold {{ $item['tone'] }}">{{ $item['value'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,.65fr)]">
        <div class="grid gap-6">
            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-bold text-slate-950">Revenue Trend</h2>
                        <p class="text-xs text-slate-500">Paid payment total for the last 6 months.</p>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="text-sm font-bold text-cyan-700 hover:text-cyan-800">Open reports</a>
                </div>
                <div class="mt-4 h-64"><canvas id="revenueChart"></canvas></div>
            </section>

            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-bold text-slate-950">Recent Bookings</h2>
                        <p class="text-xs text-slate-500">Latest guest activity across the booking workflow.</p>
                    </div>
                    <a href="{{ route('admin.bookings.index') }}" class="text-sm font-bold text-cyan-700 hover:text-cyan-800">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="p-3">Guest</th>
                                <th class="p-3">Facility</th>
                                <th class="p-3">Date</th>
                                <th class="p-3">Booking</th>
                                <th class="p-3">Payment</th>
                                <th class="p-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentBookings as $booking)
                                <tr class="border-t border-slate-100 transition hover:bg-slate-50">
                                    <td class="p-3">
                                        <p class="font-semibold text-slate-900">{{ $booking->user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $booking->user->email }}</p>
                                    </td>
                                    <td class="p-3 text-slate-700">{{ $booking->facility->name }}</td>
                                    <td class="p-3 text-slate-600">{{ $booking->booking_date->format('M d, Y') }}</td>
                                    <td class="p-3">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-bold ring-1 {{ $statusTone[$booking->booking_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->booking_status) }}</span>
                                    </td>
                                    <td class="p-3">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-bold ring-1 {{ $statusTone[$booking->payment_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->payment_status) }}</span>
                                    </td>
                                    <td class="p-3 text-right"><a href="{{ route('admin.bookings.show', $booking) }}" class="font-bold text-cyan-700 hover:text-cyan-800">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-8 text-center text-slate-500">No bookings yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="grid content-start gap-6">
            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <h2 class="font-bold text-slate-950">Quick Actions</h2>
                <div class="mt-4 grid gap-2">
                    @foreach ($quickActions as $action)
                        <a href="{{ $action['href'] }}" class="group flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:border-cyan-200 hover:bg-cyan-50">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-slate-100 text-slate-700 transition group-hover:bg-white group-hover:text-cyan-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/></svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block font-bold text-slate-900">{{ $action['label'] }}</span>
                                <span class="block truncate text-xs text-slate-500">{{ $action['caption'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-slate-950">Upcoming Bookings</h2>
                        <p class="text-xs text-slate-500">Next pending or approved arrivals.</p>
                    </div>
                    <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-bold text-cyan-700">{{ $upcomingBookings->count() }}</span>
                </div>
                <div class="mt-4 grid gap-3">
                    @forelse ($upcomingBookings as $booking)
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="rounded-lg border border-slate-200 p-3 transition hover:border-cyan-200 hover:bg-cyan-50">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-slate-900">{{ $booking->facility->name }}</p>
                                    <p class="mt-1 truncate text-sm text-slate-500">{{ $booking->user->name }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-2 py-1 text-xs font-bold ring-1 {{ $statusTone[$booking->booking_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->booking_status) }}</span>
                            </div>
                            <p class="mt-3 text-xs font-semibold text-slate-600">{{ $booking->booking_date->format('M d, Y') }}{{ $booking->start_time ? ' at '.str($booking->start_time)->substr(0, 5) : '' }}</p>
                        </a>
                    @empty
                        <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No upcoming bookings found.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-1">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="font-bold text-slate-950">Booking Status</h2>
                            <span class="text-xs font-semibold text-slate-500">{{ $summary['cancelled_bookings'] }} cancelled</span>
                        </div>
                        <div class="mt-3 h-52"><canvas id="bookingStatusChart"></canvas></div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="font-bold text-slate-950">Payment Status</h2>
                            <span class="text-xs font-semibold text-slate-500">{{ $summary['paid_bookings'] }} paid</span>
                        </div>
                        <div class="mt-3 h-52"><canvas id="paymentStatusChart"></canvas></div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-slate-950">Facility Usage</h2>
                    <p class="text-xs text-slate-500">Most booked facilities by reservation count.</p>
                </div>
                @if ($isAdmin)
                    <a href="{{ route('admin.facilities.index') }}" class="text-sm font-bold text-cyan-700 hover:text-cyan-800">Manage facilities</a>
                @endif
            </div>
            <div class="mt-4 h-72"><canvas id="facilityChart"></canvas></div>
        </section>

        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-slate-950">Inventory Watch</h2>
                    <p class="text-xs text-slate-500">Lowest active facility inventory.</p>
                </div>
                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700">{{ $summary['low_inventory'] }} low</span>
            </div>
            <div class="mt-4 grid gap-3">
                @forelse ($lowInventoryFacilities as $facility)
                    <div class="rounded-lg border border-slate-200 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-bold text-slate-900">{{ $facility->name }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $facility->category }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-sm font-extrabold {{ $facility->inventory_count <= 3 ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-700' }}">{{ $facility->inventory_count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No facilities found.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    if (typeof Chart === 'undefined') {
        return;
    }

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
            ctx.font = '600 13px Figtree, sans-serif';
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
                    borderWidth: 2,
                    borderRadius: type === 'bar' ? 8 : 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                cutout: type === 'doughnut' ? '64%' : undefined,
                plugins: {
                    legend: { display: type === 'doughnut', position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: type === 'doughnut' ? {} : {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e2e8f0' } },
                    x: { grid: { display: false }, ticks: { maxRotation: 0 } }
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
