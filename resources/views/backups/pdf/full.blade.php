<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #16233d; }
        .cover { text-align: center; padding-top: 220px; page-break-after: always; }
        .cover h1 { font-size: 28px; margin-bottom: 4px; }
        .cover p { color: #5b6b85; }
        .toc { page-break-after: always; }
        .toc h2 { border-bottom: 2px solid #2f6fed; padding-bottom: 6px; }
        .toc ol li { margin-bottom: 6px; font-size: 13px; }
        .summary-grid { display: table; width: 100%; margin-bottom: 20px; }
        .summary-cell { display: table-cell; width: 25%; padding: 10px; border: 1px solid #dfe6f2; }
        .summary-cell strong { display: block; font-size: 16px; color: #2f6fed; }
        section { page-break-after: always; }
        section:last-child { page-break-after: auto; }
        h2 { color: #2f6fed; border-bottom: 1px solid #dfe6f2; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #dfe6f2; padding: 5px 7px; text-align: left; font-size: 10px; }
        th { background-color: #eef4ff; }
        .muted { color: #8a97ad; font-size: 9px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="cover">
        <h1>Zedan</h1>
        <p>Dental Clinic Management System</p>
        <p>Full Data Backup</p>
        <p>Generated: {{ $generatedAt->format('Y-m-d H:i') }}</p>
    </div>

    <div class="toc">
        <h2>Table of Contents</h2>
        <div class="summary-grid">
            @foreach($modules as $title => $module)
                <div class="summary-cell">
                    <strong>{{ count($module['rows']) }}</strong>
                    {{ $title }}
                </div>
            @endforeach
        </div>
        <ol>
            @foreach($modules as $title => $module)
                <li>{{ $title }} ({{ count($module['rows']) }} records)</li>
            @endforeach
        </ol>
    </div>

    @foreach($modules as $title => $module)
        <section>
            <h2>{{ $title }}</h2>
            @if(count($module['rows']) === 0)
                <p class="muted">No records.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            @foreach($module['headings'] as $heading)
                                <th>{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($module['rows'] as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @endforeach

    <p class="muted">Generated automatically by Zedan. This document contains confidential patient data - handle per clinic data-protection policy.</p>
</body>
</html>
