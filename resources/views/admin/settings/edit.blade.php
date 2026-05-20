@extends('layouts.admin')

@section('content')
    @php
        $defaults = [
            'resort_name' => 'Mamagan Fun & Adventure Beach Resort',
            'email' => 'info@mamagan.test',
            'phone' => '',
            'address' => '',
            'business_hours' => '8:00 AM - 6:00 PM',
            'booking_rules' => '',
            'notification_settings' => '',
        ];
        $value = fn ($key) => old($key, $settings[$key] ?? $defaults[$key]);
        $lastUpdated = $summary['last_updated'] ? \Illuminate\Support\Carbon::parse($summary['last_updated'])->format('M d, Y g:i A') : 'Not saved yet';
        $cards = [
            ['label' => 'Required Fields', 'value' => $summary['completed_required'].' / '.$summary['required_count'], 'caption' => 'Public information completed', 'tone' => 'bg-cyan-50 text-cyan-700 ring-cyan-100', 'icon' => 'm9 12 2 2 4-4M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z'],
            ['label' => 'Setup Progress', 'value' => $summary['completion'].'%', 'caption' => 'Required settings only', 'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'icon' => 'M4 19V5M4 19h16M8 17v-6M12 17V7M16 17v-9'],
            ['label' => 'Saved Keys', 'value' => number_format($summary['total']), 'caption' => 'Records in app settings', 'tone' => 'bg-indigo-50 text-indigo-700 ring-indigo-100', 'icon' => 'M4 5h16M4 12h16M4 19h16'],
        ];
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Administration</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Settings</h1>
            <p class="mt-1 text-sm text-slate-500">Manage public resort details, booking rules, and staff notification notes.</p>
        </div>
        <div class="rounded-lg bg-white px-3 py-2 text-sm font-bold text-slate-600 shadow-sm ring-1 ring-slate-200">
            Last updated: {{ $lastUpdated }}
        </div>
    </div>

    @if (session('success'))
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <p class="font-bold">Please fix the settings form.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        @foreach ($cards as $card)
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $card['value'] }}</p>
                    </div>
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg ring-1 {{ $card['tone'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-slate-500">{{ $card['caption'] }}</p>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6">
        @csrf
        @method('PATCH')

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="grid gap-6">
                <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-bold text-slate-950">Resort Information</h2>
                            <p class="mt-1 text-sm text-slate-500">These details are used for guest-facing contact and booking areas.</p>
                        </div>
                        <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-bold text-cyan-700">Public</span>
                    </div>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Resort name
                            <input name="resort_name" value="{{ $value('resort_name') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" required>
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Email
                            <input name="email" type="email" value="{{ $value('email') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" required>
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Phone
                            <input name="phone" value="{{ $value('phone') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" placeholder="+63..." required>
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Business hours
                            <input name="business_hours" value="{{ $value('business_hours') }}" class="h-10 rounded-lg border-slate-300 text-sm font-normal" required>
                        </label>
                    </div>
                    <label class="mt-4 grid gap-1 text-sm font-bold text-slate-700">Address
                        <textarea name="address" rows="3" class="rounded-lg border-slate-300 text-sm font-normal" placeholder="Complete resort address" required>{{ $value('address') }}</textarea>
                    </label>
                </section>

                <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-bold text-slate-950">Operations Notes</h2>
                            <p class="mt-1 text-sm text-slate-500">Internal reference for bookings, reminders, and staff handling.</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Internal</span>
                    </div>
                    <div class="mt-5 grid gap-4">
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Booking rules
                            <textarea name="booking_rules" rows="5" class="rounded-lg border-slate-300 text-sm font-normal" placeholder="Cancellation, reservation, check-in, and guest rules">{{ $value('booking_rules') }}</textarea>
                        </label>
                        <label class="grid gap-1 text-sm font-bold text-slate-700">Notification settings
                            <textarea name="notification_settings" rows="5" class="rounded-lg border-slate-300 text-sm font-normal" placeholder="Email/SMS instructions, templates, reminders, or escalation notes">{{ $value('notification_settings') }}</textarea>
                        </label>
                    </div>
                </section>
            </div>

            <aside class="grid content-start gap-6">
                <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <h2 class="font-bold text-slate-950">Public Preview</h2>
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Resort</p>
                        <p class="mt-2 text-lg font-extrabold text-slate-950">{{ $value('resort_name') }}</p>
                        <div class="mt-4 grid gap-3 text-sm">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Contact</p>
                                <p class="mt-1 font-semibold text-slate-800">{{ $value('email') }}</p>
                                <p class="font-semibold text-slate-800">{{ $value('phone') ?: 'No phone set' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Hours</p>
                                <p class="mt-1 font-semibold text-slate-800">{{ $value('business_hours') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Address</p>
                                <p class="mt-1 whitespace-pre-line font-semibold text-slate-800">{{ $value('address') ?: 'No address set' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl bg-slate-950 p-4 text-white shadow-sm">
                    <h2 class="font-bold">Save Changes</h2>
                    <p class="mt-1 text-sm text-slate-400">Changes apply immediately to stored app settings.</p>
                    <button class="mt-4 inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-cyan-600 px-4 text-sm font-bold text-white transition hover:bg-cyan-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2ZM17 21v-8H7v8M7 3v5h8"/></svg>
                        Save Settings
                    </button>
                </section>
            </aside>
        </div>
    </form>
@endsection
