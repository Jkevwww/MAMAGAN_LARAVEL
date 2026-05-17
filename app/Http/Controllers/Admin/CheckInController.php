<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function index()
    {
        return view('admin.checkin.index');
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:500'],
        ]);

        $reference = $this->extractReference($data['reference']);

        $ticket = Ticket::with(['booking.user', 'booking.facility', 'booking.payment'])
            ->where('reference_number', $reference)
            ->first();

        if (! $ticket) {
            return back()->withErrors(['reference' => 'Ticket reference was not found.']);
        }

        return view('admin.checkin.result', compact('ticket'));
    }

    public function confirm(Ticket $ticket)
    {
        $ticket->load('booking');

        abort_unless($ticket->booking->payment_status === 'paid', 422, 'Only paid bookings can check in.');

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
        $decoded = json_decode($input, true);

        if (is_array($decoded) && isset($decoded['reference_number'])) {
            return $decoded['reference_number'];
        }

        if (is_array($decoded) && isset($decoded['booking_id'])) {
            return Ticket::where('booking_id', $decoded['booking_id'])->value('reference_number') ?? $input;
        }

        return trim($input);
    }
}
