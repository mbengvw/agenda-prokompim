<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Agenda Kegiatan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #000;
            margin: 30px;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-item {
            margin-bottom: 15px;
        }

        .time-row {
            font-weight: bold;
            color: #e50000;
            /* Red color matching the screenshot */
        }

        .title-row {
            font-weight: bold;
            margin-top: 3px;
            margin-bottom: 3px;
            font-size: 15px;
        }

        .detail-table {
            border-collapse: collapse;
            width: 100%;
        }

        .detail-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .label-col {
            width: 120px;
        }

        .colon-col {
            width: 15px;
            text-align: center;
        }

        .separator {
            border: 0;
            border-top: 1px solid #777;
            /* Darker border matching screenshot */
            margin: 15px 0;
        }

        .footer {
            position: fixed;
            bottom: -15px;
            left: 0px;
            right: 0px;
            height: 30px;
            font-size: 11px;
            font-style: italic;
            color: #555;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>AGENDA KEGIATAN {{ strtoupper($leader->position) }}</h1>
        <p>{{ $dateFormatted }}</p>
        <hr>
    </div>

    @if ($dihadiri->isEmpty() && $didisposisi->isEmpty())
        <p>Tidak ada agenda kegiatan pada tanggal ini.</p>
    @else
        @if ($dihadiri->isNotEmpty())
            <div class="activity-list">
                @foreach ($dihadiri as $index => $activity)
                    @php
                        if ($activity->is_tentative || !$activity->start_time) {
                            $timeDisplay = 'Tentatif';
                        } else {
                            $start = \Carbon\Carbon::parse($activity->start_time);
                            $end = $activity->end_time ? \Carbon\Carbon::parse($activity->end_time) : null;
                            $duration = $end ? $start->diffInMinutes($end) : null;

                            $timeString = $start->format('H.i');
                            if ($end) {
                                $timeString .= '-' . $end->format('H.i');
                            }
                            $timeString .= ' WIB';
                            if ($duration) {
                                $timeString .= " ({$duration}')";
                            }
                            $timeDisplay = 'Pkl. ' . $timeString;
                        }
                    @endphp
                    <div class="activity-item">
                        <div class="time-row">({{ $index + 1 }}). {{ $timeDisplay }}</div>
                        <div class="title-row">{{ $activity->title }}</div>
                        <table class="detail-table">
                            <tr>
                                <td class="label-col">Lokasi</td>
                                <td class="colon-col">:</td>
                                <td>{{ $activity->location ? $activity->location->name : $activity->location_text }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">Penyelenggara</td>
                                <td class="colon-col">:</td>
                                <td>{{ $activity->organization ? $activity->organization->name : $activity->organizer_text }}
                                </td>
                            </tr>
                            @if ($activity->dress_code)
                                <tr>
                                    <td class="label-col">Pakaian</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $activity->dress_code }}</td>
                                </tr>
                            @endif
                            @if ($activity->companions->count() > 0)
                                <tr>
                                    <td class="label-col">Pendamping</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $activity->companions->pluck('position')->map(fn($p) => \Illuminate\Support\Str::title($p))->join(', ') }}
                                    </td>
                                </tr>
                            @endif
                            @if ($activity->protocolOfficer)
                                <tr>
                                    <td class="label-col">PIC Protokol</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $activity->protocolOfficer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">No.Hp</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $activity->protocolOfficer->phone ?? '-' }}</td>
                                </tr>
                            @endif
                            @if ($activity->contact_person_name)
                                <tr>
                                    <td class="label-col">Narahubung</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $activity->contact_person_name }}</td>
                                </tr>
                                @if ($activity->contact_person_phone)
                                    <tr>
                                        <td class="label-col">No.Hp</td>
                                        <td class="colon-col">:</td>
                                        <td>{{ $activity->contact_person_phone }}</td>
                                    </tr>
                                @endif
                            @endif
                        </table>
                    </div>

                    @if (!$loop->last)
                        <hr class="separator">
                    @endif
                @endforeach
            </div>
        @endif

        @if ($didisposisi->isNotEmpty())
            <h2
                style="font-size: 15px; margin-bottom: 10px; border-bottom: 2px solid #ccc; padding-bottom: 5px; margin-top: 30px;">
                DISPOSISI:</h2>
            <div class="activity-list">
                @foreach ($didisposisi as $index => $activity)
                    @php
                        if ($activity->is_tentative || !$activity->start_time) {
                            $timeDisplay = 'Tentatif';
                        } else {
                            $start = \Carbon\Carbon::parse($activity->start_time);
                            $end = $activity->end_time ? \Carbon\Carbon::parse($activity->end_time) : null;
                            $duration = $end ? $start->diffInMinutes($end) : null;

                            $timeString = $start->format('H.i');
                            if ($end) {
                                $timeString .= '-' . $end->format('H.i');
                            }
                            $timeString .= ' WIB';
                            if ($duration) {
                                $timeString .= " ({$duration}')";
                            }
                            $timeDisplay = 'Pkl. ' . $timeString;
                        }
                    @endphp
                    <div class="activity-item">
                        <div class="time-row">({{ $index + 1 }}). {{ $timeDisplay }}</div>
                        <div class="title-row">{{ $activity->title }}</div>
                        <table class="detail-table">
                            <tr>
                                <td class="label-col" style="color: #e50000; font-weight: bold;">Disposisi: </td>
                                <td class="colon-col">:</td>
                                <td style="font-weight: bold;">
                                    {{ $activity->leader ? $activity->leader->position . ' (' . $activity->leader->position . ')' : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">Lokasi</td>
                                <td class="colon-col">:</td>
                                <td>{{ $activity->location ? $activity->location->name : $activity->location_text }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">Penyelenggara</td>
                                <td class="colon-col">:</td>
                                <td>{{ $activity->organization ? $activity->organization->name : $activity->organizer_text }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    @if (!$loop->last)
                        <hr class="separator">
                    @endif
                @endforeach
            </div>
        @endif

    @endif

    @php
        $allActivities = $dihadiri->concat($didisposisi);
        $adcNames = $allActivities->pluck('adc')->filter()->unique()->join(', ');

        $posLower = strtolower($leader->position ?? '');
        if (str_contains($posLower, 'wakil') || str_contains($posLower, 'wabup')) {
            $leaderTitle = 'Wabup';
        } elseif (str_contains($posLower, 'bupati')) {
            $leaderTitle = 'Bupati';
        } elseif (str_contains($posLower, 'sekda')) {
            $leaderTitle = 'Sekda';
        } else {
            $leaderTitle = ucwords($posLower);
        }
    @endphp

    <div class="closing-section" style="margin-top: 25px; page-break-inside: avoid; font-size: 13px; line-height: 1.5;">
        <p style="margin-bottom: 15px;">Adc: {{ $adcNames ?: '-' }}</p>

        <p style="margin-bottom: 15px;">
            Demikian Disampaikan,<br>
            Hatur
        </p>

        <p>
            Salam,<br>
            <strong>Tim Sekpri {{ $leaderTitle }}</strong>
        </p>
    </div>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d M Y, H:i') }} WIB | Dicetak dari sistem LEAD-IT
        Prokompim Kuningan
    </div>

</body>

</html>
