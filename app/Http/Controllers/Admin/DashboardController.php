<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = Carbon::today();

        $summary = [
            'total_bookings' => Booking::count(),
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
            'pending_bookings' => Booking::where('booking_status', 'pending')->count(),
            'approved_bookings' => Booking::where('booking_status', 'approved')->count(),
            'checked_in_bookings' => Booking::where('booking_status', 'checked_in')->count(),
            'cancelled_bookings' => Booking::where('booking_status', 'cancelled')->count(),
            'paid_bookings' => Booking::where('payment_status', 'paid')->count(),
            'pending_payments' => Booking::where('payment_status', 'pending')->count(),
            'active_clients' => User::where('role', 'guest')->where('status', 'active')->count(),
            'today_bookings' => Booking::whereDate('booking_date', $today)->count(),
            'facilities' => Facility::count(),
            'bookable_facilities' => Facility::where('is_active', true)->where('is_bookable', true)->count(),
            'low_inventory' => Facility::where('is_active', true)->where('inventory_count', '<=', 3)->count(),
        ];

        $dateExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyRevenue = Payment::selectRaw("{$dateExpression} as label, SUM(amount) as total")
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('label')
            ->orderBy('label')
            ->pluck('total', 'label');

        $monthLabels = collect(range(5, 0))->map(fn ($monthsAgo) => now()->subMonths($monthsAgo)->format('Y-m'));
        $monthlyRevenue = $monthLabels->mapWithKeys(fn ($label) => [$label => (float) ($monthlyRevenue[$label] ?? 0)]);

        $bookingStatuses = collect(['pending', 'approved', 'cancelled', 'checked_in'])
            ->mapWithKeys(fn ($status) => [$status => 0])
            ->merge(Booking::select('booking_status', DB::raw('count(*) as total'))->groupBy('booking_status')->pluck('total', 'booking_status'));

        $paymentStatuses = collect(['pending', 'paid', 'failed', 'refunded'])
            ->mapWithKeys(fn ($status) => [$status => 0])
            ->merge(Booking::select('payment_status', DB::raw('count(*) as total'))->groupBy('payment_status')->pluck('total', 'payment_status'));

        $facilityUsage = Facility::withCount('bookings')->orderByDesc('bookings_count')->take(8)->pluck('bookings_count', 'name');
        $recentBookings = Booking::with(['user', 'facility', 'payment'])->latest()->take(10)->get();
        $upcomingBookings = Booking::with(['user', 'facility'])
            ->whereDate('booking_date', '>=', $today)
            ->whereIn('booking_status', ['pending', 'approved'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->take(6)
            ->get();
        $lowInventoryFacilities = Facility::where('is_active', true)
            ->orderBy('inventory_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'summary',
            'monthlyRevenue',
            'bookingStatuses',
            'paymentStatuses',
            'facilityUsage',
            'recentBookings',
            'upcomingBookings',
            'lowInventoryFacilities'
        ));
    }
}
