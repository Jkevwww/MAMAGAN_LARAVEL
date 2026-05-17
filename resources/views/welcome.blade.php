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
        <section class="relative min-h-[92vh] overflow-hidden">
            <img
                src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=2200&q=85"
                alt="Mamagan Beach shoreline"
                class="absolute inset-0 h-full w-full object-cover"
            >
            <div class="absolute inset-0 bg-slate-950/55"></div>

            <header class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="text-lg font-bold tracking-wide text-white sm:text-xl">
                    MAMAGAN
                </a>
                <nav class="flex items-center gap-2 text-sm font-semibold">
                    <a href="{{ route('facilities.index') }}" class="hidden rounded-md px-3 py-2 text-white hover:bg-white/10 sm:inline-flex">Facilities</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-white px-4 py-2 text-slate-900 hover:bg-cyan-50">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-white hover:bg-white/10">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-cyan-600 px-4 py-2 text-white hover:bg-cyan-500">Register</a>
                    @endauth
                </nav>
            </header>

            <div class="relative z-10 mx-auto flex min-h-[calc(92vh-76px)] max-w-7xl items-center px-4 pb-14 sm:px-6 lg:px-8">
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
                <div class="max-w-2xl">
                    <h2 class="text-2xl font-bold text-slate-950 sm:text-3xl">Fast booking, clear verification</h2>
                    <p class="mt-3 text-slate-600">The system keeps tourist booking and resort operations in one flow.</p>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="text-sm font-semibold text-cyan-700">Step 1</div>
                        <h3 class="mt-2 font-bold">Choose a facility</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Filter available cottages, cabanas, rooms, and beach equipment by category, price, rating, and capacity.</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="text-sm font-semibold text-cyan-700">Step 2</div>
                        <h3 class="mt-2 font-bold">Reserve and pay</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Enter schedule, quantity, guest count, and promo code, then submit your GCash or payment reference for verification.</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="text-sm font-semibold text-cyan-700">Step 3</div>
                        <h3 class="mt-2 font-bold">Use your QR ticket</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">After payment approval, print or show the QR ticket so staff can verify and check you in at the resort.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                <div>
                    <h2 class="text-2xl font-bold text-slate-950 sm:text-3xl">Built for tourists and resort staff</h2>
                    <p class="mt-4 leading-7 text-slate-600">
                        Guests get simple booking and ticket access. Staff and admins get payment verification, QR check-in, reports, user management, promotions, blackout dates, and dashboard charts.
                    </p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm text-slate-500">Facilities</p>
                        <p class="mt-2 text-2xl font-bold">Cottages, rooms, equipment</p>
                    </div>
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm text-slate-500">Tickets</p>
                        <p class="mt-2 text-2xl font-bold">QR check-in ready</p>
                    </div>
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm text-slate-500">Payments</p>
                        <p class="mt-2 text-2xl font-bold">Reference tracking</p>
                    </div>
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm text-slate-500">Reports</p>
                        <p class="mt-2 text-2xl font-bold">Revenue and usage</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-slate-950 px-4 py-8 text-white sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="font-bold tracking-wide">MAMAGAN</div>
                <div class="text-sm text-slate-400">&copy; {{ date('Y') }} Mamagan Fun & Adventure Beach Resort. All rights reserved.</div>
            </div>
        </footer>
    </div>
</body>
</html>
