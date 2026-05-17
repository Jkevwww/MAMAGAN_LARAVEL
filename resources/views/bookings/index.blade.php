<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">My Bookings</h2>
            <a href="{{ route('facilities.index') }}" class="rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white">Browse Facilities</a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="p-3">Facility</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Booking</th>
                        <th class="p-3">Payment</th>
                        <th class="p-3">Total</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr class="border-t">
                            <td class="p-3">{{ $booking->facility->name }}</td>
                            <td class="p-3">{{ $booking->booking_date->format('M d, Y') }}</td>
                            <td class="p-3">{{ ucfirst($booking->booking_status) }}</td>
                            <td class="p-3">{{ ucfirst($booking->payment_status) }}</td>
                            <td class="p-3">₱{{ number_format($booking->total_amount, 2) }}</td>
                            <td class="p-3 text-right"><a href="{{ route('bookings.show', $booking) }}" class="font-semibold text-cyan-700">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-gray-500">No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $bookings->links() }}</div>
    </div>
</x-app-layout>
