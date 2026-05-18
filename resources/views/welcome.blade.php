<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mamagan Fun & Adventure Beach Resort</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    <div class="min-h-screen">
        <section class="relative overflow-hidden">
            <img
                src="{{ asset('assets/backgrounds/background.jpg') }}"
                alt="Mamagan Beach shoreline"
                fetchpriority="high"
                class="absolute inset-0 h-full w-full object-cover"
            >
            <div class="absolute inset-0 bg-slate-950/55"></div>

            <header class="relative z-10 mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center">
                    <img
                        src="{{ asset('assets/logo/mamagan.png') }}"
                        alt="Mamagan Fun & Adventure Beach Resort"
                        class="h-11 w-auto max-w-[150px] object-contain sm:h-14 sm:max-w-[210px]"
                    >
                </a>
                <nav class="flex min-w-0 items-center gap-2 text-sm font-semibold">
                    <a href="{{ route('facilities.index') }}" class="hidden rounded-md px-3 py-2 text-white hover:bg-white/10 sm:inline-flex">Facilities</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-white px-4 py-2 text-slate-900 hover:bg-cyan-50">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-white hover:bg-white/10">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-cyan-600 px-4 py-2 text-white hover:bg-cyan-500">Register</a>
                    @endauth
                </nav>
            </header>

            <div class="relative z-10 mx-auto flex max-w-7xl items-start px-4 pb-24 pt-12 sm:px-6 sm:pb-28 sm:pt-16 lg:px-8 lg:pb-32 lg:pt-20">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cyan-100">Online Booking and Management System</p>
                    <h1 class="mt-4 max-w-2xl text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                        Mamagan Fun & Adventure Beach Resort
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-100 sm:text-lg">
                        Browse cottages, cabanas, rooms, and beach equipment. Reserve your date, submit a payment reference, and receive a QR ticket once your booking is verified.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('facilities.index') }}" class="rounded-md bg-cyan-600 px-6 py-3 text-center font-semibold text-white shadow-lg shadow-cyan-950/20 hover:bg-cyan-500">
                            Browse Facilities
                        </a>
                        <a href="#booking-flow" class="rounded-md border border-white/70 px-6 py-3 text-center font-semibold text-white hover:bg-white/10">
                            How Booking Works
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="booking-flow" class="bg-white px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cyan-700">Simple booking flow</p>
                        <h2 class="mt-3 text-2xl font-bold text-slate-950 sm:text-3xl">Fast booking, clear verification</h2>
                    </div>
                    <p class="max-w-xl leading-7 text-slate-600">
                        Pick your date, reserve the facility you need, and wait for staff confirmation before arrival.
                    </p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-cyan-700 text-sm font-bold text-white">01</div>
                        <h3 class="mt-5 text-lg font-bold text-slate-950">Choose your spot</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Browse cottages, cabanas, rooms, and beach equipment with capacity and price details before you book.</p>
                    </div>
                    <div class="rounded-lg border border-cyan-200 bg-cyan-50 p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-cyan-700 text-sm font-bold text-white">02</div>
                        <h3 class="mt-5 text-lg font-bold text-slate-950">Send your request</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Enter your schedule, guest count, and payment reference so the resort can review your reservation.</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-cyan-700 text-sm font-bold text-white">03</div>
                        <h3 class="mt-5 text-lg font-bold text-slate-950">Arrive with your QR</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">After approval, show your QR ticket at check-in for faster verification at the resort entrance.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-slate-950 px-4 py-16 text-white sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cyan-200">One system, two views</p>
                    <h2 class="mt-3 text-2xl font-bold sm:text-3xl">Built for tourists and resort staff</h2>
                    <p class="mt-4 leading-7 text-slate-300">
                        Guests get a direct path from browsing to check-in. Staff get the tools needed to verify payments, manage availability, and keep daily operations organized.
                    </p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg bg-white p-5 text-slate-950">
                        <p class="text-sm font-semibold text-cyan-700">For guests</p>
                        <h3 class="mt-2 text-xl font-bold">Book with less waiting</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">View facilities, submit booking details, track status, and access the approved QR ticket from one account.</p>
                    </div>
                    <div class="rounded-lg bg-white p-5 text-slate-950">
                        <p class="text-sm font-semibold text-cyan-700">For staff</p>
                        <h3 class="mt-2 text-xl font-bold">Verify faster on site</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Review payment references, confirm reservations, scan QR tickets, and record check-ins without paper lists.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                        <p class="text-sm font-semibold text-cyan-200">Availability</p>
                        <h3 class="mt-2 text-xl font-bold">Control schedules</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Manage facilities, seasonal rates, promos, and blackout dates so guests see accurate booking options.</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                        <p class="text-sm font-semibold text-cyan-200">Reports</p>
                        <h3 class="mt-2 text-xl font-bold">See daily activity</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Monitor reservations, payment status, facility usage, and revenue through admin dashboard reports.</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="border-t border-slate-800 bg-slate-950 px-4 py-10 text-white sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-[1.2fr_0.8fr_0.8fr]">
                <div>
                    <img
                        src="{{ asset('assets/logo/mamagan.png') }}"
                        alt="Mamagan Fun & Adventure Beach Resort"
                        class="h-12 w-auto max-w-[190px] object-contain"
                    >
                    <p class="mt-4 max-w-md text-sm leading-6 text-slate-400">
                        Online booking and management for Mamagan Fun & Adventure Beach Resort.
                    </p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-200">Explore</h2>
                    <div class="mt-4 grid gap-3 text-sm text-slate-300">
                        <a href="{{ route('home') }}" class="hover:text-white">Home</a>
                        <a href="{{ route('facilities.index') }}" class="hover:text-white">Facilities</a>
                        <a href="#booking-flow" class="hover:text-white">Booking flow</a>
                    </div>
                </div>
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-200">Account</h2>
                    <div class="mt-4 grid gap-3 text-sm text-slate-300">
                        @auth
                            <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                            <a href="{{ route('bookings.index') }}" class="hover:text-white">My bookings</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-white">Log in</a>
                            <a href="{{ route('register') }}" class="hover:text-white">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
            <div class="mx-auto mt-8 flex max-w-7xl flex-col gap-3 border-t border-slate-800 pt-6 text-sm text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} Mamagan Fun & Adventure Beach Resort. All rights reserved.</p>
                <p>Booking, payment verification, and QR check-in in one place.</p>
            </div>
        </footer>
    </div>
</body>
</html>
