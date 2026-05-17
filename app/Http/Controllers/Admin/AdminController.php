<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Cottage;

class AdminController extends Controller
{
    /**
     * Admin Dashboard.
     */
    public function dashboard()
    {
        $totalReservations = Reservation::count();
        $pendingReservationsCount = Reservation::where('status', 'pending')->count();
        $totalUsers = User::where('role', 'client')->count();
        $totalCottages = Cottage::count();
        
        $recentReservations = Reservation::with(['user', 'cottage'])->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalReservations', 
            'pendingReservationsCount', 
            'totalUsers', 
            'totalCottages',
            'recentReservations'
        ));
    }

    /**
     * View all reservations.
     */
    public function reservations(Request $request)
    {
        $status = $request->get('status');
        $query = Reservation::with(['user', 'cottage'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $reservations = $query->paginate(20);

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Show a single reservation for approval/rejection.
     */
    public function showReservation(Reservation $reservation)
    {
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Approve or reject a reservation.
     */
    public function updateReservationStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $reservation->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.reservations')->with('success', 'Reservation status updated successfully.');
    }
}
