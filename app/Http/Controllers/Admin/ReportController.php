<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filteredQuery = $this->query($request);
        $analyticsBookings = (clone $filteredQuery)->get();
        $bookings = (clone $filteredQuery)->paginate(10)->withQueryString();
        $facilities = Facility::orderBy('name')->get();
        $categories = Facility::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $paidBookings = $analyticsBookings->where('payment_status', 'paid');
        $revenue = $paidBookings->sum('total_amount');
        $totals = [
            'bookings' => $analyticsBookings->count(),
            'revenue' => $revenue,
            'paid' => $paidBookings->count(),
            'unpaid' => $analyticsBookings->where('payment_status', '!=', 'paid')->count(),
            'average_paid' => $paidBookings->count() ? $revenue / $paidBookings->count() : 0,
            'guests' => $analyticsBookings->pluck('user_id')->unique()->count(),
        ];

        $bookingStatusBreakdown = collect(['pending', 'approved', 'checked_in', 'cancelled'])
            ->mapWithKeys(fn ($status) => [$status => $analyticsBookings->where('booking_status', $status)->count()]);
        $paymentStatusBreakdown = collect(['pending', 'paid', 'failed', 'refunded'])
            ->mapWithKeys(fn ($status) => [$status => $analyticsBookings->where('payment_status', $status)->count()]);
        $categoryBreakdown = $analyticsBookings
            ->groupBy(fn ($booking) => $booking->facility?->category ?: 'Uncategorized')
            ->map->count()
            ->sortDesc();
        $topFacilities = $analyticsBookings
            ->groupBy('facility_id')
            ->map(function ($rows) {
                $facility = $rows->first()->facility;

                return [
                    'name' => $facility?->name ?? 'Unknown facility',
                    'category' => $facility?->category ?? 'Uncategorized',
                    'bookings' => $rows->count(),
                    'revenue' => $rows->where('payment_status', 'paid')->sum('total_amount'),
                ];
            })
            ->sortByDesc('bookings')
            ->take(5)
            ->values();

        return view('admin.reports.index', compact(
            'bookings',
            'facilities',
            'categories',
            'totals',
            'bookingStatusBreakdown',
            'paymentStatusBreakdown',
            'categoryBreakdown',
            'topFacilities'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'mamagan-report-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference', 'Tourist', 'Facility', 'Category', 'Date', 'Booking Status', 'Payment Status', 'Total']);

            $this->query($request)->chunk(100, function ($bookings) use ($handle) {
                foreach ($bookings as $booking) {
                    fputcsv($handle, [
                        $booking->ticket?->reference_number,
                        $booking->user->name,
                        $booking->facility->name,
                        $booking->facility->category,
                        $booking->booking_date->format('Y-m-d'),
                        $booking->booking_status,
                        $booking->payment_status,
                        $booking->total_amount,
                    ]);
                }
            });

            fclose($handle);
        }, $filename);
    }

    private function query(Request $request)
    {
        return Booking::with(['user', 'facility', 'ticket'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->whereHas('user', fn ($user) => $user
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('facility', fn ($facility) => $facility
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%"))
                        ->orWhereHas('ticket', fn ($ticket) => $ticket
                            ->where('reference_number', 'like', "%{$search}%"));
                });
            })
            ->when($request->date_from, fn ($query, $date) => $query->whereDate('booking_date', '>=', $date))
            ->when($request->date_to, fn ($query, $date) => $query->whereDate('booking_date', '<=', $date))
            ->when($request->facility_id, fn ($query, $facilityId) => $query->where('facility_id', $facilityId))
            ->when($request->category, fn ($query, $category) => $query->whereHas('facility', fn ($facility) => $facility->where('category', $category)))
            ->when($request->booking_status, fn ($query, $status) => $query->where('booking_status', $status))
            ->when($request->payment_status, fn ($query, $status) => $query->where('payment_status', $status))
            ->latest();
    }
}
