@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Settings</h1>
            <p class="mt-1 text-sm text-slate-500">Manage public resort details, booking rules, and notification notes.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 grid gap-6">
        @csrf @method('PATCH')

        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="font-semibold text-slate-950">Resort information</h2>
                <p class="mt-1 text-sm text-slate-500">Shown to guests across booking and contact areas.</p>
            </div>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label class="grid gap-1 text-sm font-semibold text-slate-700">Resort name
                    <input name="resort_name" value="{{ old('resort_name', $settings['resort_name'] ?? 'Mamagan Fun & Adventure Beach Resort') }}" class="rounded-md border-gray-300 text-sm font-normal" required>
                </label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">Email
                    <input name="email" type="email" value="{{ old('email', $settings['email'] ?? 'info@mamagan.test') }}" class="rounded-md border-gray-300 text-sm font-normal" required>
                </label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">Phone
                    <input name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" class="rounded-md border-gray-300 text-sm font-normal" required>
                </label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">Business hours
                    <input name="business_hours" value="{{ old('business_hours', $settings['business_hours'] ?? '8:00 AM - 6:00 PM') }}" class="rounded-md border-gray-300 text-sm font-normal" required>
                </label>
            </div>
            <label class="mt-4 grid gap-1 text-sm font-semibold text-slate-700">Address
                <textarea name="address" rows="2" class="rounded-md border-gray-300 text-sm font-normal" required>{{ old('address', $settings['address'] ?? '') }}</textarea>
            </label>
        </section>

        <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="font-semibold text-slate-950">Operations</h2>
                <p class="mt-1 text-sm text-slate-500">Internal guidance used by staff and admins.</p>
            </div>
            <div class="mt-5 grid gap-4">
                <label class="grid gap-1 text-sm font-semibold text-slate-700">Booking rules
                    <textarea name="booking_rules" rows="4" class="rounded-md border-gray-300 text-sm font-normal" placeholder="Cancellation, reservation, and check-in rules">{{ old('booking_rules', $settings['booking_rules'] ?? '') }}</textarea>
                </label>
                <label class="grid gap-1 text-sm font-semibold text-slate-700">Notification settings
                    <textarea name="notification_settings" rows="4" class="rounded-md border-gray-300 text-sm font-normal" placeholder="Email/SMS instructions or reminders">{{ old('notification_settings', $settings['notification_settings'] ?? '') }}</textarea>
                </label>
            </div>
        </section>

        <div class="flex justify-end">
            <button class="rounded-md bg-cyan-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-cyan-600">Save Settings</button>
        </div>
    </form>
@endsection
