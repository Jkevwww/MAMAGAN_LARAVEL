@extends('layouts.admin')

@section('content')
    @php
        $cards = [
            ['label' => 'Expected Today', 'value' => $summary['expected_today'], 'caption' => 'Paid bookings dated today', 'tone' => 'bg-cyan-50 text-cyan-700 ring-cyan-100', 'icon' => 'M7 2v3M17 2v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z'],
            ['label' => 'Checked In', 'value' => $summary['checked_in_today'], 'caption' => 'Completed today', 'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'icon' => 'm9 12 2 2 4-4M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z'],
            ['label' => 'Waiting', 'value' => $summary['waiting_today'], 'caption' => 'Paid and not checked in', 'tone' => 'bg-amber-50 text-amber-700 ring-amber-100', 'icon' => 'M12 6v6l4 2M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z'],
            ['label' => 'Unpaid Today', 'value' => $summary['unpaid_today'], 'caption' => 'Payment action needed', 'tone' => 'bg-rose-50 text-rose-700 ring-rose-100', 'icon' => 'M12 9v4M12 17h.01M10.29 3.86 1.71-1 1.71 1 8.49 14.7-1.71 3H3.8l-1.71-3 8.2-14.7Z'],
        ];
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Gate Operations</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-950">QR Check-In</h1>
            <p class="mt-1 text-sm text-slate-500">Scan a guest ticket or look up a reference before confirming entry.</p>
        </div>
        <div class="rounded-lg bg-white px-3 py-2 text-sm font-bold text-slate-600 shadow-sm ring-1 ring-slate-200">
            {{ now()->format('M d, Y g:i A') }}
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
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

    @if ($errors->has('reference'))
        <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first('reference') }}
        </div>
    @endif

    @if (session('success') || session('status'))
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            {{ session('success') ?: session('status') }}
        </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-slate-950">Camera Scanner</h2>
                    <p id="scannerStatus" class="mt-1 text-sm text-slate-500">Start the scanner and center the QR code inside the frame.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="startScanner" data-no-loader="true" class="inline-flex h-10 items-center gap-2 rounded-lg bg-cyan-700 px-4 text-sm font-bold text-white transition hover:bg-cyan-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10 20 5M20 5h-5M20 5v5M9 14 4 19M4 19h5M4 19v-5M15 14l5 5M20 19h-5M20 19v-5M9 10 4 5M4 5h5M4 5v5"/></svg>
                        Start
                    </button>
                    <button type="button" id="stopScanner" data-no-loader="true" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50" disabled>Stop</button>
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl bg-slate-950 p-3 shadow-inner">
                <div class="mx-auto grid max-w-lg place-items-center rounded-lg border border-white/10 bg-slate-900 p-3">
                    <div id="reader" class="aspect-square w-full max-w-md overflow-hidden rounded-lg bg-slate-900"></div>
                </div>
            </div>

            <div id="scannerError" class="mt-4 hidden rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900"></div>

            <form id="scanForm" method="POST" action="{{ route('admin.checkin.lookup') }}" class="hidden">
                @csrf
                <input id="scanReference" name="reference">
            </form>
        </section>

        <div class="grid content-start gap-6">
            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <h2 class="font-bold text-slate-950">Manual Lookup</h2>
                <p class="mt-1 text-xs text-slate-500">Use ticket reference, booking ID, payment reference, or pasted QR payload.</p>

                <form method="POST" action="{{ route('admin.checkin.lookup') }}" class="mt-4 grid gap-3">
                    @csrf
                    <label class="grid gap-1 text-sm font-bold text-slate-700" for="reference">Reference or payload
                        <textarea id="reference" name="reference" rows="4" class="rounded-lg border-slate-300 text-sm font-normal" placeholder="MAM-YYYYMMDD-XXXXXX, booking ID, payment reference, or JSON payload" required>{{ old('reference') }}</textarea>
                    </label>
                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                        <button type="button" id="clearReference" data-no-loader="true" class="h-10 rounded-lg border border-slate-300 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Clear</button>
                        <button class="h-10 rounded-lg bg-cyan-700 px-4 text-sm font-bold text-white transition hover:bg-cyan-600">Lookup Ticket</button>
                    </div>
                </form>
            </section>

            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-slate-950">Today&apos;s Arrivals</h2>
                        <p class="mt-1 text-xs text-slate-500">Paid bookings scheduled for today.</p>
                    </div>
                    <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-bold text-cyan-700">{{ $todayArrivals->count() }}</span>
                </div>
                <div class="mt-4 grid gap-2">
                    @forelse ($todayArrivals as $booking)
                        <div class="rounded-lg border border-slate-200 px-3 py-2">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-950">{{ $booking->user->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $booking->facility->name }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-2 py-1 text-xs font-bold {{ $booking->ticket?->checked_in_at ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $booking->ticket?->checked_in_at ? 'Done' : 'Waiting' }}
                                </span>
                            </div>
                            <p class="mt-2 text-xs font-semibold text-slate-500">{{ $booking->start_time ? str($booking->start_time)->substr(0, 5) : 'Whole day' }}</p>
                        </div>
                    @empty
                        <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No paid arrivals for today.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <section class="mt-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-slate-950">Recent Check-Ins</h2>
                <p class="mt-1 text-xs text-slate-500">Latest confirmed ticket entries.</p>
            </div>
        </div>
        <div class="mt-4 grid gap-2 lg:grid-cols-5">
            @forelse ($recentCheckIns as $ticket)
                <div class="rounded-lg border border-slate-200 p-3">
                    <p class="truncate text-sm font-bold text-slate-950">{{ $ticket->booking?->user?->name }}</p>
                    <p class="mt-1 truncate text-xs text-slate-500">{{ $ticket->booking?->facility?->name }}</p>
                    <p class="mt-3 font-mono text-xs font-bold text-cyan-700">{{ $ticket->reference_number }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $ticket->checked_in_at?->format('M d, g:i A') }}</p>
                </div>
            @empty
                <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500 lg:col-span-5">No check-ins recorded yet.</p>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(() => {
    const readerId = 'reader';
    const startButton = document.getElementById('startScanner');
    const stopButton = document.getElementById('stopScanner');
    const statusEl = document.getElementById('scannerStatus');
    const errorEl = document.getElementById('scannerError');
    const scanForm = document.getElementById('scanForm');
    const scanReference = document.getElementById('scanReference');
    const referenceInput = document.getElementById('reference');
    const clearReference = document.getElementById('clearReference');
    let scanner = null;
    let isSubmitting = false;

    const setStatus = (message) => {
        statusEl.textContent = message;
    };

    const showError = (message) => {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    };

    const clearError = () => {
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
    };

    const submitScan = async (decodedText) => {
        if (isSubmitting) {
            return;
        }

        isSubmitting = true;
        setStatus('QR detected. Looking up ticket...');
        scanReference.value = decodedText;

        try {
            if (scanner) {
                await scanner.stop();
            }
        } catch (error) {
            // Lookup submission can continue even if camera shutdown fails.
        }

        scanForm.submit();
    };

    const startScanner = async () => {
        clearError();

        if (!window.Html5Qrcode) {
            showError('QR scanner library did not load. Check internet access or use manual lookup.');
            return;
        }

        if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            showError('Camera access requires HTTPS. Use your HTTPS admin URL or localhost.');
            return;
        }

        try {
            scanner = scanner || new Html5Qrcode(readerId);
            const cameras = await Html5Qrcode.getCameras();

            if (!cameras.length) {
                showError('No camera was found. Use manual lookup instead.');
                return;
            }

            const backCamera = cameras.find((camera) => /back|rear|environment/i.test(camera.label));
            const cameraId = (backCamera || cameras[0]).id;

            await scanner.start(
                cameraId,
                { fps: 12, qrbox: { width: 280, height: 280 }, aspectRatio: 1 },
                submitScan,
                () => {}
            );

            startButton.disabled = true;
            stopButton.disabled = false;
            setStatus('Scanner running. Keep the QR code steady inside the square.');
        } catch (error) {
            showError('Unable to start the camera. Allow permission, close other camera apps, or use manual lookup.');
            setStatus('Scanner stopped.');
        }
    };

    const stopScanner = async () => {
        clearError();

        if (!scanner) {
            return;
        }

        try {
            await scanner.stop();
            setStatus('Scanner stopped.');
        } catch (error) {
            showError('Scanner was already stopped.');
        }

        startButton.disabled = false;
        stopButton.disabled = true;
    };

    startButton.addEventListener('click', startScanner);
    stopButton.addEventListener('click', stopScanner);
    clearReference.addEventListener('click', () => {
        referenceInput.value = '';
        referenceInput.focus();
    });
})();
</script>
@endpush
