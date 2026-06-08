<x-header/>
<x-bootstrap/>
<div class="container my-4" style="max-width: 700px; font-family: 'Nunito Sans', Arial, sans-serif;">
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h2 class="mb-3" style="color:#4F6F52; font-family:'Poppins',sans-serif;">Detail Reservasi</h2>
            <table class="table table-bordered mb-3">
                <tr>
                    <th class="bg-light">Tanggal Permintaan</th>
                    <td>{{ $formattedDate }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Status</th>
                    <td>
                        @php
                            $reservationDateInt = (int)($reservation->dateid);
                            $todayInt = (int)($today);
                            $isExpired = $reservation->status === 'Menunggu' && $reservationDateInt < $todayInt;
                        @endphp
                        @if($isExpired)
                            <span class="badge bg-secondary">Kedaluwarsa</span>
                        @else
                            <span class="badge bg-info text-dark">{{ $reservation->status }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52;">Informasi Pemohon</h4>
            <table class="table table-bordered mb-0">
                <tr><th class="bg-light">Nama</th><td>{{ $reservation->user->identity->name }}</td></tr>
                <tr><th class="bg-light">Jenis Kelamin</th><td>{{ $reservation->user->identity->gender }}</td></tr>
                <tr><th class="bg-light">Program Studi</th><td>{{ $reservation->user->identity->major->name ?? '-' }}</td></tr>
                <tr><th class="bg-light">Fakultas</th><td>{{ $reservation->user->identity->major->faculty->name ?? '-' }}</td></tr>
                <tr><th class="bg-light">Angkatan</th><td>{{ $reservation->user->identity->generation }}</td></tr>
                <tr><th class="bg-light">Nomor WhatsApp</th><td><a href="https://wa.me/{{ $reservation->user->identity->whatsapp_number }}" target="_blank">{{ $reservation->user->identity->whatsapp_number }}</a></td></tr>
                <tr><th class="bg-light">Email</th><td><a href="mailto:{{ $reservation->user->identity->email }}">{{ $reservation->user->identity->email }}</a></td></tr>
                <tr><th class="bg-light">Disabilitas</th><td>{{ $reservation->user->identity->specials->pluck('special')->implode(', ') }}</td></tr>
            </table>
        </div>
    </div>

    @if($reservation->assistantUser)
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52;">Informasi Pendamping</h4>
            <table class="table table-bordered mb-0">
                <tr><th class="bg-light">Nama</th><td>{{ $reservation->assistantUser->identity->name }}</td></tr>
                <tr><th class="bg-light">Jenis Kelamin</th><td>{{ $reservation->assistantUser->identity->gender }}</td></tr>
                <tr><th class="bg-light">Program Studi</th><td>{{ $reservation->assistantUser->identity->major->name ?? '-' }}</td></tr>
                <tr><th class="bg-light">Fakultas</th><td>{{ $reservation->assistantUser->identity->major->faculty->name ?? '-' }}</td></tr>
                <tr><th class="bg-light">Angkatan</th><td>{{ $reservation->assistantUser->identity->generation }}</td></tr>
                <tr><th class="bg-light">Nomor WhatsApp</th><td><a href="https://wa.me/{{ $reservation->assistantUser->identity->whatsapp_number }}" target="_blank">{{ $reservation->assistantUser->identity->whatsapp_number }}</a></td></tr>
                <tr><th class="bg-light">Email</th><td><a href="mailto:{{ $reservation->assistantUser->identity->email }}">{{ $reservation->assistantUser->identity->email }}</a></td></tr>
            </table>
        </div>
    </div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52;">Log Aktivitas</h4>
            @if($reservation->logs && count($reservation->logs))
                <ul class="list-group list-group-flush">
                    @foreach($reservation->logs as $log)
                        <li class="list-group-item">
                            <span class="fw-semibold">{{ $log->created_at }}:</span> {{ $log->log }}
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-info mb-0">Tidak ada log aktivitas.</div>
            @endif
        </div>
    </div>
</div>
