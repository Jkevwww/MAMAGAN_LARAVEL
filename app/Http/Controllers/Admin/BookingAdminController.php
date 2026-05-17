<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\TicketIssuer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingAdminController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['user', 'facility', 'payment', 'ticket'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('facility', fn ($facility) => $facility->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('ticket', fn ($ticket) => $ticket->where('reference_number', 'like', "%{$search}%"));
                });
            })
            ->when($request->booking_status, fn ($query, $status) => $query->where('booking_status', $status))
            ->when($request->payment_status, fn ($query, $status) => $query->where('payment_status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'facility', 'payment', 'ticket', 'promotion']);

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
}
