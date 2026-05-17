@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Blackout Dates</h1>
    <form method="POST" action="{{ route('admin.blackout-dates.store') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-5 shadow-sm md:grid-cols-4">
        @csrf
        <input name="title" class="rounded-md border-gray-300" placeholder="Title" required>
        <select name="facility_id" class="rounded-md border-gray-300"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}">{{ $facility->name }}</option>@endforeach</select>
        <input name="starts_at" type="date" class="rounded-md border-gray-300" required>
        <input name="ends_at" type="date" class="rounded-md border-gray-300" required>
        <textarea name="reason" class="rounded-md border-gray-300 md:col-span-3" placeholder="Reason"></textarea>
        <button class="rounded-md bg-cyan-700 px-4 py-2 text-white">Save Blackout</button>
    </form>
    <div class="mt-6 rounded-lg bg-white shadow-sm">
        @foreach ($blackouts as $blackout)
            <div class="flex items-center justify-between border-b p-4 text-sm">
                <span><strong>{{ $blackout->title }}</strong> · {{ $blackout->facility?->name ?? 'All facilities' }} · {{ $blackout->starts_at->format('Y-m-d') }} to {{ $blackout->ends_at->format('Y-m-d') }}</span>
                <form method="POST" action="{{ route('admin.blackout-dates.destroy', $blackout) }}">@csrf @method('DELETE') <button class="text-red-700">Delete</button></form>
            </div>
        @endforeach
    </div>
@endsection
