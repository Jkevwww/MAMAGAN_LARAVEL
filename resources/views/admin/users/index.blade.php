@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Users</h1>
    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Phone</th><th class="p-3">Access</th><th class="p-3"></th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-t">
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">{{ $user->phone }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-wrap gap-2">
                                @csrf @method('PATCH')
                                <select name="role" class="rounded-md border-gray-300 text-sm">
                                    @foreach (['guest','staff','admin','super_admin'] as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                                    @endforeach
                                </select>
                                <select name="status" class="rounded-md border-gray-300 text-sm">
                                    @foreach (['active','inactive'] as $status)
                                        <option value="{{ $status }}" @selected($user->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button class="rounded-md bg-slate-900 px-3 py-2 text-white">Save</button>
                            </form>
                        </td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE') <button class="text-red-700">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
@endsection
