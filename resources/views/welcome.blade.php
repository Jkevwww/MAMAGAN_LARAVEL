<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mamagan Fun & Adventure Beach Resort</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-stone-50 text-slate-950">
    <main>
        <section class="relative min-h-[88vh] overflow-hidden bg-slate-950">
            <img
                src="{{ asset('assets/backgrounds/background.jpg') }}"
                alt="Mamagan Beach Resort cottages near the shoreline"
                class="absolute inset-0 h-full w-full object-cover"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-950/55 to-slate-950/20"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-stone-50 to-transparent"></div>

            <header class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-3 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-md bg-white/95 px-2 py-2 shadow-sm sm:px-3">
                    <img src="{{ asset('assets/logo/mamagan.png') }}" alt="Mamagan Fun & Adventure Beach Resort" class="h-8 w-auto max-w-[120px] object-contain sm:h-10 sm:max-w-[240px]">
                </a>

                <nav class="flex items-center gap-1 text-xs font-semibold sm:gap-2 sm:text-sm">
                    <a href="{{ route('facilities.index') }}" class="hidden rounded-md px-3 py-2 text-white transition hover:bg-white/10 sm:inline-flex">Facilities</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-white px-4 py-2 text-slate-950 shadow-sm transition hover:bg-amber-50">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-2 py-2 text-white transition hover:bg-white/10 sm:px-3">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-teal-600 px-3 py-2 text-white shadow-sm transition hover:bg-teal-500 sm:px-4">Register</a>
                    @endauth
                </nav>
            </header>

            <div class="relative z-10 mx-auto flex min-h-[calc(88vh-74px)] max-w-7xl items-center px-4 pb-24 pt-10 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-200">Online Booking and Reservation System</p>
                    <h1 class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                        Mamagan Fun & Adventure Beach Resort
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-100 sm:text-lg">
                        Reserve cottages, cabanas, rooms, and beach equipment with verified payments and QR tickets for faster resort check-in.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('facilities.index') }}" class="rounded-md bg-teal-600 px-6 py-3 text-center font-bold text-white shadow-lg shadow-slate-950/25 transition hover:bg-teal-500">
                            Browse Facilities
                        </a>
                        <a href="#booking-flow" class="rounded-md border border-white/75 px-6 py-3 text-center font-bold text-white transition hover:bg-white/10">
                            View Booking Steps
                        </a>
                    </div>

                    <dl class="mt-10 grid max-w-2xl grid-cols-3 gap-3 text-white">
                        <div class="border-l-2 border-amber-300 pl-3">
                            <dt class="text-xs uppercase tracking-wide text-slate-200">Stay</dt>
                            <dd class="mt-1 text-sm font-bold sm:text-base">Rooms and cottages</dd>
                        </div>
                        <div class="border-l-2 border-teal-300 pl-3">
                            <dt class="text-xs uppercase tracking-wide text-slate-200">Entry</dt>
                            <dd class="mt-1 text-sm font-bold sm:text-base">QR ticket check-in</dd>
                        </div>
                        <div class="border-l-2 border-sky-300 pl-3">
                            <dt class="text-xs uppercase tracking-wide text-slate-200">Payment</dt>
                            <dd class="mt-1 text-sm font-bold sm:text-base">Reference tracking</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <section id="booking-flow" class="relative -mt-12 px-4 pb-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl rounded-lg bg-white shadow-xl shadow-slate-900/10 ring-1 ring-slate-200">
                <div class="grid gap-0 md:grid-cols-3">
                    <article class="p-6 md:p-8">
                        <p class="text-sm font-bold text-teal-700">Step 1</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-950">Choose your facility</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Browse the resort inventory by category, capacity, price range, and availability.</p>
                    </article>
                    <article class="border-y border-slate-200 p-6 md:border-x md:border-y-0 md:p-8">
                        <p class="text-sm font-bold text-teal-700">Step 2</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-950">Reserve your date</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Submit guest details, schedule, quantity, and payment reference in the booking flow.</p>
                    </article>
                    <article class="p-6 md:p-8">
                        <p class="text-sm font-bold text-teal-700">Step 3</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-950">Check in with QR</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">After verification, use the generated ticket so resort staff can confirm arrival quickly.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="px-4 pb-16 sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-teal-700">Resort Operations</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">One place for guest bookings and staff verification</h2>
                    <p class="mt-5 leading-7 text-slate-600">
                        Guests get a direct path from facility browsing to booking status. Staff and admins get tools for payment approval, check-in lookup, reports, promotions, blackout dates, and user management.
                    </p>
                    <div class="mt-7">
                        <a href="{{ route('facilities.index') }}" class="inline-flex rounded-md bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                            See Available Facilities
                        </a>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-semibold text-slate-500">Facilities</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-950">Cottages, cabanas, rooms, and equipment</h3>
                    </article>
                    <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-semibold text-slate-500">Tickets</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-950">Printable QR codes for approved bookings</h3>
                    </article>
                    <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-semibold text-slate-500">Payments</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-950">Manual reference review and PayMongo checkout</h3>
                    </article>
                    <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-semibold text-slate-500">Admin</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-950">Reports, settings, promotions, and logs</h3>
                    </article>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-slate-950 px-4 py-8 text-white sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="font-bold">Mamagan Fun & Adventure Beach Resort</div>
            <div class="text-sm text-slate-400">&copy; {{ date('Y') }} All rights reserved.</div>
        </div>
    </footer>
</body>
</html>
