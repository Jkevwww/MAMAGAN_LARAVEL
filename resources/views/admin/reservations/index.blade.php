<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Reservations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex space-x-4">
                <a href="{{ route('admin.reservations') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium {{ !request('status') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">All</a>
                <a href="{{ route('admin.reservations', ['status' => 'pending']) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium {{ request('status') === 'pending' ? 'bg-yellow-50 border-yellow-500 text-yellow-700' : 'text-gray-700 hover:bg-gray-50' }}">Pending</a>
                <a href="{{ route('admin.reservations', ['status' => 'approved']) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium {{ request('status') === 'approved' ? 'bg-green-50 border-green-500 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">Approved</a>
                <a href="{{ route('admin.reservations', ['status' => 'rejected']) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium {{ request('status') === 'rejected' ? 'bg-red-50 border-red-500 text-red-700' : 'text-gray-700 hover:bg-gray-50' }}">Rejected</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cottage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reservations as $reservation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $reservation->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $reservation->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reservation->cottage->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($reservation->check_in)->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">₱{{ number_format($reservation->total_price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $reservation->status === 'approved' ? 'bg-green-100 text-green-800' : ($reservation->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($reservation->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-indigo-600 hover:text-indigo-900">Review</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
