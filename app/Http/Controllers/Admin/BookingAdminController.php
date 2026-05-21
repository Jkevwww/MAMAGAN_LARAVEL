<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Services\TicketIssuer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingAdminController extends Controller
{
    public function index(Request $request)
    {
        $filteredQuery = $this->query($request);
        $analyticsBookings = (clone $filteredQuery)->get();
        $bookings = (clone $filteredQuery)
            ->paginate(15)
            ->withQueryString();
        $facilities = Facility::orderBy('name')->get();
        $categories = Facility::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $today = today();
        $paidBookings = $analyticsBookings->where('payment_status', 'paid');
        $summary = [
            'total' => $analyticsBookings->count(),
            'pending' => $analyticsBookings->where('booking_status', 'pending')->count(),
            'pending_payments' => $analyticsBookings->where('payment_status', 'pending')->count(),
            'today' => $analyticsBookings->filter(fn ($booking) => $booking->booking_date->isSameDay($today))->count(),
            'upcoming' => $analyticsBookings
                ->filter(fn ($booking) => $booking->booking_date->greaterThanOrEqualTo($today))
                ->whereIn('booking_status', ['pending', 'approved'])
                ->count(),
            'paid_revenue' => $paidBookings->sum('total_amount'),
        ];

        return view('admin.bookings.index', compact('bookings', 'facilities', 'categories', 'summary'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'facility', 'payment.verifier', 'ticket', 'promotion']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'booking_status' => ['required', 'in:pending,approved,cancelled,checked_in'],
        ]);

        $booking->update($data);
        $this->log('booking.status_updated', $booking, $data);

        return back()->with('success', 'Booking status updated.');
    }

    public function verifyPayment(Request $request, Booking $booking, TicketIssuer $ticketIssuer)
    {
        $data = $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
        ]);

        DB::transaction(function () use ($booking, $data, $ticketIssuer) {
            $booking->update([
                'payment_status' => $data['payment_status'],
                'booking_status' => $data['payment_status'] === 'paid' ? 'approved' : $booking->booking_status,
            ]);

            $booking->payment?->update([
                'status' => $data['payment_status'],
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            if ($data['payment_status'] === 'paid') {
                $ticketIssuer->issue($booking->fresh(['user', 'facility']));
            }

            $this->log('payment.verified', $booking, $data);
        });

        return back()->with('success', 'Payment status updated.');
    }

    private function log(string $action, Booking $booking, array $properties = []): void
    {
        \App\Models\SystemLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'target_type' => Booking::class,
            'target_id' => $booking->id,
            'properties' => $properties,
            'ip_address' => request()->ip(),
        ]);
    }

    private function query(Request $request)
    {
        return Booking::with(['user', 'facility', 'payment', 'ticket'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner
                        ->whereHas('user', fn ($user) => $user
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%"))
                        ->orWhereHas('facility', fn ($facility) => $facility
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%"))
                        ->orWhereHas('ticket', fn ($ticket) => $ticket
                            ->where('reference_number', 'like', "%{$search}%"))
                        ->orWhereHas('payment', fn ($payment) => $payment
                            ->where('reference_number', 'like', "%{$search}%")
                            ->orWhere('paymongo_checkout_id', 'like', "%{$search}%")
                            ->orWhere('paymongo_payment_id', 'like', "%{$search}%"));

                    if (is_numeric($search)) {
                        $inner->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($request->date_from, fn ($query, $date) => $query->whereDate('booking_date', '>=', $date))
            ->when($request->date_to, fn ($query, $date) => $query->whereDate('booking_date', '<=', $date))
            ->when($request->facility_id, fn ($query, $facilityId) => $query->where('facility_id', $facilityId))
            ->when($request->category, fn ($query, $category) => $query->whereHas('facility', fn ($facility) => $facility->where('category', $category)))
            ->when($request->booking_status, fn ($query, $status) => $query->where('booking_status', $status))
            ->when($request->payment_status, fn ($query, $status) => $query->where('payment_status', $status))
            ->when($request->sort === 'booking_date_asc', fn ($query) => $query->orderBy('booking_date')->orderBy('start_time'))
            ->when($request->sort === 'booking_date_desc', fn ($query) => $query->orderByDesc('booking_date')->orderByDesc('start_time'))
            ->when($request->sort === 'total_desc', fn ($query) => $query->orderByDesc('total_amount'))
            ->when($request->sort === 'total_asc', fn ($query) => $query->orderBy('total_amount'))
            ->when(! in_array($request->sort, ['booking_date_asc', 'booking_date_desc', 'total_desc', 'total_asc'], true), fn ($query) => $query->latest());
    }
}
