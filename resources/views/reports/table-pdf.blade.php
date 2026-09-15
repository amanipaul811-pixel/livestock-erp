<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin-bottom: 0; }
        p.subtitle { color: #6b7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 5px 8px; text-align: left; }
        th { background-color: #f9fafb; }
        td.num, th.num { text-align: right; }
        .summary-table td { border: none; padding: 3px 12px 3px 0; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="subtitle">{{ $subtitle }}</p>

    @if (! empty($summary))
        <table class="summary-table">
            @foreach (array_chunk($summary, 2, true) as $pair)
                <tr>
                    @foreach ($pair as $label => $value)
                        <td>{{ $label }}</td><td class="num">{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    @endif

    <table>
        <thead>
            <tr>@foreach ($headings as $heading)<th>{{ $heading }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
            @empty
                <tr><td colspan="{{ count($headings) }}">No records found for this filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
