@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">Settings</h1>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 grid gap-4 rounded-lg bg-white p-6 shadow-sm">
        @csrf @method('PATCH')
        <div class="grid gap-4 md:grid-cols-2">
            <input name="resort_name" value="{{ old('resort_name', $settings['resort_name'] ?? 'Mamagan Fun & Adventure Beach Resort') }}" class="rounded-md border-gray-300" placeholder="Resort name" required>
            <input name="email" type="email" value="{{ old('email', $settings['email'] ?? 'info@mamagan.test') }}" class="rounded-md border-gray-300" placeholder="Email" required>
            <input name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" class="rounded-md border-gray-300" placeholder="Phone" required>
            <input name="business_hours" value="{{ old('business_hours', $settings['business_hours'] ?? '8:00 AM - 6:00 PM') }}" class="rounded-md border-gray-300" placeholder="Business hours" required>
        </div>
        <textarea name="address" rows="2" class="rounded-md border-gray-300" placeholder="Address" required>{{ old('address', $settings['address'] ?? '') }}</textarea>
        <textarea name="booking_rules" rows="4" class="rounded-md border-gray-300" placeholder="Booking rules">{{ old('booking_rules', $settings['booking_rules'] ?? '') }}</textarea>
        <textarea name="notification_settings" rows="4" class="rounded-md border-gray-300" placeholder="Notification settings">{{ old('notification_settings', $settings['notification_settings'] ?? '') }}</textarea>
        <button class="w-fit rounded-md bg-cyan-700 px-5 py-2 font-semibold text-white">Save Settings</button>
    </form>
@endsection
