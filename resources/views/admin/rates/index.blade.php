@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Seasonal Rates</h1>
    <form method="POST" action="{{ route('admin.seasonal-rates.store') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-5 shadow-sm md:grid-cols-5">
        @csrf
        <select name="facility_id" class="rounded-md border-gray-300" required>@foreach ($facilities as $facility)<option value="{{ $facility->id }}">{{ $facility->name }}</option>@endforeach</select>
        <input name="name" class="rounded-md border-gray-300" placeholder="Season name" required>
        <input name="starts_at" type="date" class="rounded-md border-gray-300" required>
        <input name="ends_at" type="date" class="rounded-md border-gray-300" required>
        <input name="price" type="number" step="0.01" class="rounded-md border-gray-300" placeholder="Price" required>
        <button class="rounded-md bg-cyan-700 px-4 py-2 text-white md:col-span-5">Save Rate</button>
    </form>
    <div class="mt-6 rounded-lg bg-white shadow-sm">
        @foreach ($rates as $rate)
            <div class="flex items-center justify-between border-b p-4 text-sm">
                <span><strong>{{ $rate->name }}</strong> · {{ $rate->facility->name }} · {{ $rate->starts_at->format('Y-m-d') }} to {{ $rate->ends_at->format('Y-m-d') }} · ₱{{ number_format($rate->price, 2) }}</span>
                <form method="POST" action="{{ route('admin.seasonal-rates.destroy', $rate) }}">@csrf @method('DELETE') <button class="text-red-700">Delete</button></form>
            </div>
        @endforeach
    </div>
@endsection
