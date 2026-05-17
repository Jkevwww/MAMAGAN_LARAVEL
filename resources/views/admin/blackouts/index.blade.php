@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Blackout Dates</h1>
    <details class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200" open>
        <summary class="cursor-pointer font-semibold">Create Blackout Window</summary>
        <form method="POST" action="{{ route('admin.blackout-dates.store') }}" class="mt-4 grid gap-3 md:grid-cols-4">
            @csrf
            <input name="title" class="rounded-lg border-gray-300" placeholder="Title" required>
            <select name="facility_id" class="rounded-lg border-gray-300"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}">{{ $facility->name }}</option>@endforeach</select>
            <input name="starts_at" type="date" class="rounded-lg border-gray-300" required>
            <input name="ends_at" type="date" class="rounded-lg border-gray-300" required>
            <textarea name="reason" class="rounded-lg border-gray-300 md:col-span-3" placeholder="Reason"></textarea>
            <button class="rounded-lg bg-cyan-700 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600">Save Blackout</button>
        </form>
    </details>
    <div class="mt-6 grid gap-4">
        @foreach ($blackouts as $blackout)
            <details class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <summary class="cursor-pointer list-none">
                    <span class="font-semibold">{{ $blackout->title }}</span>
                    <span class="text-sm text-slate-500">&middot; {{ $blackout->facility?->name ?? 'All facilities' }} &middot; {{ $blackout->starts_at->format('Y-m-d') }} to {{ $blackout->ends_at->format('Y-m-d') }}</span>
                </summary>
                <form method="POST" action="{{ route('admin.blackout-dates.update', $blackout) }}" class="mt-4 grid gap-3 border-t border-slate-100 pt-4 md:grid-cols-4">
                    @csrf @method('PATCH')
                    <input name="title" value="{{ $blackout->title }}" class="rounded-lg border-gray-300" required>
                    <select name="facility_id" class="rounded-lg border-gray-300"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}" @selected($blackout->facility_id === $facility->id)>{{ $facility->name }}</option>@endforeach</select>
                    <input name="starts_at" type="date" value="{{ $blackout->starts_at->format('Y-m-d') }}" class="rounded-lg border-gray-300" required>
                    <input name="ends_at" type="date" value="{{ $blackout->ends_at->format('Y-m-d') }}" class="rounded-lg border-gray-300" required>
                    <textarea name="reason" class="rounded-lg border-gray-300 md:col-span-3">{{ $blackout->reason }}</textarea>
                    <button class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">Update</button>
                </form>
                <form method="POST" action="{{ route('admin.blackout-dates.destroy', $blackout) }}" class="mt-3" onsubmit="return confirm('Delete blackout window?')">@csrf @method('DELETE') <button class="text-sm font-semibold text-red-700">Delete blackout</button></form>
            </details>
        @endforeach
    </div>
    <div class="mt-6">{{ $blackouts->links() }}</div>
@endsection
