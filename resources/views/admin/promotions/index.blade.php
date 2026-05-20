@extends('layouts.admin')

@section('content')
    @php
        $activeFilters = collect(['search', 'status', 'discount_type', 'facility_id'])->filter(fn ($key) => filled(request($key)));
        $filtersOpen = $activeFilters->isNotEmpty() ? 'true' : 'false';
        $today = now()->startOfDay();
        $maxDiscountCount = max(1, $discountBreakdown->max() ?? 0);
        $summaryCards = [
            ['label' => 'Promos', 'value' => number_format($summary['total']), 'caption' => 'Matching filters', 'tone' => 'bg-cyan-50 text-cyan-700 ring-cyan-100', 'icon' => 'M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7M4 12h16M12 3v18M12 3H8.5a3.5 3.5 0 1 0 0 7H12M12 3h3.5a3.5 3.5 0 1 1 0 7H12'],
            ['label' => 'Usable Now', 'value' => number_format($summary['usable']), 'caption' => 'Active and available', 'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'icon' => 'm9 12 2 2 4-4M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z'],
            ['label' => 'Inactive', 'value' => number_format($summary['inactive']), 'caption' => number_format($summary['expired']).' expired', 'tone' => 'bg-slate-100 text-slate-700 ring-slate-200', 'icon' => 'M18.36 18.36A9 9 0 0 1 5.64 5.64M5.64 18.36A9 9 0 0 1 18.36 5.64M3 3l18 18'],
            ['label' => 'Total Uses', 'value' => number_format($summary['used']), 'caption' => 'Redeemed promo count', 'tone' => 'bg-indigo-50 text-indigo-700 ring-indigo-100', 'icon' => 'M4 19V5M4 19h16M8 17v-6M12 17V7M16 17v-9'],
        ];
        $promoState = function ($promotion) use ($today) {
            if (! $promotion->is_active) {
                return ['Inactive', 'bg-slate-100 text-slate-700 ring-slate-200'];
            }

            if ($promotion->starts_at?->gt($today)) {
                return ['Upcoming', 'bg-indigo-50 text-indigo-700 ring-indigo-200'];
            }

            if ($promotion->ends_at?->lt($today)) {
                return ['Expired', 'bg-rose-50 text-rose-700 ring-rose-200'];
            }

            if ($promotion->usage_limit && $promotion->used_count >= $promotion->usage_limit) {
                return ['Used Up', 'bg-amber-50 text-amber-700 ring-amber-200'];
            }

            return ['Usable', 'bg-emerald-50 text-emerald-700 ring-emerald-200'];
        };
        $discountLabel = fn ($promotion) => $promotion->discount_type === 'percent'
            ? number_format($promotion->discount_value, 2).'% off'
            : '&#8369;'.number_format($promotion->discount_value, 2).' off';
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Revenue</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Promotions</h1>
            <p class="mt-1 text-sm text-slate-500">Create, monitor, and update promo codes for bookings.</p>
        </div>
        <a href="#create-promotion" class="inline-flex w-fit items-center gap-2 rounded-lg bg-cyan-700 px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
            New Promotion
        </a>
    </div>

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <p class="font-bold">Please fix the promotion details.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
        <section id="create-promotion" class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-bold text-slate-950">Create Promotion</h2>
                    <p class="mt-1 text-xs text-slate-500">Set code, discount, limits, dates, and facility scope.</p>
                </div>
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-cyan-50 text-cyan-700 ring-1 ring-cyan-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7M4 12h16M12 3v18"/></svg>
                </span>
            </div>

            <form method="POST" action="{{ route('admin.promotions.store') }}" class="mt-4 grid gap-3">
                @csrf
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Name
                        <input name="name" value="{{ old('name') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" placeholder="Summer discount" required>
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Code
                        <input name="code" value="{{ old('code') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal uppercase" placeholder="SUMMER10" required>
                    </label>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Discount type
                        <select name="discount_type" class="h-10 rounded-lg border-slate-300 text-sm font-normal">
                            <option value="percent" @selected(old('discount_type', 'percent') === 'percent')>Percent</option>
                            <option value="fixed" @selected(old('discount_type') === 'fixed')>Fixed amount</option>
                        </select>
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Value
                        <input name="discount_value" type="number" step="0.01" min="0" value="{{ old('discount_value') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" placeholder="10.00" required>
                    </label>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Minimum amount
                        <input name="minimum_amount" type="number" step="0.01" min="0" value="{{ old('minimum_amount') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" placeholder="0.00">
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Usage limit
                        <input name="usage_limit" type="number" min="1" value="{{ old('usage_limit') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" placeholder="Unlimited">
                    </label>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Starts
                        <input name="starts_at" type="date" value="{{ old('starts_at') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal">
                    </label>
                    <label class="grid gap-1 text-sm font-bold text-slate-700">Ends
                        <input name="ends_at" type="date" value="{{ old('ends_at') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal">
                    </label>
                </div>
                <label class="grid gap-1 text-sm font-bold text-slate-700">Facility scope
                    <select name="facility_id" class="h-10 rounded-lg border-slate-300 text-sm font-normal">
                        <option value="">All facilities</option>
                        @foreach ($facilities as $facility)
                            <option value="{{ $facility->id }}" @selected(old('facility_id') == $facility->id)>{{ $facility->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-cyan-700" @checked(old('is_active', '1'))>
                    Active immediately
                </label>
                <button class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-cyan-700 px-4 text-sm font-bold text-white transition hover:bg-cyan-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2ZM17 21v-8H7v8M7 3v5h8"/></svg>
                    Save Promotion
                </button>
            </form>
        </section>

        <div class="grid content-start gap-6">
            <form class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200" x-data="{ filtersOpen: {{ $filtersOpen }} }">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row">
                        <div class="relative min-w-0 flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                            <input name="search" value="{{ request('search') }}" class="h-10 w-full rounded-lg border-slate-300 pl-9 pr-3 text-sm" placeholder="Search code, name, facility, or category">
                        </div>
                        <button class="h-10 rounded-lg bg-slate-950 px-4 text-sm font-bold text-white transition hover:bg-slate-800">Search</button>
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
                            <a href="{{ route('admin.promotions.index') }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Reset</a>
                        @endif
                    </div>
                </div>

                <div x-show="filtersOpen" x-transition x-cloak class="mt-4 border-t border-slate-100 pt-4">
                    <div class="grid gap-3 md:grid-cols-3">
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Status
                            <select name="status" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                                <option value="">All statuses</option>
                                <option value="active" @selected(request('status') === 'active')>Active</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                            </select>
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Discount
                            <select name="discount_type" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                                <option value="">All types</option>
                                <option value="percent" @selected(request('discount_type') === 'percent')>Percent</option>
                                <option value="fixed" @selected(request('discount_type') === 'fixed')>Fixed amount</option>
                            </select>
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Facility
                            <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                                <option value="">Any facility</option>
                                <option value="all" @selected(request('facility_id') === 'all')>All facilities only</option>
                                @foreach ($facilities as $facility)
                                    <option value="{{ $facility->id }}" @selected(request('facility_id') == $facility->id)>{{ $facility->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
            </form>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($summaryCards as $card)
                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                                <p class="mt-2 truncate text-2xl font-extrabold text-slate-950">{{ $card['value'] }}</p>
                            </div>
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg ring-1 {{ $card['tone'] }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">{{ $card['caption'] }}</p>
                    </div>
                @endforeach
            </div>

            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <h2 class="font-bold text-slate-950">Discount Mix</h2>
                <div class="mt-4 grid gap-3">
                    @forelse ($discountBreakdown as $type => $count)
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                                <span class="font-semibold text-slate-700">{{ ucfirst($type) }}</span>
                                <span class="font-bold text-slate-950">{{ $count }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-cyan-600" style="width: {{ ($count / $maxDiscountCount) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No promotion data found.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <section class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-slate-950">Promotion Codes</h2>
                <p class="text-xs text-slate-500">Showing 10 promotions per page. Open a row to edit.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Page {{ $promotions->currentPage() }} of {{ $promotions->lastPage() }}</span>
        </div>

        <div class="grid gap-2 p-3">
            @forelse ($promotions as $promotion)
                @php
                    [$stateLabel, $stateClasses] = $promoState($promotion);
                    $limitLabel = $promotion->usage_limit ? $promotion->used_count.' / '.$promotion->usage_limit : $promotion->used_count.' / unlimited';
                @endphp
                <details class="group rounded-lg border border-slate-200 bg-white px-3 py-2.5 transition open:border-cyan-200 open:bg-cyan-50/30">
                    <summary class="cursor-pointer list-none">
                        <div class="grid gap-2 lg:grid-cols-[180px_minmax(0,1fr)_130px_140px_88px_20px] lg:items-center">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-bold ring-1 {{ $stateClasses }}">{{ $stateLabel }}</span>
                                    <p class="truncate font-mono text-sm font-extrabold text-slate-950">{{ $promotion->code }}</p>
                                </div>
                                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $promotion->name }}</p>
                            </div>
                            <p class="truncate text-sm font-semibold text-slate-700">{{ $promotion->facility?->name ?? 'All facilities' }}</p>
                            <p class="text-sm font-extrabold text-slate-950">{!! $discountLabel($promotion) !!}</p>
                            <p class="text-xs font-semibold text-slate-500">
                                {{ $promotion->starts_at?->format('M d') ?? 'Anytime' }} - {{ $promotion->ends_at?->format('M d') ?? 'No end' }}
                            </p>
                            <p class="text-xs font-bold text-slate-600">{{ $limitLabel }}</p>
                            <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180 group-open:text-cyan-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </summary>

                    <div class="mt-3 border-t border-slate-200 pt-3">
                        <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}" class="grid gap-2 lg:grid-cols-12 lg:items-end">
                            @csrf
                            @method('PATCH')
                            <label class="grid gap-1 text-xs font-bold text-slate-700 lg:col-span-3">Name
                                <input name="name" value="{{ $promotion->name }}" class="h-9 rounded-md border-slate-300 text-sm font-normal" required>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700 lg:col-span-2">Code
                                <input name="code" value="{{ $promotion->code }}" class="h-9 rounded-md border-slate-300 text-sm font-normal uppercase" required>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700 lg:col-span-2">Type
                                <select name="discount_type" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                                    <option value="percent" @selected($promotion->discount_type === 'percent')>Percent</option>
                                    <option value="fixed" @selected($promotion->discount_type === 'fixed')>Fixed</option>
                                </select>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700">Value
                                <input name="discount_value" type="number" step="0.01" min="0" value="{{ $promotion->discount_value }}" class="h-9 rounded-md border-slate-300 text-sm font-normal" required>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700">Minimum
                                <input name="minimum_amount" type="number" step="0.01" min="0" value="{{ $promotion->minimum_amount }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700">Limit
                                <input name="usage_limit" type="number" min="1" value="{{ $promotion->usage_limit }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700 lg:col-span-2">Facility
                                <select name="facility_id" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                                    <option value="">All facilities</option>
                                    @foreach ($facilities as $facility)
                                        <option value="{{ $facility->id }}" @selected($promotion->facility_id === $facility->id)>{{ $facility->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700">Starts
                                <input name="starts_at" type="date" value="{{ $promotion->starts_at?->format('Y-m-d') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            </label>
                            <label class="grid gap-1 text-xs font-bold text-slate-700">Ends
                                <input name="ends_at" type="date" value="{{ $promotion->ends_at?->format('Y-m-d') }}" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                            </label>
                            <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-700">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-cyan-700" @checked($promotion->is_active)>
                                Active
                            </label>
                            <button class="inline-flex h-9 items-center justify-center rounded-md bg-slate-950 px-3 text-sm font-bold text-white transition hover:bg-slate-800">Update</button>
                        </form>
                        <form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" class="mt-2 flex justify-end" onsubmit="return confirm('Delete promotion?')">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex h-8 items-center gap-2 rounded-md border border-rose-200 bg-white px-3 text-xs font-bold text-rose-700 transition hover:bg-rose-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
                                Delete Promotion
                            </button>
                        </form>
                    </div>
                </details>
            @empty
                <div class="rounded-xl bg-slate-50 p-8 text-center text-sm text-slate-500">No promotions found.</div>
            @endforelse
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-slate-500">
                @if ($promotions->total())
                    Showing {{ $promotions->firstItem() }}-{{ $promotions->lastItem() }} of {{ $promotions->total() }}
                @else
                    Showing 0 of 0
                @endif
            </p>
            <div class="flex items-center gap-2">
                @if ($promotions->onFirstPage())
                    <span class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                        Previous
                    </span>
                @else
                    <a href="{{ $promotions->previousPageUrl() }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                        Previous
                    </a>
                @endif

                <span class="inline-flex h-9 items-center rounded-lg bg-slate-100 px-3 text-sm font-bold text-slate-600">
                    Page {{ $promotions->currentPage() }} of {{ $promotions->lastPage() }}
                </span>

                @if ($promotions->hasMorePages())
                    <a href="{{ $promotions->nextPageUrl() }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-cyan-700 px-3 text-sm font-bold text-white transition hover:bg-cyan-600">
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
