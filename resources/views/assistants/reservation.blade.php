
@php
$reservationDateInt = (int)($reservation->dateid);
$todayInt = (int)($today);
@endphp
<x-header/>
<x-bootstrap/>
<div class="container my-4" style="max-width: 700px; font-family: 'Nunito Sans', Arial, sans-serif;">
    @if (session("success"))
        <div class="alert alert-success">{{ session("success") }}</div>
    @endif
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h3 class="mb-3" style="color:#4F6F52; font-family:'Poppins',sans-serif;">Status Janji Pendampingan</h3>
            @if($reservation->status === 'Menunggu')
                @if($reservationDateInt < $todayInt)
                    <span class="badge bg-secondary mb-2">Kedaluwarsa</span>
                    <div class="alert alert-info mt-2">
                        Jika lupa melakukan konfirmasi selesai, segera lakukan konfirmasi selesai.<br>
                    </div>
                @else
                    <span class="badge bg-success mb-2">Menunggu Hari Pendampingan</span>
                    <div class="alert alert-info mt-2">
                        Mohon segera hubungi pemohon untuk konfirmasi lebih lanjut.
                    </div>
                @endif
            @else
                <span class="badge bg-secondary mb-2">{{$reservation->status}}</span>
            @endif
        </div>
    </div>

    @if($reservation->status === 'Menunggu')
        @if($todayInt <= $reservationDateInt)
        <div class="mb-3">
            <form id="cancelForm" action="{{ route('users.cancelReservation', $reservation->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <input type="hidden" name="reason" id="cancelReason">
                <button type="button" class="btn btn-danger" onclick="confirmCancel()" style="margin: 10px 0;">
                    Batalkan janji pendampingan
                </button>
            </form>
        </div>
        @endif

        @if($reservationDateInt <= $todayInt)
        <div class="mb-3">
            <form id="completeForm" action="{{ route('assistants.completeReservation', $reservation->id) }}" method="POST" style="display: inline;">
                @csrf
                <button type="button" class="btn btn-success" onclick="confirmComplete()" style="margin: 10px 0;">
                    Pendampingan Selesai
                </button>
            </form>

            <form id="completeWithReplacementForm" action="{{ route('assistants.completeReservation', $reservation->id) }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="replacement_studentid" id="replacement_studentid">
                <button type="button" class="btn btn-primary" onclick="promptReplacementNim()" style="margin: 10px 0;">
                    Pendampingan Selesai (Dengan pengganti)
                </button>
            </form>
        </div>
        @endif
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52;">Informasi Pemohon</h4>
            <table class="table table-bordered mb-0">
                <tr><th class="bg-light">Nama</th><td>{{ $reservation->user->identity->name }}</td></tr>
                <tr><th class="bg-light">Jenis Kelamin</th><td>{{ $reservation->user->identity->gender }}</td></tr>
                <tr><th class="bg-light">Program Studi</th><td>{{ $reservation->user->identity->major->name }}</td></tr>
                <tr><th class="bg-light">Fakultas</th><td>{{ $reservation->user->identity->major->faculty->name }}</td></tr>
                <tr><th class="bg-light">Angkatan</th><td>{{ $reservation->user->identity->generation }}</td></tr>
                <tr><th class="bg-light">Nomor WhatsApp</th><td><a href="https://wa.me/{{ $reservation->user->identity->whatsapp_number }}" target="_blank">{{ $reservation->user->identity->whatsapp_number }}</a></td></tr>
                <tr><th class="bg-light">Email</th><td><a href="mailto:{{ $reservation->user->identity->email }}">{{ $reservation->user->identity->email }}</a></td></tr>
                <tr><th class="bg-light">Disabilitas</th><td>{{ $reservation->user->identity->specials->pluck('special')->implode(', ') }}</td></tr>
            </table>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52;">Detail Permintaan</h4>
            <table class="table table-bordered mb-3">
                <tr>
                    <th class="bg-light">Tanggal</th>
                    <td>{{ $formattedDate }}@if((int)$reservation->dateid === (int)$today) <span class="badge bg-success ms-2">Hari Ini</span>@endif</td>
                </tr>
                <tr>
                    <th class="bg-light">Tempat</th>
                    <td>{{ $reservation->place }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Tipe Pendampingan</th>
                    <td>{{ $reservation->type }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Waktu Diajukan</th>
                    <td>{{ $reservation->formatted_created_at }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Deskripsi</th>
                    <td>{{ $reservation->description }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52;">Riwayat Log</h4>
            <div class="logs-container">
                @foreach($reservation->logs->sortBy('created_at') as $log)
                    @php
                        $dayNames = [
                            0 => "Minggu",
                            1 => "Senin", 
                            2 => "Selasa",
                            3 => "Rabu",
                            4 => "Kamis",
                            5 => "Jumat",
                            6 => "Sabtu"
                        ];
                        $monthNames = [
                            1 => "Januari",
                            2 => "Februari", 
                            3 => "Maret",
                            4 => "April",
                            5 => "Mei",
                            6 => "Juni",
                            7 => "Juli",
                            8 => "Agustus",
                            9 => "September",
                            10 => "Oktober",
                            11 => "November",
                            12 => "Desember"
                        ];
                        $logDate = $log->created_at;
                        $dayOfWeek = $logDate->dayOfWeek;
                        $day = $logDate->day;
                        $month = $logDate->month;
                        $year = $logDate->year;
                        $time = $logDate->format('H:i:s');
                        $formattedLogDate = $dayNames[$dayOfWeek] . ", " . $day . " " . $monthNames[$month] . " " . $year . " " . $time;
                    @endphp
                    <div class="log-entry mb-2 p-3 border-start border-4 border-primary bg-light rounded">
                        <div style="font-size: 14px;">
                            <strong>{{ $formattedLogDate }}:</strong> {{ $log->log }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
function confirmComplete() {
    if (confirm('Yakin ingin menandai pendampingan ini sebagai selesai?')) {
        document.getElementById('completeForm').submit();
    }
}

function promptReplacementNim() {
    const nim = prompt('Masukan NIM Pendamping Pengganti');
    if (nim !== null && nim.trim() !== "") {
        document.getElementById('replacement_studentid').value = nim.trim();
        document.getElementById('completeWithReplacementForm').submit();
    }
}
</script>
<script>
function confirmCancel() {
    const reason = prompt("Yakin ingin batalkan pendampingan? alasan pembatalan:");
    if (reason !== null && reason.trim() !== "") {
        document.getElementById('cancelReason').value = reason;
        document.getElementById('cancelForm').submit();
    }
}
</script>
