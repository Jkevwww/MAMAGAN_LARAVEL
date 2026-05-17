@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Facilities</h1>
        <a href="{{ route('admin.facilities.create') }}" class="rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white">New Facility</a>
    </div>
    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3">Name</th><th class="p-3">Category</th><th class="p-3">Rate</th><th class="p-3">Inventory</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
            <tbody>
                @foreach ($facilities as $facility)
                    <tr class="border-t">
                        <td class="p-3 font-semibold">{{ $facility->name }}</td>
                        <td class="p-3">{{ $facility->category }}</td>
                        <td class="p-3">₱{{ number_format($facility->price_min, 2) }} - ₱{{ number_format($facility->price_max, 2) }}</td>
                        <td class="p-3">{{ $facility->inventory_count }}</td>
                        <td class="p-3">{{ $facility->is_active ? 'Active' : 'Inactive' }} / {{ $facility->is_bookable ? 'Bookable' : 'Blocked' }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.facilities.edit', $facility) }}" class="font-semibold text-cyan-700">Edit</a>
                            <form method="POST" action="{{ route('admin.facilities.destroy', $facility) }}" class="inline" onsubmit="return confirm('Delete this facility?')">@csrf @method('DELETE') <button class="ml-3 text-red-700">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $facilities->links() }}</div>
@endsection
