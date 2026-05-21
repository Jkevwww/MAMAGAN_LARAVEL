@extends('layouts.admin')

@section('content')
    @php
        $activeFilters = collect(['search', 'date_from', 'date_to', 'facility_id', 'category', 'booking_status', 'payment_status', 'sort'])
            ->filter(fn ($key) => filled(request($key)) && ! ($key === 'sort' && request($key) === 'latest'));
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
        $summaryCards = [
            ['label' => 'Filtered Bookings', 'value' => number_format($summary['total']), 'caption' => 'Rows matching current view', 'tone' => 'bg-cyan-50 text-cyan-700 ring-cyan-100', 'icon' => 'M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z'],
            ['label' => 'Pending Approval', 'value' => number_format($summary['pending']), 'caption' => 'Booking action needed', 'tone' => 'bg-amber-50 text-amber-700 ring-amber-100', 'icon' => 'M12 6v6l4 2M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z'],
            ['label' => 'Pending Payment', 'value' => number_format($summary['pending_payments']), 'caption' => 'Payment verification queue', 'tone' => 'bg-orange-50 text-orange-700 ring-orange-100', 'icon' => 'M12 9v4M12 17h.01M10.29 3.86 1.71-1 1.71 1 8.49 14.7-1.71 3H3.8l-1.71-3 8.2-14.7Z'],
            ['label' => 'Paid Revenue', 'value' => '&#8369;'.number_format($summary['paid_revenue'], 2), 'caption' => number_format($summary['today']).' today / '.number_format($summary['upcoming']).' upcoming', 'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'icon' => 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6'],
        ];
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Operations</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Bookings</h1>
            <p class="mt-1 text-sm text-slate-500">Review guest bookings, verify payments, and prepare upcoming arrivals.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.bookings.index', ['booking_status' => 'pending']) }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700 shadow-sm transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z"/></svg>
                Approval Queue
            </a>
            <a href="{{ route('admin.bookings.index', ['payment_status' => 'pending']) }}" class="inline-flex h-10 items-center gap-2 rounded-lg bg-cyan-700 px-3 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z"/></svg>
                Verify Payments
            </a>
        </div>
    </div>

    <form class="mt-6" x-data="{ filtersOpen: {{ $filtersOpen }} }">
        <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-2 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row">
                    <div class="relative min-w-0 flex-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                        </svg>
                        <input name="search" value="{{ request('search') }}" class="h-10 w-full rounded-lg border-slate-300 pl-9 pr-3 text-sm" placeholder="Search guest, phone, facility, booking ID, payment, or ticket">
                    </div>
                    <button class="h-10 rounded-lg bg-slate-950 px-4 text-sm font-bold text-white transition hover:bg-slate-800">Search</button>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.bookings.index', ['date_from' => today()->toDateString(), 'date_to' => today()->toDateString()]) }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Today</a>
                    <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="relative inline-flex h-10 items-center gap-2 rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/></svg>
                        Filters
                        @if ($activeFilters->isNotEmpty())
                            <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-cyan-700 px-1 text-[10px] font-extrabold text-white ring-2 ring-white">{{ $activeFilters->count() }}</span>
                        @endif
                    </button>
                    @if ($activeFilters->isNotEmpty())
                        <a href="{{ route('admin.bookings.index') }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Reset</a>
                    @endif
                </div>
            </div>

            <div x-show="filtersOpen" x-transition x-cloak class="mt-4 border-t border-slate-100 pt-4">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-8">
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
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Sort
                        <select name="sort" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest created</option>
                            <option value="booking_date_asc" @selected(request('sort') === 'booking_date_asc')>Booking date asc</option>
                            <option value="booking_date_desc" @selected(request('sort') === 'booking_date_desc')>Booking date desc</option>
                            <option value="total_desc" @selected(request('sort') === 'total_desc')>Total high to low</option>
                            <option value="total_asc" @selected(request('sort') === 'total_asc')>Total low to high</option>
                        </select>
                    </label>
                </div>
                <div class="mt-4 flex justify-end">
                    <button class="h-10 rounded-lg bg-cyan-700 px-4 text-sm font-bold text-white transition hover:bg-cyan-600">Apply Filters</button>
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

    <section class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-slate-950">Booking Queue</h2>
                <p class="text-xs text-slate-500">Page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }} with {{ number_format($bookings->total()) }} total results.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'checked_in' => 'Checked In', 'cancelled' => 'Cancelled'] as $status => $label)
                    <a href="{{ route('admin.bookings.index', array_filter(array_merge(request()->except(['page', 'booking_status']), ['booking_status' => $status]))) }}" class="rounded-full px-3 py-1.5 text-xs font-bold ring-1 transition {{ request('booking_status') === $status ? ($statusTone[$status] ?? 'bg-slate-100 text-slate-700 ring-slate-200') : 'bg-white text-slate-600 ring-slate-200 hover:bg-slate-50' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1020px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="p-3">Guest</th>
                        <th class="p-3">Facility</th>
                        <th class="p-3">Schedule</th>
                        <th class="p-3">Booking</th>
                        <th class="p-3">Payment</th>
                        <th class="p-3">Reference</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr class="border-t border-slate-100 transition hover:bg-slate-50">
                            <td class="p-3">
                                <p class="font-bold text-slate-950">{{ $booking->user->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $booking->user->email }}</p>
                                @if ($booking->user->phone)
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $booking->user->phone }}</p>
                                @endif
                            </td>
                            <td class="p-3">
                                <p class="font-semibold text-slate-800">{{ $booking->facility->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $booking->facility->category }} &middot; {{ $booking->quantity }} qty</p>
                            </td>
                            <td class="p-3">
                                <p class="font-semibold text-slate-800">{{ $booking->booking_date->format('M d, Y') }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $booking->start_time ? str($booking->start_time)->substr(0, 5) : 'Whole day' }}{{ $booking->end_time ? ' - '.str($booking->end_time)->substr(0, 5) : '' }}</p>
                            </td>
                            <td class="p-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $statusTone[$booking->booking_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->booking_status) }}</span>
                                <p class="mt-2 text-xs text-slate-500">{{ $booking->guest_count }} guests</p>
                            </td>
                            <td class="p-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $statusTone[$booking->payment_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $formatStatus($booking->payment_status) }}</span>
                                <p class="mt-2 text-xs text-slate-500">{{ $booking->payment?->method ? str($booking->payment->method)->upper() : 'No method' }}</p>
                            </td>
                            <td class="p-3">
                                <p class="font-mono text-xs font-bold text-slate-700">{{ $booking->ticket?->reference_number ?: 'No ticket yet' }}</p>
                                @if ($booking->payment?->reference_number)
                                    <p class="mt-1 font-mono text-xs text-slate-500">{{ $booking->payment->reference_number }}</p>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <p class="font-extrabold text-slate-950">&#8369;{{ number_format($booking->total_amount, 2) }}</p>
                                @if ($booking->discount_amount > 0)
                                    <p class="mt-0.5 text-xs text-emerald-700">-&#8369;{{ number_format($booking->discount_amount, 2) }}</p>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex h-9 items-center rounded-lg bg-slate-950 px-3 text-sm font-bold text-white transition hover:bg-slate-800">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center">
                                <p class="font-bold text-slate-700">No bookings found</p>
                                <p class="mt-1 text-sm text-slate-500">Adjust the search or filters to widen the queue.</p>
                            </td>
                        </tr>
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
@endsection
