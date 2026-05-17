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
        $bookings = $this->query($request)->paginate(25)->withQueryString();
        $facilities = Facility::orderBy('name')->get();
        $totals = [
            'bookings' => $this->query($request)->count(),
            'revenue' => $this->query($request)->where('payment_status', 'paid')->sum('total_amount'),
            'paid' => $this->query($request)->where('payment_status', 'paid')->count(),
            'unpaid' => $this->query($request)->where('payment_status', '!=', 'paid')->count(),
        ];

        return view('admin.reports.index', compact('bookings', 'facilities', 'totals'));
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
            ->when($request->date_from, fn ($query, $date) => $query->whereDate('booking_date', '>=', $date))
            ->when($request->date_to, fn ($query, $date) => $query->whereDate('booking_date', '<=', $date))
            ->when($request->facility_id, fn ($query, $facilityId) => $query->where('facility_id', $facilityId))
            ->when($request->category, fn ($query, $category) => $query->whereHas('facility', fn ($facility) => $facility->where('category', $category)))
            ->when($request->booking_status, fn ($query, $status) => $query->where('booking_status', $status))
            ->when($request->payment_status, fn ($query, $status) => $query->where('payment_status', $status))
            ->latest();
    }
}
