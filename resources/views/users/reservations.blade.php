<x-header/>
<x-bootstrap/>
<x-jquery/>
<x-datatables/>
@if (session("success"))
    <div class="alert alert-success">
        {{ session("success") }}
    </div>
@endif
<div class="container my-4" style="max-width: 900px; font-family: 'Nunito Sans', Arial, sans-serif;">
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h3 class="mb-3" style="color:#4F6F52; font-family:'Poppins',sans-serif;">Pendampingan Aktif</h3>
            <table id="activeTable" class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Tempat</th>
                        <th>Tipe</th>
                        <th>Pendamping</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $activeCount = 0; @endphp
                    @foreach ($reservations as $reservation)
                        @if(in_array($reservation->status, ['Menunggu', 'confirmed']) && ($reservation->visual_status ?? $reservation->status) !== 'Kedaluwarsa')
                            @php $activeCount++; @endphp
                            <tr>
                                <td>{{ $activeCount }}</td>
                                <td>{{ substr($reservation->dateid, 0, 2) }}-{{ substr($reservation->dateid, 2, 2) }}-{{ substr($reservation->dateid, 4, 4) }}</td>
                                <td>{{ $reservation->place }}</td>
                                <td>{{ $reservation->type }}</td>
                                <td>{{ $reservation->assistantUser->identity->name ?? 'Belum ditugaskan' }}</td>
                                <td>
                                    <a href="{{ route('users.reservation', $reservation->id) }}" class="btn btn-outline-primary btn-sm">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h3 class="mb-3" style="color:#4F6F52; font-family:'Poppins',sans-serif;">Riwayat Pendampingan</h3>
            <table id="historyTable" class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Tempat</th>
                        <th>Tipe</th>
                        <th>Pendamping</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $historyCount = 0; @endphp
                    @foreach ($reservations as $reservation)
                        @if(in_array($reservation->visual_status ?? $reservation->status, ['Dibatalkan', 'Selesai', 'Kedaluwarsa']))
                            @php $historyCount++; @endphp
                            <tr>
                                <td>{{ $historyCount }}</td>
                                <td>{{ substr($reservation->dateid, 0, 2) }}-{{ substr($reservation->dateid, 2, 2) }}-{{ substr($reservation->dateid, 4, 4) }}</td>
                                <td>{{ $reservation->place }}</td>
                                <td>{{ $reservation->type }}</td>
                                <td>{{ $reservation->assistantUser->identity->name ?? 'Tidak ada' }}</td>
                                <td>{{ $reservation->visual_status ?? $reservation->status }}</td>
                                <td>
                                    <a href="{{ route('users.reservation', $reservation->id) }}" class="btn btn-outline-primary btn-sm">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    let activeTable = new DataTable("#activeTable", {
        columnDefs:[{
            type:"num",
            targets: 0
        }]
    });
    let historyTable = new DataTable("#historyTable", {
        columnDefs:[{
            type:"num",
            targets: 0
        }]
    });
</script>