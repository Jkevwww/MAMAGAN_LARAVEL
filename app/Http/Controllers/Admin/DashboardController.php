<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $summary = [
            'total_bookings' => Booking::count(),
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
            'pending_bookings' => Booking::where('booking_status', 'pending')->count(),
            'paid_bookings' => Booking::where('payment_status', 'paid')->count(),
            'active_clients' => User::where('role', 'guest')->where('status', 'active')->count(),
        ];

        $monthlyRevenue = Payment::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as label, SUM(amount) as total")
            ->where('status', 'paid')
            ->groupBy('label')
            ->orderBy('label')
            ->pluck('total', 'label');

        $bookingStatuses = Booking::select('booking_status', DB::raw('count(*) as total'))->groupBy('booking_status')->pluck('total', 'booking_status');
        $paymentStatuses = Booking::select('payment_status', DB::raw('count(*) as total'))->groupBy('payment_status')->pluck('total', 'payment_status');
        $facilityUsage = Facility::withCount('bookings')->orderByDesc('bookings_count')->take(8)->pluck('bookings_count', 'name');
        $recentBookings = Booking::with(['user', 'facility', 'payment'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('summary', 'monthlyRevenue', 'bookingStatuses', 'paymentStatuses', 'facilityUsage', 'recentBookings'));
    }
}
