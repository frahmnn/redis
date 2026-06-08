<x-header/>
<x-jquery/>
<x-bootstrap/>
<x-datatables/>
<main class="bg-light min-vh-100 py-4" style="font-family:'Nunito Sans',sans-serif;">
    <section class="container-fluid mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-3">
            <div class="d-flex flex-column flex-md-row align-items-md-end gap-2">
                <span class="h4 fw-bold mb-0" style="color:#1A4D2E; font-family:'Poppins',sans-serif; font-size:1.6rem;">Kalender Pendampingan</span>
                <span class="h4 fw-bold mb-0" style="color:#4F6F52; font-family:'Poppins',sans-serif; font-size:1.6rem;">{{ $currentMonthName }} {{ $year }}</span>
            </div>
            <a href="{{ route('assistants.schedule') }}" class="btn btn-primary">Ubah Jadwal Ketersediaan</a>
        </div>
        <style>
        .calendar-day-clickable:hover {
            background-color: #e6f2ff !important;
            box-shadow: 0 0 0 2px #4F6F52 inset;
            cursor: pointer;
            transition: background 0.15s;
 }
 .calendar-table .badge {
     word-break: break-word;
     white-space: normal;
     max-width: 100%;
     display: inline-block;
        }
        </style>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover calendar-table bg-white mb-0" style="min-width:700px; table-layout:fixed;">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Minggu</th>
                        <th scope="col">Senin</th>
                        <th scope="col">Selasa</th>
                        <th scope="col">Rabu</th>
                        <th scope="col">Kamis</th>
                        <th scope="col">Jumat</th>
                        <th scope="col">Sabtu</th>
                    </tr>
                </thead>
                <tbody>
                    @php $day = 1 - $firstDayOfMonth; @endphp
                    @for ($w = 0; $w < ceil(($latestDateThisMonth + $firstDayOfMonth) / 7); $w++)
                        <tr>
                            @for ($d = 0; $d < 7; $d++, $day++)
                                @php
                                    $isHoliday = array_key_exists($day, $holidays);
                                    $isCurrentMonth = $day >= 1 && $day <= $latestDateThisMonth;
                                    $hasPendingReservation = array_key_exists($day, $pendingReservations) && count($pendingReservations[$day]) > 0;
                                    if ($isCurrentMonth && $day < $today) $bgColor = "#c2c2c2";
                                    elseif ($day < 1 || $day > $latestDateThisMonth) $bgColor = "#808080";
                                    elseif ($hasPendingReservation) $bgColor = "#ee90daff";
                                    else $bgColor = "rgba(0, 0, 0, 0)";
                                @endphp
                                <td
                                    @if ($day >= 1 && $day <= $latestDateThisMonth)
                                        class="calendar-day-clickable position-relative align-top text-center"
                                        style="cursor:pointer; min-width: 90px; height: 80px; vertical-align: top; @if($bgColor !== 'rgba(0, 0, 0, 0)') background-color: {{ $bgColor }}; @endif"
                                        onclick="searchByDate('{{ sprintf('%02d', $day) }}')"
                                    @else
                                        class="align-top text-center"
                                        style="min-width: 90px; height: 80px; vertical-align: top; @if($bgColor !== 'rgba(0, 0, 0, 0)') background-color: {{ $bgColor }}; @endif"
                                    @endif
                                    originalbg="{{ $bgColor }}"
                                >
                                    @if ($day < 1)
                                        <span class="text-muted">{{ $latestDatePrevMonth + $day }}</span>
                                    @elseif ($day <= $latestDateThisMonth)
                                        <span @if(in_array($day, $sundays) || $isHoliday) style="color: #FF0000;" @endif class="fw-semibold">{{ $day }}</span>
                                        @if(array_key_exists($day, $availableAssistance))
                                            @foreach($availableAssistance[$day] as $speciality => $count)
                                                @php
                                                    $full = $fullAvailableAssistance[$day][$speciality] ?? 0;
                                                    $occupied = $occupiedAssistance[$day][$speciality] ?? 0;
                                                    $free = $full - $occupied;
                                                @endphp
                                                <br>
                                                @if(in_array($day, $userScheduledDays ?? []) && in_array($speciality, $userSpecialties ?? []))
                                                    <span class="badge bg-primary text-white mt-1">{{ $speciality }}: {{ $free }} <span class="text-light">({{ $full }}-{{ $occupied }})</span></span>
                                                @else
                                                    <span class="badge bg-info-subtle text-dark mt-1">{{ $speciality }}: {{ $free }} <span class="text-muted">({{ $full }}-{{ $occupied }})</span></span>
                                                @endif
                                            @endforeach
                                        @endif
                                    @else
                                        <span class="text-muted">{{ $day - $latestDateThisMonth }}</span>
                                    @endif
                                    @if($isHoliday)
                                        <br><span class="badge bg-danger-subtle text-danger mt-1">{{ $holidays[$day] }}</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <h3 class="h5 fw-bold mt-5 mb-3" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Pengajuan Pendampingan - {{ $currentMonthName }} {{ $year }}</h3>
        <div class="table-responsive mb-4">
            <table id="datatable" class="table table-bordered table-hover align-middle bg-white mb-0" style="font-size:0.98rem;">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pemohon</th>
                        <th>Tempat</th>
                        <th>Tipe</th>
                        <th>Waktu Pengajuan</th>
                        <th>Prasyarat</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach ($pendingReservations as $day => $reservations)
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>
                                    <p style="display: none;">TANGGAL:{{ sprintf('%02d', $day) }}</p>
                                    {{ sprintf('%02d', $day) }}-{{ sprintf('%02d', date('n')) }}-{{ $year }}
                                </td>
                                <td>{{ $reservation['name'] }}</td>
                                <td>{{ $reservation['place'] }}</td>
                                <td>{{ $reservation['type'] }}</td>
                                <td>{{ $reservation['created_at']->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span style="color: {{ $reservation['color'] }}; font-weight: bold;">
                                        {{ $reservation['met_requirements'] }} of {{ $reservation['total_requirements'] }}
                                    </span>
                                    <br>
                                    <small>
                                        @if(count($reservation['disabilities']) > 0)
                                            @php
                                                $unmatchedDisabilities = array_diff($reservation['disabilities'], $reservation['specialty_matches']);
                                            @endphp
                                            @foreach($reservation['specialty_matches'] as $matchedDisability)
                                                ✓ Disabilitas: {{ $matchedDisability }}<br>
                                            @endforeach
                                            @foreach($unmatchedDisabilities as $unmatchedDisability)
                                                ✗ Disabilitas: {{ $unmatchedDisability }}<br>
                                            @endforeach
                                        @endif
                                        @if($reservation['is_scheduled_this_day'])
                                            ✓ Jadwal cocok
                                        @else
                                            ✗ Jadwal tidak cocok
                                        @endif
                                        <br>
                                        @if($reservation['role_match'])
                                            ✓ Divisi cocok ({{ $reservation['assistant_division'] ?? 'Tidak ada divisi' }} → {{ $reservation['type'] }})
                                        @else
                                            ✗ Divisi tidak cocok ({{ $reservation['assistant_division'] ?? 'Tidak ada divisi' }} → {{ $reservation['type'] }})
                                        @endif
                                    </small>
                                    <a href="{{ route('assistants.request', $reservation['id']) }}" style="display: block; margin-top: 5px;">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</main>
<script>
    let table = new DataTable("#datatable", {
        columnDefs:[{
            type:"num",
            targets: 0
        }],
        search: {
            regex: true
        }
    });
    function searchByDate(day) {
        // Use OR search for multiple days
        let current = table.search().trim();
        let parts = current.split('|').map(s => s.trim()).filter(Boolean);
        // Remove any (?=.*TANGGAL:...) style regex from previous version
        parts = parts.filter(s => !/^\(\?=\.\*TANGGAL:\d{2}\)$/.test(s));
        // Remove duplicate day
        if (!parts.includes(`TANGGAL:${day}`)) {
            parts.push(`TANGGAL:${day}`);
        }
        table.search(parts.join(' | '), true, false).draw();
        document.getElementById('datatable').scrollIntoView({ 
            behavior: 'smooth' 
        });
    }
</script>