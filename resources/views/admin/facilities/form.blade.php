@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">{{ $facility->exists ? 'Edit Facility' : 'New Facility' }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $facility->exists ? 'Update inventory, rates, status, and media.' : 'Add a cottage, cabana/room, or beach equipment.' }}</p>
        </div>
        <a href="{{ route('admin.facilities.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to facilities</a>
    </div>

    <form method="POST" action="{{ $facility->exists ? route('admin.facilities.update', $facility) : route('admin.facilities.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
        @csrf
        @if ($facility->exists) @method('PUT') @endif

        <section class="grid gap-5 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Name</label>
                    <input name="name" value="{{ old('name', $facility->name) }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Facility name" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Category</label>
                    <select name="category" class="mt-1 w-full rounded-lg border-gray-300" required>
                        @foreach (['Cottage', 'Cabana / Room', 'Beach Equipment'] as $category)
                            <option value="{{ $category }}" @selected(old('category', $facility->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Minimum Price</label>
                    <input name="price_min" type="number" step="0.01" min="0" value="{{ old('price_min', $facility->price_min) }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="0.00" required>
                    <x-input-error :messages="$errors->get('price_min')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Maximum Price</label>
                    <input name="price_max" type="number" step="0.01" min="0" value="{{ old('price_max', $facility->price_max) }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="0.00" required>
                    <x-input-error :messages="$errors->get('price_max')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Capacity</label>
                    <input name="capacity" type="number" min="1" value="{{ old('capacity', $facility->capacity ?: 1) }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                    <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Inventory Count</label>
                    <input name="inventory_count" type="number" min="1" value="{{ old('inventory_count', $facility->inventory_count ?: 1) }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                    <x-input-error :messages="$errors->get('inventory_count')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-semibold text-slate-700">Rental Type</label>
                    <select name="rental_type" class="mt-1 w-full rounded-lg border-gray-300" required>
                        @foreach (['hourly', 'daily', 'overnight'] as $type)
                            <option value="{{ $type }}" @selected(old('rental_type', $facility->rental_type ?: 'daily') === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('rental_type')" class="mt-2" />
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Description</label>
                <textarea name="description" rows="5" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Describe the facility, inclusions, location, and rules.">{{ old('description', $facility->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-cyan-700" @checked(old('is_active', $facility->exists ? $facility->is_active : true))>
                    Active
                </label>
                <label class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">
                    <input type="checkbox" name="is_bookable" value="1" class="rounded border-gray-300 text-cyan-700" @checked(old('is_bookable', $facility->exists ? $facility->is_bookable : true))>
                    Bookable
                </label>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button class="rounded-lg bg-cyan-700 px-5 py-2.5 font-semibold text-white transition hover:bg-cyan-600">Save Facility</button>
                <a href="{{ route('admin.facilities.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-center font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</a>
            </div>
        </section>

        <aside class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="font-semibold">Facility Image</h2>
            <div class="mt-4 aspect-[16/10] overflow-hidden rounded-lg bg-slate-100">
                @if ($facility->image)
                    <img src="{{ asset('storage/'.$facility->image) }}" alt="{{ $facility->name }}" class="h-full w-full object-cover">
                @else
                    <div class="grid h-full place-items-center text-sm text-slate-500">No image uploaded</div>
                @endif
            </div>
            <input type="file" name="image" accept="image/*" class="mt-4 w-full rounded-lg border border-gray-300 p-2">
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
            @if ($facility->image)
                <label class="mt-3 inline-flex items-center gap-2 text-sm text-red-700">
                    <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-700">
                    Remove current image
                </label>
            @endif
            <div class="mt-5 rounded-lg bg-cyan-50 p-4 text-sm leading-6 text-cyan-900">
                Use clear landscape photos. Uploaded images are stored in Laravel public storage.
            </div>
        </aside>
    </form>
@endsection
