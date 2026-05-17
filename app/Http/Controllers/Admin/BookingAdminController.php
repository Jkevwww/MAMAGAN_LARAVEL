<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingAdminController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['user', 'facility', 'payment', 'ticket'])
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

    public function verifyPayment(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
        ]);

        DB::transaction(function () use ($booking, $data) {
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
                $this->issueTicket($booking->fresh(['user', 'facility']));
            }

            $this->log('payment.verified', $booking, $data);
        });

        return back()->with('success', 'Payment status updated.');
    }

    private function issueTicket(Booking $booking): Ticket
    {
        return Ticket::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'reference_number' => 'MAM-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'qr_payload' => json_encode([
                    'booking_id' => $booking->id,
                    'tourist' => $booking->user->email,
                    'facility' => $booking->facility->name,
                    'signature' => hash_hmac('sha256', $booking->id.'|'.$booking->user_id, config('app.key')),
                ]),
                'issued_at' => now(),
            ]
        );
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
