
<x-header/>
<x-bootstrap/>
<x-jquery/>
<x-datatables/>

<main class="bg-light min-vh-100 py-4" style="font-family:'Nunito Sans',sans-serif;">
    <section class="container-fluid mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-end gap-2 mb-2">
            <span class="h4 fw-bold mb-0" style="color:#1A4D2E; font-family:'Poppins',sans-serif; font-size:1.6rem;">Jadwal Pendamping</span>
            <span class="h4 fw-bold mb-0" style="color:#4F6F52; font-family:'Poppins',sans-serif; font-size:1.6rem;">{{ $currentMonthName }} {{ $year }}</span>
        </div>
        <form id="monthForm" method="get" action="" class="d-flex align-items-center gap-2 mb-3">
            <label for="monthPicker" class="form-label mb-0 fw-semibold">Pilih Bulan:</label>
            <input type="month" id="monthPicker" class="form-control form-control-sm" style="max-width:160px;" value="{{ $year }}-{{ sprintf('%02d', $month) }}" max="{{ date('Y-m') }}">
        </form>
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
                                    if ($isCurrentMonth && $day < $today && $month == date('n') && $year == date('Y')) $bgColor = "#c2c2c2";
                                    elseif ($day < 1 || $day > $latestDateThisMonth) $bgColor = "#808080";
                                    else $bgColor = "rgba(0, 0, 0, 0)";
                                @endphp
                                <td
                                    @if ($day >= 1 && $day <= $latestDateThisMonth)
                                        class="calendar-day-clickable position-relative align-top text-center"
                                        data-date="{{ $year }}-{{ sprintf('%02d', $month) }}-{{ sprintf('%02d', $day) }}"
                                        style="cursor:pointer; min-width: 90px; height: 80px; vertical-align: top; @if($bgColor !== 'rgba(0, 0, 0, 0)') background-color: {{ $bgColor }}; @endif"
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

        <h3 class="h5 fw-bold mt-5 mb-3" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Daftar Pendamping & Jumlah Jadwal Bulan Ini</h3>
        <div class="table-responsive mb-4">
            <table id="assistantSchedulesTable" class="table table-bordered table-hover align-middle bg-white mb-0" style="font-size:0.98rem;">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Pendamping</th>
                        <th>Spesialisasi</th>
                        <th>Jumlah Jadwal Bulan Ini</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assistantSchedules as $i => $as)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $as['name'] }}
                                <p style="display:none;">
                                    {{-- Searchable keyword dump: all assistant's available dates (DD-MM-YYYY) --}}
                                    @php
                                        // Find this assistant's available dates for this month
                                        $user = null;
                                        foreach($assistants ?? [] as $a) {
                                            if (($a->identity->name ?? null) === $as['name']) { $user = $a; break; }
                                        }
                                        $dates = [];
                                        if ($user) {
                                            foreach($user->schedules()->where('month', sprintf('%02d', $month).$year)->get() as $sched) {
                                                $dates[] = sprintf('%02d', $sched->day).'-'.sprintf('%02d', $month).'-'.$year;
                                            }
                                        }
                                    @endphp
                                    {{ implode(' ', $dates) }}
                                </p>
                            </td>
                            <td>
                                @if(!empty($as['specializations']))
                                    <ul class="mb-0 ps-3">
                                    @foreach((array)$as['specializations'] as $spec)
                                        <li>{{ $spec }}</li>
                                    @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $as['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h3 class="h5 fw-bold mt-5 mb-3" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Janji Pendampingan Bulan Ini</h3>
        <div class="table-responsive mb-4">
                <table id="nonPendingTable" class="table table-bordered table-hover align-middle bg-white mb-0" style="font-size:0.98rem;">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pemohon</th>
                        <th>Tempat</th>
                        <th>Tipe</th>
                        <th>Pendamping</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $row = 0; @endphp
                    @foreach ($nonPendingReservations as $reservation)
                        @php $row++; @endphp
                        <tr>
                            <td>{{ $row }}</td>
                            <td>{{ substr($reservation->dateid, 0, 2) }}-{{ substr($reservation->dateid, 2, 2) }}-{{ substr($reservation->dateid, 4, 4) }}</td>
                            <td>{{ $reservation->user->identity->name ?? 'N/A' }}</td>
                            <td>{{ $reservation->place }}</td>
                            <td>{{ $reservation->type }}</td>
                            <td>{{ $reservation->assistantUser->identity->name ?? '-' }}</td>
                            <td>
                                @php
                                    // Determine if expired: status Menunggu and dateid < today
                                    $isExpired = false;
                                    if ($reservation->status === 'Menunggu') {
                                        $date = DateTime::createFromFormat('dmY', $reservation->dateid);
                                        if ($date && $date < new DateTime(date('Y-m-d'))) {
                                            $isExpired = true;
                                        }
                                    }
                                @endphp
                                {{ $isExpired ? 'Kedaluwarsa' : $reservation->status }}
                            </td>
                            <td>
                                <a href="{{ route('admins.reservation', $reservation->id) }}">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
    let assistantSchedulesTable = new DataTable("#assistantSchedulesTable", {
        search: { regex: true },
        columnDefs:[{
            type:"num",
            targets: 0
        }]
    });

    let nonPendingTable = new DataTable("#nonPendingTable", {
        search: { regex: true },
        columnDefs:[{
            type:"num",
            targets: 0
        }]
    });


    // Helper: format date as YYYY-MM-DD to DD-MM-YYYY
    function formatDateForSearch(dateStr) {
        // Input: YYYY-MM-DD, Output: DD-MM-YYYY
        const [y, m, d] = dateStr.split('-');
        return `${d}-${m}-${y}`;
    }

    // Helper: get current search value and append new regex as OR (|)
    function appendDateSearch(table, dateStr) {
        let current = table.search();
        current = current.trim();
        // Remove any previous (?=.*...) and split by |
        let parts = current.split('|').map(s => s.trim()).filter(Boolean);
        // Remove any (?=.*...) style regex from previous version
        parts = parts.filter(s => !/^\(\?=\.\*.+\)$/.test(s));
        // Remove duplicate dateStr
        if (!parts.includes(dateStr)) {
            parts.push(dateStr);
        }
        return parts.join(' | ');
    }

    // Make calendar days clickable and filter both DataTables by date (DD-MM-YYYY)
    document.querySelectorAll('.calendar-day-clickable').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const date = this.dataset.date;
            if (!date) return;
            // Format for search: DD-MM-YYYY
            const [y, m, d] = date.split('-');
            const formatted = `${d}-${m}-${y}`;
            // For assistantSchedulesTable: search for the date string (DD-MM-YYYY) in the hidden <p>
            let asTable = assistantSchedulesTable;
            let asSearch = appendDateSearch(asTable, formatted);
            asTable.search(asSearch, true, false).draw();
            // For nonPendingTable: search for the formatted date (DD-MM-YYYY) in the Tanggal column
            let npTable = nonPendingTable;
            let npSearch = appendDateSearch(npTable, formatted);
            npTable.search(npSearch, true, false).draw();
        });
    });

    document.getElementById('monthPicker').addEventListener('change', function() {
        const val = this.value;
        if (!val) return;
        const [year, month] = val.split('-');
        const monthid = `${month}${year}`;
        window.location.href = "{{ route('admins.schedules', ['monthid' => 'MONTHID']) }}".replace('MONTHID', monthid);
    });
</script>