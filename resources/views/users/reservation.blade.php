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
            @if($reservation->status == 'Menunggu')
                @if($reservationDateInt < $todayInt)
                    <span class="badge bg-secondary mb-2">Kedaluwarsa</span>
                    @if(is_null($reservation->assistant))
                        <div class="alert alert-warning mt-2">
                            Mohon maaf, tidak ada pendamping yang tersedia untuk permintaan ini.<br>
                            Silakan ajukan ulang jika masih membutuhkan pendampingan.
                        </div>
                    @else
                        <div class="alert alert-info mt-2">
                            Jika membutuhkan bantuan lebih lanjut, silakan hubungi pendamping atau Contact Person.
                        </div>
                    @endif
                @else
                    @if(is_null($reservation->assistant))
                        <span class="badge bg-info text-dark mb-2">Menunggu Pendamping</span>
                        <div class="alert alert-info mt-2">
                            Mohon tunggu hingga pendamping ditugaskan.<br>
                            Pendamping akan segera menghubungi Anda melalui WhatsApp atau email.
                        </div>
                    @else
                        <span class="badge bg-success mb-2">Menunggu Hari Pendampingan</span>
                        <div class="alert alert-info mt-2">
                            Pendamping anda sudah ditugaskan.<br>
                            Mohon tunggu melalui WhatsApp atau email untuk konfirmasi lebih lanjut oleh pendamping.
                        </div>
                    @endif
                @endif
            @else
                <span class="badge bg-secondary mb-2">{{$reservation->status}}</span>
            @endif
        </div>
    </div>

    @if($reservation->assistant)
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52; font-family:'Poppins',sans-serif;">Informasi Pendamping</h4>
            <table class="table table-bordered mb-0">
                <tr>
                    <th class="bg-light">Nama</th>
                    <td>{{ $reservation->assistantUser->identity->name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Jenis Kelamin</th>
                    <td>{{ $reservation->assistantUser->identity->gender }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Program Studi</th>
                    <td>{{ $reservation->assistantUser->identity->major->name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Fakultas</th>
                    <td>{{ $reservation->assistantUser->identity->major->faculty->name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Angkatan</th>
                    <td>{{ $reservation->assistantUser->identity->generation }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Nomor WhatsApp</th>
                    <td><a href="https://wa.me/{{ $reservation->assistantUser->identity->whatsapp_number }}" target="_blank">{{ $reservation->assistantUser->identity->whatsapp_number }}</a></td>
                </tr>
                <tr>
                    <th class="bg-light">Email</th>
                    <td><a href="mailto:{{ $reservation->assistantUser->identity->email }}">{{ $reservation->assistantUser->identity->email }}</a></td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    @if($reservation->status === 'Menunggu' && $todayInt <= $reservationDateInt)
    <div class="mb-4">
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

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52; font-family:'Poppins',sans-serif;">Detail Permintaan</h4>
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
            <h4 class="mb-3" style="color:#4F6F52; font-family:'Poppins',sans-serif;">Riwayat Log</h4>
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
function confirmCancel() {
    const reason = prompt("Yakin ingin batalkan pendampingan? alasan pembatalan:");
    if (reason !== null && reason.trim() !== "") {
        document.getElementById('cancelReason').value = reason;
        document.getElementById('cancelForm').submit();
    }
}
</script>