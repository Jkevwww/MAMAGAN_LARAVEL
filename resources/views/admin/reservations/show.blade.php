<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Reservation #' . $reservation->id) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Reservation Summary</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="text-gray-500">Customer:</span>
                                <span class="font-semibold text-gray-900">{{ $reservation->user->name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="text-gray-500">Email:</span>
                                <span class="text-gray-900">{{ $reservation->user->email }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="text-gray-500">Cottage:</span>
                                <span class="font-semibold text-gray-900">{{ $reservation->cottage->name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="text-gray-500">Check-in:</span>
                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($reservation->check_in)->format('F d, Y') }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="text-gray-500">Check-out:</span>
                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($reservation->check_out)->format('F d, Y') }}</span>
                            </div>
                            <div class="flex justify-between pt-2">
                                <span class="text-gray-700 font-bold text-lg">Total Price:</span>
                                <span class="text-indigo-700 font-bold text-xl">₱{{ number_format($reservation->total_price, 2) }}</span>
                            </div>
                        </div>

                        @if($reservation->payment_proof)
                            <div class="mt-8">
                                <h4 class="text-md font-bold text-gray-900 mb-4 uppercase tracking-widest">Payment Proof</h4>
                                <a href="{{ asset('storage/' . $reservation->payment_proof) }}" target="_blank" class="block group relative">
                                    <img src="{{ asset('storage/' . $reservation->payment_proof) }}" alt="Payment Proof" class="w-full rounded-lg border-2 border-gray-100 hover:border-indigo-300 transition duration-300">
                                    <div class="absolute inset-0 bg-indigo-600 bg-opacity-0 group-hover:bg-opacity-10 rounded-lg flex items-center justify-center transition duration-300">
                                        <span class="text-indigo-700 font-bold opacity-0 group-hover:opacity-100">Click to enlarge</span>
                                    </div>
                                </a>
                            </div>
                        @else
                            <div class="mt-8 p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
                                <p class="text-yellow-700 text-sm italic">No payment proof uploaded yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Action</h3>
                        <form action="{{ route('admin.reservations.update_status', $reservation) }}" method="POST">
                            @csrf
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-green-500 peer-checked:border-green-600">
                                        <input type="radio" name="status" value="approved" class="sr-only peer" {{ $reservation->status === 'approved' ? 'checked' : '' }}>
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900">Approve</span>
                                                <span class="mt-1 flex items-center text-xs text-gray-500">Confirm payment and booking</span>
                                            </span>
                                        </span>
                                        <svg class="h-5 w-5 text-green-600 opacity-0 peer-checked:opacity-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-red-500 peer-checked:border-red-600">
                                        <input type="radio" name="status" value="rejected" class="sr-only peer" {{ $reservation->status === 'rejected' ? 'checked' : '' }}>
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900">Reject</span>
                                                <span class="mt-1 flex items-center text-xs text-gray-500">Invalid payment or other reasons</span>
                                            </span>
                                        </span>
                                        <svg class="h-5 w-5 text-red-600 opacity-0 peer-checked:opacity-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </label>
                                </div>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (optional)</label>
                                <textarea name="admin_notes" id="admin_notes" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Reason for rejection or additional instructions...">{{ old('admin_notes', $reservation->admin_notes) }}</textarea>
                                @error('admin_notes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                Update Reservation Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
