@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold">QR Check-In</h1>
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-lg bg-white p-6 shadow-sm">
            <div id="reader" class="mx-auto aspect-square max-w-sm overflow-hidden rounded-lg border"></div>
            <form id="scanForm" method="POST" action="{{ route('admin.checkin.lookup') }}" class="hidden">@csrf <input id="scanReference" name="reference"></form>
        </section>
        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="font-semibold">Manual Reference Lookup</h2>
            <form method="POST" action="{{ route('admin.checkin.lookup') }}" class="mt-4 grid gap-3">
                @csrf
                <input name="reference" class="rounded-md border-gray-300" placeholder="MAM-YYYYMMDD-XXXXXX or scanned payload" required>
                <button class="rounded-md bg-cyan-700 px-4 py-2 text-white">Lookup Ticket</button>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const submitScan = (decodedText) => {
    document.getElementById('scanReference').value = decodedText;
    document.getElementById('scanForm').submit();
};
new Html5QrcodeScanner('reader', { fps: 10, qrbox: { width: 240, height: 240 } }).render(submitScan);
</script>
@endpush
