<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mamagan Beach Resort</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-50 text-gray-900">
        <div class="relative min-h-screen">
            <!-- Hero Section -->
            <div class="relative h-screen flex items-center justify-center overflow-hidden">
                <div class="absolute inset-0 z-0">
                    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Beach" class="w-full h-full object-cover brightness-50">
                </div>
                
                <header class="absolute top-0 left-0 right-0 p-6 flex justify-between items-center z-10">
                    <div class="text-white text-2xl font-bold tracking-tighter">MAMAGAN</div>
                    <nav class="flex gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-white font-medium hover:text-indigo-200 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-white font-medium hover:text-indigo-200 transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md font-medium hover:bg-indigo-700 transition">Register</a>
                            @endif
                        @endauth
                    </nav>
                </header>

                <div class="relative z-10 text-center px-4 max-w-4xl">
                    <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight">Experience Paradise at Mamagan Beach</h1>
                    <p class="text-xl text-gray-200 mb-10 leading-relaxed">Book your perfect beach getaway today. Beautiful cottages, crystal clear water, and unforgettable sunsets await you.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('cottages.index') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition shadow-xl">Browse Cottages</a>
                        <a href="#info" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <section id="info" class="py-24 px-6 max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">Why Choose Mamagan?</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">We offer the best resort experience with automated booking and secure payments.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Real-time Availability</h3>
                        <p class="text-gray-600 leading-relaxed">Check cottage availability instantly for your preferred dates. No more waiting for phone confirmations.</p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-1.382-3.072z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Secure GCash Payment</h3>
                        <p class="text-gray-600 leading-relaxed">Conveniently pay via GCash and upload your receipt for quick verification by our team.</p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Digital Receipts</h3>
                        <p class="text-gray-600 leading-relaxed">Download and print your reservation receipts anytime from your personalized dashboard.</p>
                    </div>
                </div>
            </section>

            <footer class="bg-gray-900 text-white py-12 px-6">
                <div class="max-w-7xl mx-auto flex flex-col md:row justify-between items-center border-t border-gray-800 pt-8 mt-8">
                    <div class="text-2xl font-bold mb-4 md:mb-0 tracking-tighter">MAMAGAN</div>
                    <div class="text-gray-400 text-sm">© {{ date('Y') }} Mamagan Beach Resort. All rights reserved.</div>
                </div>
            </footer>
        </div>
    </body>
</html>
