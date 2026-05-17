<?php

namespace App\Http\Controllers;

use App\Models\BlackoutDate;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Promotion;
use App\Models\SeasonalRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['facility', 'payment', 'ticket'])
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create(Facility $facility)
    {
        abort_unless($facility->is_active && $facility->is_bookable, 404);

        return view('bookings.create', compact('facility'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'quantity' => ['required', 'integer', 'min:1'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'booking_type' => ['required', 'string', 'max:50'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $facility = Facility::findOrFail($data['facility_id']);
        abort_unless($facility->is_active && $facility->is_bookable, 422);

        if ($this->isBlackedOut($facility, $data['booking_date'])) {
            return back()->withInput()->withErrors(['booking_date' => 'This facility is unavailable on the selected date.']);
        }

        if (! $this->hasInventory($facility, $data['booking_date'], $data['start_time'] ?? null, $data['end_time'] ?? null, (int) $data['quantity'])) {
            return back()->withInput()->withErrors(['quantity' => 'The requested quantity is no longer available for that schedule.']);
        }

        $unitPrice = $this->priceFor($facility, $data['booking_date']);
        $baseAmount = $unitPrice * (int) $data['quantity'];
        $promotion = null;
        $discount = 0;

        if (! empty($data['promo_code'])) {
            $promotion = Promotion::where('code', strtoupper($data['promo_code']))->first();

            if (! $promotion || ! $promotion->isUsableFor($facility, $baseAmount)) {
                return back()->withInput()->withErrors(['promo_code' => 'Promo code is invalid or no longer available.']);
            }

            $discount = $promotion->discountFor($baseAmount);
        }

        $booking = DB::transaction(function () use ($data, $facility, $promotion, $baseAmount, $discount) {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'facility_id' => $facility->id,
                'promotion_id' => $promotion?->id,
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'quantity' => $data['quantity'],
                'guest_count' => $data['guest_count'],
                'booking_type' => $data['booking_type'],
                'base_amount' => $baseAmount,
                'discount_amount' => $discount,
                'total_amount' => max(0, $baseAmount - $discount),
                'booking_status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $booking->payment()->create([
                'method' => 'gcash',
                'amount' => $booking->total_amount,
                'status' => 'pending',
            ]);

            if ($promotion) {
                $promotion->increment('used_count');
            }

            $this->log('booking.created', $booking);

            return $booking;
        });

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking created. Record your payment reference when ready.');
    }

    public function show(Booking $booking)
    {
        $this->authorizeOwner($booking);
        $booking->load(['facility', 'payment', 'ticket', 'promotion']);

        return view('bookings.show', compact('booking'));
    }

    public function recordPayment(Request $request, Booking $booking)
    {
        $this->authorizeOwner($booking);
        abort_if(in_array($booking->booking_status, ['cancelled', 'checked_in'], true), 422);
        abort_if($booking->payment_status === 'paid', 422);

        $data = $request->validate([
            'method' => ['required', 'string', 'max:30'],
            'reference_number' => ['required', 'string', 'max:100'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $proofPath = $request->file('proof')?->store('payment-proofs', 'public');

        $booking->payment()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'method' => $data['method'],
                'amount' => $booking->total_amount,
                'status' => 'pending',
                'reference_number' => $data['reference_number'],
                'proof_path' => $proofPath ?? $booking->payment?->proof_path,
            ]
        );

        $this->log('payment.reference_recorded', $booking);

        return back()->with('success', 'Payment reference submitted for verification.');
    }

    public function cancel(Booking $booking)
    {
        $this->authorizeOwner($booking);
        abort_if(in_array($booking->booking_status, ['checked_in', 'cancelled'], true), 422);
        abort_if($booking->payment_status === 'paid', 422);

        $booking->update([
            'booking_status' => 'cancelled',
            'payment_status' => $booking->payment_status === 'pending' ? 'failed' : $booking->payment_status,
        ]);
        $this->log('booking.cancelled', $booking);

        return back()->with('success', 'Booking cancelled.');
    }

    private function hasInventory(Facility $facility, string $date, ?string $startTime, ?string $endTime, int $quantity): bool
    {
        $reserved = Booking::where('facility_id', $facility->id)
            ->where('booking_date', $date)
            ->whereIn('booking_status', ['pending', 'approved', 'checked_in'])
            ->when($startTime && $endTime, function ($query) use ($startTime, $endTime) {
                $query->where(function ($slot) use ($startTime, $endTime) {
                    $slot->whereNull('start_time')
                        ->orWhereNull('end_time')
                        ->orWhere(function ($time) use ($startTime, $endTime) {
                            $time->where('start_time', '<', $endTime)
                                ->where('end_time', '>', $startTime);
                        });
                });
            })
            ->sum('quantity');

        return ($reserved + $quantity) <= $facility->inventory_count;
    }

    private function isBlackedOut(Facility $facility, string $date): bool
    {
        return BlackoutDate::where(function ($query) use ($facility) {
            $query->whereNull('facility_id')->orWhere('facility_id', $facility->id);
        })->where('starts_at', '<=', $date)->where('ends_at', '>=', $date)->exists();
    }

    private function priceFor(Facility $facility, string $date): float
    {
        $seasonalRate = SeasonalRate::where('facility_id', $facility->id)
            ->where('starts_at', '<=', $date)
            ->where('ends_at', '>=', $date)
            ->orderByDesc('created_at')
            ->first();

        return (float) ($seasonalRate?->price ?? $facility->price_min);
    }

    private function authorizeOwner(Booking $booking): void
    {
        abort_unless($booking->user_id === Auth::id(), 403);
    }

    private function log(string $action, Booking $booking): void
    {
        \App\Models\SystemLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_type' => Booking::class,
            'target_id' => $booking->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
