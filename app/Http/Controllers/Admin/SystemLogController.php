<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $filteredQuery = $this->filteredLogQuery($request);
        $analyticsLogs = (clone $filteredQuery)->get();
        $logs = (clone $filteredQuery)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $actions = SystemLog::distinct()->orderBy('action')->pluck('action')->filter()->values();
        $summary = [
            'total' => $analyticsLogs->count(),
            'today' => $analyticsLogs->where('created_at', '>=', now()->startOfDay())->count(),
            'actors' => $analyticsLogs->pluck('user_id')->filter()->unique()->count(),
            'system' => $analyticsLogs->whereNull('user_id')->count(),
        ];
        $actionBreakdown = $analyticsLogs
            ->groupBy('action')
            ->map->count()
            ->sortDesc()
            ->take(6);

        return view('admin.logs.index', compact('logs', 'actions', 'summary', 'actionBreakdown'));
    }

    private function filteredLogQuery(Request $request)
    {
        return SystemLog::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($inner) use ($search) {
                    $inner->where('action', 'like', "%{$search}%")
                        ->orWhere('target_type', 'like', "%{$search}%")
                        ->orWhere('target_id', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->action))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to));
    }
}
