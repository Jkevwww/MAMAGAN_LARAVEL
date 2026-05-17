<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Available Cottages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($cottages as $cottage)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        @if($cottage->image)
                            <img src="{{ asset('storage/' . $cottage->image) }}" alt="{{ $cottage->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">No Image Available</span>
                            </div>
                        @endif
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900">{{ $cottage->name }}</h3>
                            <p class="text-gray-600 mt-2">{{ Str::limit($cottage->description, 100) }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-xl font-semibold text-indigo-600">₱{{ number_format($cottage->price, 2) }}</span>
                                <span class="text-sm text-gray-500">Capacity: {{ $cottage->capacity }}</span>
                            </div>
                            <div class="mt-6">
                                <a href="{{ route('reservations.create', $cottage) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
