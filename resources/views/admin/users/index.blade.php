@extends('layouts.admin')

@section('content')
    @php
        $filtersActive = request()->hasAny(['search', 'role', 'status']);
        $roleClasses = [
            'super_admin' => 'bg-purple-50 text-purple-700',
            'admin' => 'bg-cyan-50 text-cyan-700',
            'staff' => 'bg-emerald-50 text-emerald-700',
            'guest' => 'bg-slate-100 text-slate-700',
        ];
        $statusClasses = [
            'active' => 'bg-emerald-50 text-emerald-700',
            'inactive' => 'bg-red-50 text-red-700',
        ];
    @endphp

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Users</h1>
            <p class="mt-1 text-sm text-slate-500">Search accounts, review access, and manage user status.</p>
        </div>
        <div class="grid grid-cols-4 overflow-hidden rounded-lg bg-white text-center shadow-sm ring-1 ring-slate-200 md:min-w-[460px]">
            <div class="border-r border-slate-100 px-3 py-2">
                <div class="text-lg font-bold text-slate-950">{{ number_format($summary['total']) }}</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Total</div>
            </div>
            <div class="border-r border-slate-100 px-3 py-2">
                <div class="text-lg font-bold text-emerald-600">{{ number_format($summary['active']) }}</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Active</div>
            </div>
            <div class="border-r border-slate-100 px-3 py-2">
                <div class="text-lg font-bold text-cyan-700">{{ number_format($summary['staff']) }}</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Staff</div>
            </div>
            <div class="px-3 py-2">
                <div class="text-lg font-bold text-slate-950">{{ number_format($summary['filtered']) }}</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Shown</div>
            </div>
        </div>
    </div>

    <details class="mt-4 rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200" {{ $filtersActive ? 'open' : '' }}>
        <summary class="cursor-pointer list-none">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-950">Search and filters</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Find users by name, email, phone, role, or account status.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($filtersActive)
                        <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-700">Active</span>
                    @endif
                    <span class="text-xs font-semibold text-slate-500">Expand</span>
                </div>
            </div>
        </summary>

        <form class="mt-3 grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_180px_auto]">
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                </svg>
                <input name="search" value="{{ request('search') }}" class="h-9 w-full rounded-md border-slate-300 pl-9 pr-3 text-sm" placeholder="Search name, email, or phone">
            </div>
            <select name="role" class="h-9 rounded-md border-slate-300 text-sm">
                <option value="">All roles</option>
                @foreach (['guest','staff','admin','super_admin'] as $role)
                    <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                @endforeach
            </select>
            <select name="status" class="h-9 rounded-md border-slate-300 text-sm">
                <option value="">All statuses</option>
                @foreach (['active','inactive'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="h-9 rounded-md bg-cyan-700 px-4 text-sm font-semibold text-white transition hover:bg-cyan-600">Apply</button>
                @if ($filtersActive)
                    <a href="{{ route('admin.users.index') }}" class="inline-flex h-9 items-center rounded-md border border-slate-300 px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                @endif
            </div>
        </form>
    </details>

    <section class="mt-4 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3">
            <div>
                <h2 class="text-base font-bold text-slate-950">User Accounts</h2>
                <p class="mt-0.5 text-xs text-slate-500">Change access in-row. Delete is disabled for your own account.</p>
            </div>
            <div class="text-xs font-semibold text-slate-500">{{ $users->total() }} result{{ $users->total() === 1 ? '' : 's' }}</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-2">User</th>
                        <th class="px-4 py-2">Contact</th>
                        <th class="px-4 py-2">Current Access</th>
                        <th class="px-4 py-2">Update Access</th>
                        <th class="px-4 py-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-slate-900 text-sm font-bold text-white">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate font-bold text-slate-950">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500">Joined {{ $user->created_at?->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="truncate font-semibold text-slate-700">{{ $user->email }}</div>
                                <div class="text-xs text-slate-500">{{ $user->phone ?: 'No phone' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $roleClasses[$user->role] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClasses[$user->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="h-9 rounded-md border-slate-300 text-sm">
                                        @foreach (['guest','staff','admin','super_admin'] as $role)
                                            <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                                        @endforeach
                                    </select>
                                    <select name="status" class="h-9 rounded-md border-slate-300 text-sm" @disabled($user->id === auth()->id())>
                                        @foreach (['active','inactive'] as $status)
                                            <option value="{{ $status }}" @selected($user->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    @if ($user->id === auth()->id())
                                        <input type="hidden" name="status" value="{{ $user->status }}">
                                    @endif
                                    <button class="h-9 rounded-md bg-slate-900 px-3 text-sm font-semibold text-white transition hover:bg-slate-800">Save</button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($user->id === auth()->id())
                                    <span class="text-xs font-semibold text-slate-400">Current user</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-300 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center">
                                <h3 class="font-bold text-slate-950">No users found</h3>
                                <p class="mt-1 text-sm text-slate-500">Try adjusting the search or filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
