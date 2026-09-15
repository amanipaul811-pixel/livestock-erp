<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tag — {{ $animal->tag_id }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 24px; background: #f3f4f6; }
        .tag { width: 280px; margin: 0 auto; background: #fff; border: 1px solid #d1d5db; border-radius: 8px; padding: 16px; text-align: center; }
        .tag img { width: 180px; height: 180px; }
        .tag-id { font-size: 20px; font-weight: bold; margin-top: 8px; }
        .meta { color: #6b7280; font-size: 13px; margin-top: 2px; }
        .print-btn { display: block; margin: 16px auto 0; padding: 8px 16px; font-size: 14px; cursor: pointer; }
        @media print {
            body { background: #fff; padding: 0; }
            .print-btn { display: none; }
            .tag { border: none; }
        }
    </style>
</head>
<body>
    <div class="tag">
        <img src="{{ route('animals.qr-code', $animal) }}" alt="QR code for {{ $animal->tag_id }}">
        <div class="tag-id">{{ $animal->tag_id }}</div>
        <div class="meta">{{ $animal->species->name }} &middot; {{ ucfirst($animal->sex) }}</div>
    </div>
    <button class="print-btn" onclick="window.print()">Print</button>
</body>
</html>
