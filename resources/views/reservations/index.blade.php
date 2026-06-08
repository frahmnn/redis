<x-header/>
<x-bootstrap/>
<main class="bg-light min-vh-100 py-4" style="font-family:'Nunito Sans',sans-serif;">
    <section class="container-fluid mb-4">
        @if (session("success"))
            <div class="alert alert-success">
                {{ session("success") }}
            </div>
        @endif
        @if (session("error"))
            <div class="alert alert-danger">
                <p><strong>{{ session("error") }}</strong></p>
            </div>
        @endif
        @if (session("errors") || $errors->any())
            <div class="alert alert-danger">
                <p><strong>Ada kesalahan:</strong></p>
                <ul>
                    @if (session("errors"))
                        @foreach (session("errors") as $field => $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif
        <div class="d-flex flex-column flex-md-row align-items-md-end gap-2 mb-2">
            <span class="h4 fw-bold mb-0" style="color:#1A4D2E; font-family:'Poppins',sans-serif; font-size:1.6rem;">Pilih Tanggal untuk Membuat Janji Pendampingan</span>
            <span class="h4 fw-bold mb-0" style="color:#4F6F52; font-family:'Poppins',sans-serif; font-size:1.6rem;">{{ $currentMonthName }} {{ $year }}</span>
        </div>
        <style>
        .calendar-table .badge, .calendar-table small {
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
                                    $hasAvailability = $isCurrentMonth && array_key_exists($day, $availableAssistance);
                                    // New logic: canUserBeHelped only if at least one relevant disability has free > 0
                                    $canUserBeHelped = false;
                                    if ($isCurrentMonth && $hasAvailability) {
                                        if (!empty($userDisabilityTypes)) {
                                            foreach ($userDisabilityTypes as $dtype) {
                                                $full = $fullAvailableAssistance[$day][$dtype] ?? 0;
                                                $occupied = $occupiedAssistance[$day][$dtype] ?? 0;
                                                $free = $full - $occupied;
                                                if ($free > 0) {
                                                    $canUserBeHelped = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    if ($day < 1 || $day > $latestDateThisMonth) $bgColor = "#808080";
                                    elseif ($isCurrentMonth && ($day < $today || !$hasAvailability || ($hasAvailability && !$canUserBeHelped))) $bgColor = "#c2c2c2";
                                    else $bgColor = "rgba(0, 0, 0, 0)";
                                @endphp
                                <td
                                    style="position: relative; min-width: 90px; height: 80px; vertical-align: top; @if($bgColor !== 'rgba(0, 0, 0, 0)') background-color: {{ $bgColor }}; @endif"
                                    class="align-top text-center"
                                    originalbg="{{ $bgColor }}"
                                >
                                    @if ($day < 1)
                                        <span class="text-muted">{{ $latestDatePrevMonth + $day }}</span>
                                    @elseif ($day <= $latestDateThisMonth)
                                        <span @if(in_array($day, $sundays) || $isHoliday) style="color: #FF0000;" @endif class="fw-semibold">{{ $day }}</span>
                                        @if($hasAvailability && $canUserBeHelped && $day >= $today)
                                            <a href="{{ route('reservations.make', [sprintf('%02d%02d%04d', $day, $month, $year)]) }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; text-decoration: none; display: block;"></a>
                                        @endif
                                        @if($hasAvailability)
                                            @foreach($availableAssistance[$day] as $speciality => $count)
                                                @php
                                                    $full = $fullAvailableAssistance[$day][$speciality] ?? 0;
                                                    $occupied = $occupiedAssistance[$day][$speciality] ?? 0;
                                                    $free = $full - $occupied;
                                                @endphp
                                                <br><span class="badge bg-info-subtle text-dark mt-1">{{ $speciality }}: {{ $free }} <span class="text-muted">({{ $full }}-{{ $occupied }})</span></span>
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
    </section>
</main>