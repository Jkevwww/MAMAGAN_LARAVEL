@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Seasonal Rates</h1>
    <details class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200" open>
        <summary class="cursor-pointer font-semibold">Create Seasonal Rate</summary>
        <form method="POST" action="{{ route('admin.seasonal-rates.store') }}" class="mt-4 grid gap-3 md:grid-cols-5">
            @csrf
            <select name="facility_id" class="rounded-lg border-gray-300" required>@foreach ($facilities as $facility)<option value="{{ $facility->id }}">{{ $facility->name }}</option>@endforeach</select>
            <input name="name" class="rounded-lg border-gray-300" placeholder="Season name" required>
            <input name="starts_at" type="date" class="rounded-lg border-gray-300" required>
            <input name="ends_at" type="date" class="rounded-lg border-gray-300" required>
            <input name="price" type="number" step="0.01" class="rounded-lg border-gray-300" placeholder="Price" required>
            <button class="rounded-lg bg-cyan-700 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600 md:col-span-5">Save Rate</button>
        </form>
    </details>
    <div class="mt-6 grid gap-4">
        @foreach ($rates as $rate)
            <details class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <summary class="cursor-pointer list-none">
                    <span class="font-semibold">{{ $rate->name }}</span>
                    <span class="text-sm text-slate-500">&middot; {{ $rate->facility->name }} &middot; {{ $rate->starts_at->format('Y-m-d') }} to {{ $rate->ends_at->format('Y-m-d') }} &middot; &#8369;{{ number_format($rate->price, 2) }}</span>
                </summary>
                <form method="POST" action="{{ route('admin.seasonal-rates.update', $rate) }}" class="mt-4 grid gap-3 border-t border-slate-100 pt-4 md:grid-cols-5">
                    @csrf @method('PATCH')
                    <select name="facility_id" class="rounded-lg border-gray-300" required>@foreach ($facilities as $facility)<option value="{{ $facility->id }}" @selected($rate->facility_id === $facility->id)>{{ $facility->name }}</option>@endforeach</select>
                    <input name="name" value="{{ $rate->name }}" class="rounded-lg border-gray-300" required>
                    <input name="starts_at" type="date" value="{{ $rate->starts_at->format('Y-m-d') }}" class="rounded-lg border-gray-300" required>
                    <input name="ends_at" type="date" value="{{ $rate->ends_at->format('Y-m-d') }}" class="rounded-lg border-gray-300" required>
                    <input name="price" type="number" step="0.01" value="{{ $rate->price }}" class="rounded-lg border-gray-300" required>
                    <button class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white transition hover:bg-slate-800 md:col-span-5">Update Rate</button>
                </form>
                <form method="POST" action="{{ route('admin.seasonal-rates.destroy', $rate) }}" class="mt-3" onsubmit="return confirm('Delete seasonal rate?')">@csrf @method('DELETE') <button class="text-sm font-semibold text-red-700">Delete rate</button></form>
            </details>
        @endforeach
    </div>
    <div class="mt-6">{{ $rates->links() }}</div>
@endsection
