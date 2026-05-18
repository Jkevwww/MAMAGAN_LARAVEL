<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = SystemLog::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($inner) use ($search) {
                    $inner->where('action', 'like', "%{$search}%")
                        ->orWhere('target_type', 'like', "%{$search}%")
                        ->orWhere('target_id', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->action))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $actions = SystemLog::distinct()->orderBy('action')->pluck('action')->filter()->values();

        return view('admin.logs.index', compact('logs', 'actions'));
    }
}
