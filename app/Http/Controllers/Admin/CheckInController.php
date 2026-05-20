<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CheckInController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $summary = [
            'checked_in_today' => Ticket::whereDate('checked_in_at', $today)->count(),
            'expected_today' => Booking::whereDate('booking_date', $today)
                ->where('payment_status', 'paid')
                ->count(),
            'waiting_today' => Booking::whereDate('booking_date', $today)
                ->where('payment_status', 'paid')
                ->whereHas('ticket', fn ($ticket) => $ticket->whereNull('checked_in_at'))
                ->count(),
            'unpaid_today' => Booking::whereDate('booking_date', $today)
                ->where('payment_status', '!=', 'paid')
                ->count(),
        ];

        $recentCheckIns = Ticket::with(['booking.user', 'booking.facility', 'checker'])
            ->whereNotNull('checked_in_at')
            ->latest('checked_in_at')
            ->take(5)
            ->get();

        $todayArrivals = Booking::with(['user', 'facility', 'ticket'])
            ->whereDate('booking_date', $today)
            ->where('payment_status', 'paid')
            ->orderBy('start_time')
            ->take(6)
            ->get();

        return view('admin.checkin.index', compact('summary', 'recentCheckIns', 'todayArrivals'));
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:500'],
        ]);

        $reference = $this->extractReference($data['reference']);

        $ticket = $this->findTicket($reference);

        if (! $ticket) {
            return back()->withInput()->withErrors(['reference' => 'No booking ticket matched that QR payload, ticket reference, payment reference, or booking ID.']);
        }

        return view('admin.checkin.result', compact('ticket'));
    }

    public function confirm(Ticket $ticket)
    {
        $ticket->load(['booking.user', 'booking.facility']);

        abort_unless($ticket->booking->payment_status === 'paid', 422, 'Only paid bookings can check in.');

        if ($ticket->checked_in_at) {
            return redirect()->route('admin.checkin.index')->with('status', 'This ticket was already checked in.');
        }

        $ticket->update([
            'checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
        ]);

        $ticket->booking->update(['booking_status' => 'checked_in']);

        \App\Models\SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'booking.checked_in',
            'target_type' => Ticket::class,
            'target_id' => $ticket->id,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.checkin.index')->with('success', 'Guest checked in.');
    }

    private function extractReference(string $input): string
    {
        $input = trim($input);
        $decoded = json_decode($input, true);

        if (is_array($decoded) && isset($decoded['reference_number'])) {
            return trim($decoded['reference_number']);
        }

        if (is_array($decoded) && isset($decoded['booking_id'])) {
            if ($this->hasValidPayloadSignature($decoded)) {
                return Ticket::where('booking_id', $decoded['booking_id'])->value('reference_number') ?? $input;
            }

            return trim((string) $decoded['booking_id']);
        }

        return $input;
    }

    private function findTicket(string $reference): ?Ticket
    {
        $query = Ticket::with(['booking.user', 'booking.facility', 'booking.payment', 'checker']);

        $ticket = (clone $query)->where('reference_number', $reference)->first();

        if ($ticket) {
            return $ticket;
        }

        if (ctype_digit($reference)) {
            $booking = Booking::find((int) $reference);

            if ($booking?->ticket) {
                return $query->find($booking->ticket->id);
            }
        }

        $payment = Payment::where('reference_number', $reference)
            ->orWhere('paymongo_checkout_id', $reference)
            ->orWhere('paymongo_payment_id', $reference)
            ->first();

        if ($payment?->booking?->ticket) {
            return $query->find($payment->booking->ticket->id);
        }

        return null;
    }

    private function hasValidPayloadSignature(array $payload): bool
    {
        if (! isset($payload['signature'], $payload['booking_id'])) {
            return true;
        }

        $ticket = Ticket::where('booking_id', $payload['booking_id'])->first();

        if (! $ticket) {
            return false;
        }

        $expected = hash_hmac('sha256', $ticket->booking_id.'|'.$ticket->booking->user_id, config('app.key'));

        return hash_equals($expected, (string) $payload['signature']);
    }
}
