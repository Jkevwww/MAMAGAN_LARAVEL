@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Users</h1>
            <p class="mt-1 text-sm text-slate-500">Search users and manage account access.</p>
        </div>
    </div>

    <form class="mt-6 flex flex-col items-end gap-2" x-data="{ filtersOpen: {{ request()->hasAny(['role', 'status']) ? 'true' : 'false' }} }">
        <div class="inline-flex w-full items-center gap-1.5 rounded-lg bg-white p-1.5 shadow-sm ring-1 ring-slate-200 sm:w-auto">
            <div class="relative min-w-0 flex-1 sm:flex-none">
                <svg class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                </svg>
                <input name="search" value="{{ request('search') }}" class="h-8 w-full rounded-md border-slate-300 py-1 pl-8 pr-2 text-sm sm:w-72" placeholder="Search users">
            </div>
            <button type="button" data-no-loader="true" @click="filtersOpen = !filtersOpen" class="relative grid h-8 w-8 shrink-0 place-items-center rounded-md border border-slate-300 text-slate-700 transition hover:bg-slate-50" aria-label="Toggle filters">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/></svg>
                @if (request()->hasAny(['role', 'status']))
                    <span class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-cyan-600 ring-2 ring-white"></span>
                @endif
            </button>
            <button class="h-8 rounded-md bg-cyan-700 px-3 text-sm font-semibold text-white transition hover:bg-cyan-600">Search</button>
        </div>

        <div x-show="filtersOpen" x-transition x-cloak class="w-full rounded-lg bg-white p-4 shadow-lg ring-1 ring-slate-200 sm:w-[480px]">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-950">Filter users</h2>
                    <p class="mt-1 text-xs text-slate-500">Review accounts by role and status.</p>
                </div>
                <button type="button" data-no-loader="true" @click="filtersOpen = false" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Close filters">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-semibold text-slate-700">
                    Role
                    <select name="role" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                        <option value="">All roles</option>
                        @foreach (['guest','staff','admin','super_admin'] as $role)
                            <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">
                    Status
                    <select name="status" class="h-9 rounded-md border-slate-300 text-sm font-normal">
                        <option value="">All statuses</option>
                        @foreach (['active','inactive'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('admin.users.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                <button class="rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-600">Apply filters</button>
            </div>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600"><tr><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Phone</th><th class="p-3">Access</th><th class="p-3"></th></tr></thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-t transition hover:bg-slate-50">
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">{{ $user->phone ?: 'None' }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-wrap gap-2">
                                @csrf @method('PATCH')
                                <select name="role" class="h-9 rounded-md border-gray-300 text-sm">
                                    @foreach (['guest','staff','admin','super_admin'] as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                                    @endforeach
                                </select>
                                <select name="status" class="h-9 rounded-md border-gray-300 text-sm">
                                    @foreach (['active','inactive'] as $status)
                                        <option value="{{ $status }}" @selected($user->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button class="h-9 rounded-md bg-slate-900 px-3 text-sm font-semibold text-white transition hover:bg-slate-800">Save</button>
                            </form>
                        </td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE') <button class="text-sm font-semibold text-red-700 hover:text-red-800">Delete</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
@endsection
