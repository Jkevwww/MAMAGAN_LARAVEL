@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">System Logs</h1>
    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3">Date</th><th class="p-3">User</th><th class="p-3">Action</th><th class="p-3">Target</th><th class="p-3">IP</th></tr></thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr class="border-t">
                        <td class="p-3">{{ $log->created_at->format('M d, Y g:i A') }}</td>
                        <td class="p-3">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="p-3">{{ $log->action }}</td>
                        <td class="p-3">{{ class_basename($log->target_type) }} #{{ $log->target_id }}</td>
                        <td class="p-3">{{ $log->ip_address }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
