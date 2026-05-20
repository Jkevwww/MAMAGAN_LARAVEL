<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $filteredUsers = User::when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->role))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status));

        $users = (clone $filteredUsers)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'staff' => User::whereIn('role', ['staff', 'admin', 'super_admin'])->count(),
            'filtered' => (clone $filteredUsers)->count(),
        ];

        return view('admin.users.index', compact('users', 'summary'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:guest,staff,admin,super_admin'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        abort_if($user->id === auth()->id() && $data['status'] === 'inactive', 422);

        $user->update($data);

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 422);
        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
