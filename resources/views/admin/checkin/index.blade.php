@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">QR Check-In</h1>
            <p class="mt-1 text-sm text-slate-500">Scan a paid guest ticket or search by ticket reference, payment reference, or booking ID.</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold">Camera Scanner</h2>
                    <p id="scannerStatus" class="mt-1 text-sm text-slate-500">Camera is ready to start.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" id="startScanner" data-no-loader="true" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-600">Start</button>
                    <button type="button" id="stopScanner" data-no-loader="true" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" disabled>Stop</button>
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-slate-950 p-3">
                <div id="reader" class="mx-auto aspect-square max-w-md overflow-hidden rounded-lg bg-slate-900"></div>
            </div>

            <div id="scannerError" class="mt-4 hidden rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900"></div>

            <form id="scanForm" method="POST" action="{{ route('admin.checkin.lookup') }}" class="hidden">
                @csrf
                <input id="scanReference" name="reference">
            </form>
        </section>

        <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="font-semibold">Manual Lookup</h2>
            <p class="mt-1 text-sm text-slate-500">Use this when the camera is unavailable or the QR code is damaged.</p>

            <form method="POST" action="{{ route('admin.checkin.lookup') }}" class="mt-5 grid gap-3">
                @csrf
                <label class="text-sm font-semibold text-slate-700" for="reference">Reference or payload</label>
                <textarea id="reference" name="reference" rows="4" class="rounded-lg border-gray-300" placeholder="MAM-YYYYMMDD-XXXXXX, payment reference, booking ID, or scanned JSON payload" required>{{ old('reference') }}</textarea>
                <button class="rounded-lg bg-cyan-700 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600">Lookup Ticket</button>
            </form>

            <div class="mt-5 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                Camera scanning requires HTTPS or localhost. Your ngrok URL is HTTPS, so it can use the browser camera after permission is granted.
            </div>
        </section>
    </div>
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
        setStatus('QR code detected. Looking up ticket...');
        scanReference.value = decodedText;

        try {
            if (scanner) {
                await scanner.stop();
            }
        } catch (error) {
            // The form submission below is the important part; stop errors are non-blocking.
        }

        window.showPageLoader?.();
        scanForm.submit();
    };

    const startScanner = async () => {
        clearError();

        if (!window.Html5Qrcode) {
            showError('QR scanner library did not load. Check your internet connection or use manual lookup.');
            return;
        }

        if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            showError('Camera access requires HTTPS. Open the admin panel through your ngrok HTTPS URL.');
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
                { fps: 10, qrbox: { width: 260, height: 260 }, aspectRatio: 1 },
                submitScan,
                () => {}
            );

            startButton.disabled = true;
            stopButton.disabled = false;
            setStatus('Scanner running. Center the ticket QR code inside the square.');
        } catch (error) {
            showError('Unable to start camera scanner. Allow camera permission, close other camera apps, or use manual lookup.');
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
})();
</script>
@endpush
