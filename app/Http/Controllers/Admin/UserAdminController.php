<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->role, fn ($query, $role) => $query->where('role', $role))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
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
