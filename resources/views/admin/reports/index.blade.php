@extends('layouts.admin')

@section('content')
    @php
        $activeFilters = collect(['search', 'date_from', 'date_to', 'facility_id', 'category', 'booking_status', 'payment_status'])
            ->filter(fn ($key) => filled(request($key)));
        $filtersOpen = $activeFilters->isNotEmpty() ? 'true' : 'false';
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
        $maxBookingStatus = max(1, $bookingStatusBreakdown->max());
        $maxPaymentStatus = max(1, $paymentStatusBreakdown->max());
        $maxCategory = max(1, $categoryBreakdown->max() ?? 0);
        $summaryCards = [
            ['label' => 'Bookings', 'value' => number_format($totals['bookings']), 'caption' => 'Rows in this report', 'tone' => 'bg-cyan-50 text-cyan-700 ring-cyan-100', 'icon' => 'M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z'],
            ['label' => 'Revenue', 'value' => '&#8369;'.number_format($totals['revenue'], 2), 'caption' => 'Paid bookings only', 'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'icon' => 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6'],
            ['label' => 'Paid / Unpaid', 'value' => number_format($totals['paid']).' / '.number_format($totals['unpaid']), 'caption' => 'Payment completion', 'tone' => 'bg-indigo-50 text-indigo-700 ring-indigo-100', 'icon' => 'm9 12 2 2 4-4M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z'],
            ['label' => 'Avg. Paid Value', 'value' => '&#8369;'.number_format($totals['average_paid'], 2), 'caption' => number_format($totals['guests']).' unique guests', 'tone' => 'bg-slate-100 text-slate-700 ring-slate-200', 'icon' => 'M4 19V5M4 19h16M8 17v-6M12 17V7M16 17v-9'],
        ];
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Reports</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Booking Reports</h1>
            <p class="mt-1 text-sm text-slate-500">Analyze bookings, revenue, payment status, and facility demand.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="inline-flex items-center gap-2 rounded-lg bg-cyan-700 px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M4 21h16"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    <form class="mt-6" x-data="{ filtersOpen: {{ $filtersOpen }} }">
        <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row">
                    <div class="relative min-w-0 flex-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                        <input name="search" value="{{ request('search') }}" class="h-10 w-full rounded-lg border-slate-300 pl-9 pr-3 text-sm" placeholder="Search guest, email, facility, or ticket">
                    </div>
                    <button class="h-10 rounded-lg bg-slate-950 px-4 text-sm font-bold text-white transition hover:bg-slate-800">Run Report</button>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="relative inline-flex h-10 items-center gap-2 rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/></svg>
                        Filters
                        @if ($activeFilters->isNotEmpty())
                            <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-cyan-700 px-1 text-[10px] font-extrabold text-white ring-2 ring-white">{{ $activeFilters->count() }}</span>
                        @endif
                    </button>
                    @if ($activeFilters->isNotEmpty())
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Reset</a>
                    @endif
                </div>
            </div>

            <div x-show="filtersOpen" x-transition x-cloak class="mt-4 border-t border-slate-100 pt-4">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                    <label class="grid gap-1 text-sm font-bold text-slate-700">From
                        <input name="date_from" type="date" value="{{ request('date_from') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">To
                        <input name="date_to" type="date" value="{{ request('date_to') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700 xl:col-span-2">Facility
                        <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            <option value="">All facilities</option>
                            @foreach ($facilities as $facility)
                                <option value="{{ $facility->id }}" @selected(request('facility_id') == $facility->id)>{{ $facility->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Category
                        <select name="category" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Booking
                        <select name="booking_status" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            <option value="">All statuses</option>
                            @foreach (['pending','approved','checked_in','cancelled'] as $status)
                                <option value="{{ $status }}" @selected(request('booking_status') === $status)>{{ $formatStatus($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Payment
                        <select name="payment_status" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            <option value="">All statuses</option>
                            @foreach (['pending','paid','failed','refunded'] as $status)
                                <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ $formatStatus($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>
        </div>
    </form>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($summaryCards as $card)
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-2 truncate text-2xl font-extrabold text-slate-950">{!! $card['value'] !!}</p>
                    </div>
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg ring-1 {{ $card['tone'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-slate-500">{{ $card['caption'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-slate-950">Status Breakdown</h2>
                    <p class="text-xs text-slate-500">Current booking and payment distribution for the selected filters.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ number_format($totals['bookings']) }} rows</span>
            </div>
            <div class="mt-4 grid gap-5 lg:grid-cols-2">
                <div class="grid gap-3">
                    <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500">Booking Status</h3>
                    @foreach ($bookingStatusBreakdown as $status => $count)
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                                <span class="font-semibold text-slate-700">{{ $formatStatus($status) }}</span>
                                <span class="font-bold text-slate-950">{{ $count }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-cyan-600" style="width: {{ ($count / $maxBookingStatus) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="grid gap-3">
                    <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500">Payment Status</h3>
                    @foreach ($paymentStatusBreakdown as $status => $count)
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                                <span class="font-semibold text-slate-700">{{ $formatStatus($status) }}</span>
                                <span class="font-bold text-slate-950">{{ $count }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-emerald-600" style="width: {{ ($count / $maxPaymentStatus) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <h2 class="font-bold text-slate-950">Category Demand</h2>
            <p class="mt-1 text-xs text-slate-500">Bookings grouped by facility category.</p>
            <div class="mt-4 grid gap-3">
                @forelse ($categoryBreakdown as $category => $count)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                            <span class="truncate font-semibold text-slate-700">{{ $category }}</span>
                            <span class="font-bold text-slate-950">{{ $count }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-indigo-600" style="width: {{ ($count / $maxCategory) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No category data found.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-slate-950">Report Rows</h2>
                    <p class="text-xs text-slate-500">Detailed booking records matching the active filters.</p>
                </div>
                <span class="text-xs font-semibold text-slate-500">Page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="p-3">Reference</th>
                            <th class="p-3">Guest</th>
                            <th class="p-3">Facility</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">Booking</th>
                            <th class="p-3">Payment</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr class="border-t border-slate-100 transition hover:bg-slate-50">
                                <td class="p-3">
                                    <span class="font-mono text-xs font-bold text-slate-700">{{ $booking->ticket?->reference_number ?: 'None' }}</span>
                                </td>
                                <td class="p-3">
                                    <p class="font-semibold text-slate-950">{{ $booking->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $booking->user->email }}</p>
                                </td>
                                <td class="p-3">
                                    <p class="font-semibold text-slate-800">{{ $booking->facility->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $booking->facility->category }}</p>
                                </td>
                                <td class="p-3 text-slate-600">{{ $booking->booking_date->format('M d, Y') }}</td>
                                <td class="p-3">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-bold ring-1 {{ $statusTone[$booking->booking_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->booking_status) }}</span>
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-bold ring-1 {{ $statusTone[$booking->payment_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->payment_status) }}</span>
                                </td>
                                <td class="p-3 text-right font-bold text-slate-950">&#8369;{{ number_format($booking->total_amount, 2) }}</td>
                                <td class="p-3 text-right"><a href="{{ route('admin.bookings.show', $booking) }}" class="font-bold text-cyan-700 hover:text-cyan-800">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-8 text-center text-slate-500">No report rows found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-3 border-t border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm font-semibold text-slate-500">
                    @if ($bookings->total())
                        Showing {{ $bookings->firstItem() }}-{{ $bookings->lastItem() }} of {{ $bookings->total() }}
                    @else
                        Showing 0 of 0
                    @endif
                </p>
                <div class="flex items-center gap-2">
                    @if ($bookings->onFirstPage())
                        <span class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                            Previous
                        </span>
                    @else
                        <a href="{{ $bookings->previousPageUrl() }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                            Previous
                        </a>
                    @endif

                    <span class="inline-flex h-9 items-center rounded-lg bg-slate-100 px-3 text-sm font-bold text-slate-600">
                        Page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }}
                    </span>

                    @if ($bookings->hasMorePages())
                        <a href="{{ $bookings->nextPageUrl() }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-cyan-700 px-3 text-sm font-bold text-white transition hover:bg-cyan-600">
                            Next
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                        </a>
                    @else
                        <span class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-400">
                            Next
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                        </span>
                    @endif
                </div>
            </div>
        </section>

        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-slate-950">Top Facilities</h2>
                    <p class="mt-1 text-xs text-slate-500">Ranked by bookings in this report.</p>
                </div>
                <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-bold text-cyan-700">{{ $topFacilities->count() }}</span>
            </div>
            <div class="mt-4 grid gap-3">
                @forelse ($topFacilities as $facility)
                    <div class="rounded-lg border border-slate-200 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-bold text-slate-950">{{ $facility['name'] }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $facility['category'] }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-sm font-extrabold text-slate-700">{{ $facility['bookings'] }}</span>
                        </div>
                        <p class="mt-3 text-xs font-semibold text-slate-600">&#8369;{{ number_format($facility['revenue'], 2) }} paid revenue</p>
                    </div>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No facility data found.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
