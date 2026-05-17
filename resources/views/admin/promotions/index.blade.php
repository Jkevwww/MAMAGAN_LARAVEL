@extends('layouts.admin')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Promotions</h1>
        <p class="mt-1 text-sm text-slate-500">Create, update, activate, and delete promo codes.</p>
    </div>

    <details class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200" open>
        <summary class="cursor-pointer font-semibold">Create Promotion</summary>
        <form method="POST" action="{{ route('admin.promotions.store') }}" class="mt-4 grid gap-3 md:grid-cols-3">
            @csrf
            <input name="name" class="rounded-lg border-gray-300" placeholder="Name" required>
            <input name="code" class="rounded-lg border-gray-300 uppercase" placeholder="Code" required>
            <select name="discount_type" class="rounded-lg border-gray-300"><option value="percent">Percent</option><option value="fixed">Fixed</option></select>
            <input name="discount_value" type="number" step="0.01" class="rounded-lg border-gray-300" placeholder="Discount value" required>
            <input name="minimum_amount" type="number" step="0.01" class="rounded-lg border-gray-300" placeholder="Minimum amount">
            <input name="usage_limit" type="number" class="rounded-lg border-gray-300" placeholder="Usage limit">
            <input name="starts_at" type="date" class="rounded-lg border-gray-300">
            <input name="ends_at" type="date" class="rounded-lg border-gray-300">
            <select name="facility_id" class="rounded-lg border-gray-300"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}">{{ $facility->name }}</option>@endforeach</select>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            <button class="rounded-lg bg-cyan-700 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600 md:col-span-2">Save Promotion</button>
        </form>
    </details>

    <div class="mt-6 grid gap-4">
        @foreach ($promotions as $promotion)
            <details class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <summary class="cursor-pointer list-none">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="font-bold">{{ $promotion->code }}</div>
                            <div class="text-sm text-slate-500">{{ $promotion->name }} &middot; {{ $promotion->discount_type }} {{ $promotion->discount_value }} &middot; {{ $promotion->used_count }} / {{ $promotion->usage_limit ?: 'unlimited' }}</div>
                        </div>
                        <span class="w-fit rounded-full px-2 py-1 text-xs font-semibold {{ $promotion->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $promotion->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                </summary>
                <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}" class="mt-4 grid gap-3 border-t border-slate-100 pt-4 md:grid-cols-3">
                    @csrf @method('PATCH')
                    <input name="name" value="{{ $promotion->name }}" class="rounded-lg border-gray-300" required>
                    <input name="code" value="{{ $promotion->code }}" class="rounded-lg border-gray-300 uppercase" required>
                    <select name="discount_type" class="rounded-lg border-gray-300"><option value="percent" @selected($promotion->discount_type === 'percent')>Percent</option><option value="fixed" @selected($promotion->discount_type === 'fixed')>Fixed</option></select>
                    <input name="discount_value" type="number" step="0.01" value="{{ $promotion->discount_value }}" class="rounded-lg border-gray-300" required>
                    <input name="minimum_amount" type="number" step="0.01" value="{{ $promotion->minimum_amount }}" class="rounded-lg border-gray-300">
                    <input name="usage_limit" type="number" value="{{ $promotion->usage_limit }}" class="rounded-lg border-gray-300">
                    <input name="starts_at" type="date" value="{{ $promotion->starts_at?->format('Y-m-d') }}" class="rounded-lg border-gray-300">
                    <input name="ends_at" type="date" value="{{ $promotion->ends_at?->format('Y-m-d') }}" class="rounded-lg border-gray-300">
                    <select name="facility_id" class="rounded-lg border-gray-300"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}" @selected($promotion->facility_id === $facility->id)>{{ $facility->name }}</option>@endforeach</select>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked($promotion->is_active)> Active</label>
                    <div class="flex gap-2 md:col-span-2">
                        <button class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">Update</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" class="mt-3" onsubmit="return confirm('Delete promotion?')">
                    @csrf @method('DELETE')
                    <button class="text-sm font-semibold text-red-700 hover:text-red-800">Delete promotion</button>
                </form>
            </details>
        @endforeach
    </div>

    <div class="mt-6">{{ $promotions->links() }}</div>
@endsection
