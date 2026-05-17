@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">{{ $facility->exists ? 'Edit Facility' : 'New Facility' }}</h1>
    <form method="POST" action="{{ $facility->exists ? route('admin.facilities.update', $facility) : route('admin.facilities.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4 rounded-lg bg-white p-6 shadow-sm">
        @csrf
        @if ($facility->exists) @method('PUT') @endif
        <div class="grid gap-4 md:grid-cols-2">
            <input name="name" value="{{ old('name', $facility->name) }}" class="rounded-md border-gray-300" placeholder="Name" required>
            <select name="category" class="rounded-md border-gray-300" required>
                @foreach (['Cottage', 'Cabana / Room', 'Beach Equipment'] as $category)
                    <option value="{{ $category }}" @selected(old('category', $facility->category) === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <input name="price_min" type="number" step="0.01" value="{{ old('price_min', $facility->price_min) }}" class="rounded-md border-gray-300" placeholder="Minimum price" required>
            <input name="price_max" type="number" step="0.01" value="{{ old('price_max', $facility->price_max) }}" class="rounded-md border-gray-300" placeholder="Maximum price" required>
            <input name="capacity" type="number" value="{{ old('capacity', $facility->capacity ?: 1) }}" class="rounded-md border-gray-300" placeholder="Capacity" required>
            <input name="inventory_count" type="number" value="{{ old('inventory_count', $facility->inventory_count ?: 1) }}" class="rounded-md border-gray-300" placeholder="Inventory count" required>
            <input name="rental_type" value="{{ old('rental_type', $facility->rental_type ?: 'daily') }}" class="rounded-md border-gray-300" placeholder="Rental type" required>
            <input type="file" name="image" class="rounded-md border border-gray-300 p-2">
        </div>
        <textarea name="description" rows="4" class="rounded-md border-gray-300" placeholder="Description">{{ old('description', $facility->description) }}</textarea>
        <div class="flex gap-6">
            <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $facility->is_active ?? true))> Active</label>
            <label><input type="checkbox" name="is_bookable" value="1" @checked(old('is_bookable', $facility->is_bookable ?? true))> Bookable</label>
        </div>
        <button class="w-fit rounded-md bg-cyan-700 px-5 py-2 font-semibold text-white">Save Facility</button>
    </form>
@endsection
