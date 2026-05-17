<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book Reservation: ' . $cottage->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            @if($cottage->image)
                                <img src="{{ asset('storage/' . $cottage->image) }}" alt="{{ $cottage->name }}" class="w-full rounded-lg shadow-md mb-6">
                            @endif
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $cottage->name }}</h3>
                            <p class="text-gray-700 leading-relaxed mb-6">{{ $cottage->description }}</p>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-lg font-semibold text-indigo-700">Price: ₱{{ number_format($cottage->price, 2) }} per day</p>
                                <p class="text-gray-600 mt-2">Maximum Capacity: {{ $cottage->capacity }} persons</p>
                            </div>
                        </div>
                        <div>
                            <form action="{{ route('reservations.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cottage_id" value="{{ $cottage->id }}">

                                <div class="mb-4">
                                    <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                                    <input type="date" name="check_in" id="check_in" value="{{ old('check_in') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('check_in') border-red-500 @enderror">
                                    @error('check_in')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-6">
                                    <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                                    <input type="date" name="check_out" id="check_out" value="{{ old('check_out') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('check_out') border-red-500 @enderror">
                                    @error('check_out')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-8 bg-indigo-50 p-4 rounded-lg mb-6">
                                    <p class="text-sm text-indigo-800 font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                        Secure payment powered by Paymongo. Supports GCash, Maya, and Credit Cards.
                                    </p>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                        Proceed to Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
