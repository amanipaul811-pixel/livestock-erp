@extends('layouts.app')

@section('title', 'Scan')

@section('content')
<div class="flex items-center gap-3 mb-6">
    @include('partials.back-button')
    <h1 class="text-2xl font-semibold">Scan Animal Tag</h1>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 max-w-md">
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Point the camera at an animal's QR tag to open its record.</p>
    <div id="reader" class="w-full"></div>
    <p id="scan-status" class="text-sm text-gray-500 dark:text-gray-400 mt-3"></p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    (function () {
        const statusEl = document.getElementById('scan-status');

        function onScanSuccess(decodedText) {
            if (decodedText.startsWith(window.location.origin)) {
                statusEl.textContent = 'Tag recognized, opening record...';
                window.location.href = decodedText;
            } else {
                statusEl.textContent = 'This QR code was not issued by this app.';
            }
        }

        const scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 250 }, false);
        scanner.render(onScanSuccess);
    })();
</script>
@endpush
