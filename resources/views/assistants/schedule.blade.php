<x-header/>
<x-bootstrap/>
<main class="bg-light min-vh-100 py-4" style="font-family:'Nunito Sans',sans-serif;">
    <section class="container-fluid mb-4">
        @if (session("success"))
            <div class="alert alert-success">
                {{ session("success") }}
            </div>
        @endif
        @if (session("errors"))
            <div class="alert alert-danger">
                <p><strong>Ada kesalahan dalam pemilihan jadwal:</strong></p>
                <ul>
                    @foreach (session("errors") as $day => $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="d-flex flex-column flex-md-row align-items-md-end gap-2 mb-2">
            <span class="h4 fw-bold mb-0" style="color:#1A4D2E; font-family:'Poppins',sans-serif; font-size:1.6rem;">Edit Jadwal Ketersediaan</span>
            <span class="h4 fw-bold mb-0" style="color:#4F6F52; font-family:'Poppins',sans-serif; font-size:1.6rem;">{{ $currentMonthName }} {{ $year }}</span>
        </div>
        <div class="mb-2">Total jadwal anda bulan ini: <b>{{ count($userSchedules) }}</b></div>
        <style>
        .calendar-day-clickable:hover {
            background-color: #e6f2ff !important;
            box-shadow: 0 0 0 2px #4F6F52 inset;
            cursor: pointer;
            transition: background 0.15s;
        }
        .calendar-table .badge, .calendar-table small {
            word-break: break-word;
            white-space: normal;
            max-width: 100%;
            display: inline-block;
        }
        </style>
        <form action="{{ route('assistants.editSchedule') }}" method="post" autocomplete="off">@csrf
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
                                        if ($isCurrentMonth && $day < $today) $bgColor = "#c2c2c2";
                                        elseif ($day < 1 || $day > $latestDateThisMonth) $bgColor = "#808080";
                                        else $bgColor = "rgba(0, 0, 0, 0)";
                                    @endphp
                                    <td
                                        @if ($day >= 1 && $day <= $latestDateThisMonth)
                                            class="calendar-day-clickable position-relative align-top text-center"
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
                                            @if($isCurrentMonth && $day >= $today)
                                                <input type="checkbox" name="{{ $day }}" @if(in_array($day, $userSchedules)) checked @endif style="opacity: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0; cursor: pointer;">
                                            @endif
                                        @else
                                            <span class="text-muted">{{ $day - $latestDateThisMonth }}</span>
                                        @endif
                                        @if($isHoliday)
                                            <br><small class="badge bg-danger-subtle text-danger mt-1">{{ $holidays[$day] }}</small>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <input type="submit" value="Perbarui Jadwal Saya" id="submitButton" class="btn btn-success">
        </form>
    </section>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function(){
        const
            checkboxes = document.querySelector("tbody").querySelectorAll("input[type='checkbox']"),
            submitButton = document.getElementById("submitButton"),
            initialState = {};
        function checkForChanges(){
            let hasChanges = false;
            checkboxes.forEach(function(checkbox){
                if (checkbox.checked !== initialState[checkbox.name]) hasChanges = true;});
            submitButton.disabled = !hasChanges;
        }
        checkboxes.forEach(function(checkbox){
            initialState[checkbox.name] = checkbox.checked;
            if (checkbox.checked) checkbox.closest("td").style.backgroundColor = "#008000";
            checkbox.addEventListener("change", function(){
                const cell = this.closest("td");
                if (this.checked) cell.style.backgroundColor = "#008000";
                else {
                    const originalBg = cell.getAttribute("originalbg");
                    if (originalBg && originalBg != "rgba(0, 0, 0, 0)") cell.style.backgroundColor = originalBg;
                    else cell.style.backgroundColor = ""; 
                }
                checkForChanges();
            });
        });
        checkForChanges();
    });
</script>