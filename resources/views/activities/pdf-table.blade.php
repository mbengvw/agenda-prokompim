<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tabel Agenda Kegiatan</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #111;
            margin: 20px 25px;
        }

        .header { margin-bottom: 16px; }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header p { margin: 0; font-size: 11px; color: #444; }

        hr.divider {
            border: 0;
            border-top: 2px solid #1a3a6b;
            margin: 10px 0 14px 0;
        }

        table.agenda-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        table.agenda-table thead tr {
            background-color: #1a3a6b;
            color: #fff;
        }

        table.agenda-table thead th {
            padding: 7px 8px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.3px;
            border: 1px solid #14306b;
        }

        table.agenda-table tbody tr:nth-child(even) { background-color: #f0f4fc; }
        table.agenda-table tbody tr:nth-child(odd)  { background-color: #fff; }

        table.agenda-table tbody td {
            padding: 6px 8px;
            vertical-align: top;
            border: 1px solid #cdd6e8;
        }

        .no-col     { width: 3%; text-align: center; }
        .time-col   { width: 10%; }
        .title-col  { width: 23%; }
        .loc-col    { width: 15%; }
        .org-col    { width: 14%; }
        .leader-col { width: 11%; }
        .dress-col  { width: 9%; }
        .cp-col     { width: 15%; }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-approved  { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-submitted { background: #fef9c3; color: #713f12; border: 1px solid #fbbf24; }
        .badge-draft     { background: #f3f4f6; color: #374151; border: 1px solid #9ca3af; }
        .badge-revision  { background: #ffedd5; color: #9a3412; border: 1px solid #fdba74; }
        .badge-rejected  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .time-main  { font-weight: bold; color: #b91c1c; }
        .time-dur   { font-size: 9px; color: #555; }
        .tentative  { font-style: italic; color: #777; }

        .empty-row td {
            text-align: center;
            padding: 30px;
            color: #888;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            font-size: 9.5px;
            font-style: italic;
            color: #666;
            text-align: center;
            border-top: 1px dashed #bbb;
            padding-top: 4px;
        }

        .summary-row { margin-top: 12px; font-size: 10px; color: #444; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Tabel Agenda Kegiatan &mdash; {{ strtoupper($leader->position) }}</h1>
        <p>{{ $dateFormatted }}</p>
        <hr class="divider">
    </div>

    <table class="agenda-table">
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th class="time-col">Waktu</th>
                <th class="title-col">Kegiatan</th>
                <th class="loc-col">Lokasi</th>
                <th class="org-col">Penyelenggara</th>
                <th class="leader-col">Agenda &amp; Status</th>
                <th class="dress-col">Dress Code</th>
                <th class="cp-col">Narahubung / Protokol</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $index => $activity)
                @php
                    if ($activity->is_tentative || ! $activity->start_time) {
                        $timeHtml = '<span class="tentative">Tentatif</span>';
                    } else {
                        $start = \Carbon\Carbon::parse($activity->start_time);
                        $end   = $activity->end_time ? \Carbon\Carbon::parse($activity->end_time) : null;
                        $dur   = $end ? $start->diffInMinutes($end) : null;

                        $timeStr  = $start->format('H.i');
                        $timeStr .= $end ? '-' . $end->format('H.i') . ' WIB' : ' WIB';
                        $durStr   = $dur ? " ({$dur}')" : '';

                        $timeHtml = '<span class="time-main">' . $timeStr . '</span>'
                                  . ($durStr ? '<br><span class="time-dur">' . $durStr . '</span>' : '');
                    }

                    $statusBadge = match($activity->status) {
                        'approved'  => '<span class="badge badge-approved">Approved</span>',
                        'submitted' => '<span class="badge badge-submitted">Submitted</span>',
                        'revision'  => '<span class="badge badge-revision">Revision</span>',
                        'rejected'  => '<span class="badge badge-rejected">Rejected</span>',
                        default     => '<span class="badge badge-draft">Draft</span>',
                    };

                    $location  = $activity->location_id
                                   ? ($activity->location?->name ?? '-')
                                   : ($activity->location_text ?: '-');

                    $organizer = $activity->organization_id
                                   ? ($activity->organization?->name ?? '-')
                                   : ($activity->organizer_text ?: '-');

                    $dressCode = $activity->dress_code ?: '-';

                    $isDisposed = $activity->is_disposition
                               && $activity->leader_id !== $activity->original_leader_id;

                    $leaderLabel = $activity->leader?->position ?? '-';
                @endphp
                <tr>
                    <td class="no-col">{{ $index + 1 }}</td>
                    <td class="time-col">{!! $timeHtml !!}</td>
                    <td class="title-col">{{ $activity->title }}</td>
                    <td class="loc-col">{{ $location }}</td>
                    <td class="org-col">{{ $organizer }}</td>
                    <td class="leader-col">
                        <div>{{ $leaderLabel }}</div>
                        @if ($isDisposed)
                            <div style="font-size:9px; color:#6b21a8; margin-top:2px;">&#10230; Disposisi</div>
                        @endif
                        <div style="margin-top:3px;">{!! $statusBadge !!}</div>
                    </td>
                    <td class="dress-col">{{ $dressCode }}</td>
                    <td class="cp-col">
                        @if ($activity->contact_person_name)
                            <div><strong>NH:</strong> {{ $activity->contact_person_name }}</div>
                            @if ($activity->contact_person_phone)
                                <div>{{ $activity->contact_person_phone }}</div>
                            @endif
                        @endif
                        @if ($activity->protocolOfficer)
                            <div style="margin-top:2px;"><strong>Prot:</strong> {{ $activity->protocolOfficer->name }}</div>
                            @if ($activity->protocolOfficer->phone)
                                <div>{{ $activity->protocolOfficer->phone }}</div>
                            @endif
                        @endif
                        @if (! $activity->contact_person_name && ! $activity->protocolOfficer)
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="8">Tidak ada agenda kegiatan pada tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-row">
        Total agenda: <strong>{{ $activities->count() }}</strong>
    </div>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d M Y, H:i') }} WIB &nbsp;|&nbsp;
        Dicetak dari sistem LEAD-IT Prokompim Kuningan &nbsp;|&nbsp;
        Data: semua agenda (tanpa filter kehadiran)
    </div>

</body>
</html>
