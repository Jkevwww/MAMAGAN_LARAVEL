<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlackoutDate;
use App\Models\Facility;
use Illuminate\Http\Request;

class BlackoutDateController extends Controller
{
    public function index(Request $request)
    {
        $blackouts = BlackoutDate::with('facility')
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
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('starts_at', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.blackouts.index', [
            'blackouts' => $blackouts,
            'facilities' => Facility::orderBy('name')->get(),
        ]);
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
