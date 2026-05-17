<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation Details #' . $reservation->id) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="text-3xl font-extrabold text-gray-900">Mamagan Beach Resort</h3>
                            <p class="text-gray-500 mt-1">Reservation Receipt</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-600">Reservation Date:</p>
                            <p class="text-lg font-bold text-gray-900">{{ $reservation->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                        <div>
                            <h4 class="text-lg font-bold text-indigo-700 border-b border-indigo-100 pb-2 mb-4">Customer Details</h4>
                            <p class="text-gray-800 font-semibold text-lg">{{ $reservation->user->name }}</p>
                            <p class="text-gray-600 mt-1">{{ $reservation->user->email }}</p>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-indigo-700 border-b border-indigo-100 pb-2 mb-4">Reservation Info</h4>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-bold uppercase {{ $reservation->status === 'approved' ? 'text-green-600' : ($reservation->status === 'rejected' ? 'text-red-600' : 'text-yellow-600') }}">
                                    {{ $reservation->status }}
                                </span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Payment Status:</span>
                                <span class="font-bold uppercase {{ $reservation->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $reservation->payment_status }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Method:</span>
                                <span class="font-bold text-gray-900">{{ $reservation->payment_method }}</span>
                            </div>
                        </div>
                    </div>

                    @if($reservation->payment_status === 'unpaid' && $reservation->status === 'pending')
                        <div class="mb-12 p-6 bg-yellow-50 border border-yellow-100 rounded-lg flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-bold text-yellow-800 uppercase tracking-widest mb-1">Payment Required</h4>
                                <p class="text-yellow-700 leading-relaxed">Your reservation is pending payment. Please complete the payment to secure your cottage.</p>
                            </div>
                            <a href="{{ 'https://checkout.paymongo.com/l/' . $reservation->paymongo_link_id }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-yellow-600 border border-transparent rounded-md font-bold text-sm text-white uppercase tracking-widest hover:bg-yellow-700 transition duration-150">
                                Pay Now with Paymongo
                            </a>
                        </div>
                    @endif

                    <div class="mb-12">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cottage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reservation->cottage->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($reservation->check_in)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($reservation->check_out)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">₱{{ number_format($reservation->total_price, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($reservation->admin_notes)
                        <div class="mb-12 p-6 bg-red-50 border border-red-100 rounded-lg">
                            <h4 class="text-sm font-bold text-red-800 uppercase tracking-widest mb-2">Admin Notes</h4>
                            <p class="text-red-700 leading-relaxed">{{ $reservation->admin_notes }}</p>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-4 no-print">
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Download / Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: white;
            }
            .py-12 {
                padding: 0;
            }
            .shadow-sm {
                box-shadow: none;
            }
            .border-b {
                border-bottom: 1px solid #e5e7eb;
            }
        }
    </style>
</x-app-layout>
