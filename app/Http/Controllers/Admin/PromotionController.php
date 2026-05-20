<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $filteredQuery = $this->filteredPromotionQuery($request);
        $analyticsPromotions = (clone $filteredQuery)->get();
        $promotions = (clone $filteredQuery)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => $analyticsPromotions->count(),
            'usable' => $analyticsPromotions->filter(fn ($promotion) => $this->isUsableNow($promotion, $today))->count(),
            'inactive' => $analyticsPromotions->where('is_active', false)->count(),
            'expired' => $analyticsPromotions->filter(fn ($promotion) => $promotion->ends_at?->lt($today))->count(),
            'used' => $analyticsPromotions->sum('used_count'),
        ];
        $discountBreakdown = $analyticsPromotions
            ->groupBy('discount_type')
            ->map->count()
            ->sortDesc();

        return view('admin.promotions.index', [
            'promotions' => $promotions,
            'facilities' => Facility::orderBy('name')->get(),
            'summary' => $summary,
            'discountBreakdown' => $discountBreakdown,
        ]);
    }

    public function store(Request $request)
    {
        Promotion::create($this->validated($request));

        return back()->with('success', 'Promotion saved.');
    }

    public function update(Request $request, Promotion $promotion)
    {
        $promotion->update($this->validated($request));

        return back()->with('success', 'Promotion updated.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return back()->with('success', 'Promotion deleted.');
    }

    private function validated(Request $request): array
    {
        $promotion = $request->route('promotion');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('promotions', 'code')->ignore($promotion?->id)],
            'discount_type' => ['required', 'in:percent,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'minimum_amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'facility_id' => ['nullable', 'exists:facilities,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');
        $data['minimum_amount'] = $data['minimum_amount'] ?? 0;

        return $data;
    }

    private function filteredPromotionQuery(Request $request)
    {
        return Promotion::with('facility')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhereHas('facility', fn ($facility) => $facility
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->status === 'active'))
            ->when($request->filled('discount_type'), fn ($query) => $query->where('discount_type', $request->discount_type))
            ->when($request->filled('facility_id'), fn ($query) => $request->facility_id === 'all'
                ? $query->whereNull('facility_id')
                : $query->where('facility_id', $request->facility_id));
    }

    private function isUsableNow(Promotion $promotion, Carbon $today): bool
    {
        return $promotion->is_active
            && (! $promotion->starts_at || $promotion->starts_at->lte($today))
            && (! $promotion->ends_at || $promotion->ends_at->gte($today))
            && (! $promotion->usage_limit || $promotion->used_count < $promotion->usage_limit);
    }
}
