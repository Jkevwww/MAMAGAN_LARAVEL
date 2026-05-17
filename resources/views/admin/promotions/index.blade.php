@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Promotions</h1>
    <form method="POST" action="{{ route('admin.promotions.store') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-5 shadow-sm md:grid-cols-3">
        @csrf
        <input name="name" class="rounded-md border-gray-300" placeholder="Name" required>
        <input name="code" class="rounded-md border-gray-300 uppercase" placeholder="Code" required>
        <select name="discount_type" class="rounded-md border-gray-300"><option value="percent">Percent</option><option value="fixed">Fixed</option></select>
        <input name="discount_value" type="number" step="0.01" class="rounded-md border-gray-300" placeholder="Discount value" required>
        <input name="minimum_amount" type="number" step="0.01" class="rounded-md border-gray-300" placeholder="Minimum amount">
        <input name="usage_limit" type="number" class="rounded-md border-gray-300" placeholder="Usage limit">
        <input name="starts_at" type="date" class="rounded-md border-gray-300">
        <input name="ends_at" type="date" class="rounded-md border-gray-300">
        <select name="facility_id" class="rounded-md border-gray-300"><option value="">All facilities</option>@foreach ($facilities as $facility)<option value="{{ $facility->id }}">{{ $facility->name }}</option>@endforeach</select>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        <button class="rounded-md bg-cyan-700 px-4 py-2 text-white md:col-span-2">Save Promotion</button>
    </form>
    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3">Code</th><th class="p-3">Discount</th><th class="p-3">Valid</th><th class="p-3">Usage</th><th class="p-3"></th></tr></thead>
            <tbody>
                @foreach ($promotions as $promotion)
                    <tr class="border-t">
                        <td class="p-3 font-semibold">{{ $promotion->code }}</td>
                        <td class="p-3">{{ $promotion->discount_type }} {{ $promotion->discount_value }}</td>
                        <td class="p-3">{{ $promotion->starts_at?->format('Y-m-d') }} - {{ $promotion->ends_at?->format('Y-m-d') }}</td>
                        <td class="p-3">{{ $promotion->used_count }} / {{ $promotion->usage_limit ?: '∞' }}</td>
                        <td class="p-3 text-right"><form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" onsubmit="return confirm('Delete promotion?')">@csrf @method('DELETE') <button class="text-red-700">Delete</button></form></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
