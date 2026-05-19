<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-stone-200 bg-white/95 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                        <img src="{{ asset('assets/logo/mamagan.png') }}" alt="Mamagan Fun & Adventure Beach Resort" class="h-9 w-auto max-w-[150px] object-contain sm:max-w-[180px]">
                    </a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">{{ __('Facilities') }}</x-nav-link>
                    @auth
                    <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">{{ __('My Bookings') }}</x-nav-link>
                    @if(Auth::user()->isStaff())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">{{ __('Admin') }}</x-nav-link>
                    @endif
                    @endauth
                </div>
            </div>

            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-md border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-semibold leading-4 text-slate-700 transition hover:bg-white hover:text-slate-950 focus:outline-none">
                            <div class="max-w-32 truncate">{{ Auth::user()->name }}</div>
                            <div class="ms-1"><svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @else
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-700 transition hover:text-teal-700">Log in</a>
                <a href="{{ route('register') }}" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-teal-600">Register</a>
            </div>
            @endauth

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            <x-responsive-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">{{ __('Facilities') }}</x-responsive-nav-link>
            @auth
            <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">{{ __('My Bookings') }}</x-responsive-nav-link>
            @if(Auth::user()->isStaff())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">{{ __('Admin') }}</x-responsive-nav-link>
            @endif
            @else
            <x-responsive-nav-link :href="route('login')">{{ __('Log in') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('register')">{{ __('Register') }}</x-responsive-nav-link>
            @endauth
        </div>
        @auth
        <div class="border-t border-slate-200 pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-bold text-slate-900">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-slate-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
