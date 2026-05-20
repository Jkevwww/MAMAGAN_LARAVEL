<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlackoutDate;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BlackoutDateController extends Controller
{
    public function index(Request $request)
    {
        $calendarMonth = $request->input('calendar_month', now()->format('Y-m'));

        try {
            $calendarDate = preg_match('/^\d{4}-\d{2}$/', $calendarMonth)
                ? Carbon::createFromFormat('Y-m', $calendarMonth)->startOfMonth()
                : now()->startOfMonth();
        } catch (\Throwable) {
            $calendarDate = now()->startOfMonth();
        }
        $calendarStart = $calendarDate->copy()->startOfWeek();
        $calendarEnd = $calendarDate->copy()->endOfMonth()->endOfWeek();

        $blackouts = $this->filteredBlackoutQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $calendarBlackouts = $this->filteredBlackoutQuery($request)
            ->whereDate('starts_at', '<=', $calendarEnd)
            ->whereDate('ends_at', '>=', $calendarStart)
            ->orderBy('starts_at')
            ->get();

        $today = now()->toDateString();
        $summary = [
            'total' => $this->filteredBlackoutQuery($request)->count(),
            'active_today' => $this->filteredBlackoutQuery($request)
                ->whereDate('starts_at', '<=', $today)
                ->whereDate('ends_at', '>=', $today)
                ->count(),
            'this_month' => $this->filteredBlackoutQuery($request)
                ->whereDate('starts_at', '<=', $calendarDate->copy()->endOfMonth())
                ->whereDate('ends_at', '>=', $calendarDate)
                ->count(),
            'all_facilities' => $this->filteredBlackoutQuery($request)
                ->whereNull('facility_id')
                ->count(),
        ];

        $upcomingBlackouts = $this->filteredBlackoutQuery($request)
            ->whereDate('ends_at', '>=', $today)
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        return view('admin.blackouts.index', [
            'blackouts' => $blackouts,
            'calendarBlackouts' => $calendarBlackouts,
            'calendarDate' => $calendarDate,
            'calendarStart' => $calendarStart,
            'calendarEnd' => $calendarEnd,
            'summary' => $summary,
            'upcomingBlackouts' => $upcomingBlackouts,
            'facilities' => Facility::orderBy('name')->get(),
        ]);
    }

    private function filteredBlackoutQuery(Request $request)
    {
        return BlackoutDate::with('facility')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('facility', fn ($facility) => $facility->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('facility_id'), fn ($query) => $request->facility_id === 'all'
                ? $query->whereNull('facility_id')
                : $query->where('facility_id', $request->facility_id))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('ends_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('starts_at', '<=', $request->date_to));
    }

    public function store(Request $request)
    {
        BlackoutDate::create($this->validated($request));

        return back()->with('success', 'Blackout window saved.');
    }

    public function update(Request $request, BlackoutDate $blackoutDate)
    {
        $blackoutDate->update($this->validated($request));

        return back()->with('success', 'Blackout window updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'facility_id' => ['nullable', 'exists:facilities,id'],
            'title' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'reason' => ['nullable', 'string'],
        ]);
    }

    public function destroy(BlackoutDate $blackoutDate)
    {
        $blackoutDate->delete();

        return back()->with('success', 'Blackout window deleted.');
    }
}
