<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cottage;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display the booking form.
     */
    public function create(Cottage $cottage)
    {
        return view('reservations.create', compact('cottage'));
    }

    /**
     * Handle the booking request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cottage_id' => 'required|exists:cottages,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $cottage = Cottage::findOrFail($request->cottage_id);
        
        // Check for conflicting reservations
        $conflicts = Reservation::where('cottage_id', $cottage->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                      ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('check_in', '<=', $request->check_in)
                            ->where('check_out', '>=', $request->check_out);
                      });
            })->exists();

        if ($conflicts) {
            return back()->withErrors(['check_in' => 'The cottage is not available for the selected dates.']);
        }

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $days = $checkIn->diffInDays($checkOut);
        if ($days == 0) $days = 1; // Minimum 1 day

        $totalPrice = $days * $cottage->price;

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'cottage_id' => $cottage->id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'Paymongo',
        ]);

        return $this->createPaymongoLink($reservation);
    }

    /**
     * Create a Paymongo payment link.
     */
    protected function createPaymongoLink(Reservation $reservation)
    {
        $payload = [
            'data' => [
                'attributes' => [
                    'amount' => $reservation->total_price * 100, // Paymongo expects amount in cents
                    'description' => 'Mamagan Beach Resort Reservation - ' . $reservation->cottage->name,
                    'remarks' => 'Reservation ID: ' . $reservation->id,
                ]
            ]
        ];

        $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
            ->post('https://api.paymongo.com/v1/links', $payload);

        if ($response->successful()) {
            $data = $response->json();
            $reservation->update([
                'paymongo_link_id' => $data['data']['id'],
            ]);

            return redirect($data['data']['attributes']['checkout_url']);
        }

        return redirect()->route('reservations.index')->with('error', 'Unable to generate payment link. Please contact admin.');
    }

    /**
     * Handle payment success callback/webhook (simplified for now).
     */
    public function paymentSuccess(Request $request, Reservation $reservation)
    {
        // In a real scenario, use a Webhook to verify payment.
        // For demonstration, we'll check the link status.
        $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
            ->get('https://api.paymongo.com/v1/links/' . $reservation->paymongo_link_id);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['data']['attributes']['status'] === 'paid') {
                $reservation->update([
                    'payment_status' => 'paid',
                    'status' => 'approved',
                ]);
                return redirect()->route('reservations.show', $reservation)->with('success', 'Payment successful! Your reservation is approved.');
            }
        }

        return redirect()->route('reservations.show', $reservation)->with('error', 'Payment not verified yet.');
    }

    /**
     * Display user's reservations.
     */
    public function index()
    {
        $reservations = Auth::user()->reservations()->with('cottage')->latest()->get();
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show reservation status and receipt.
     */
    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }
        return view('reservations.show', compact('reservation'));
    }
}
