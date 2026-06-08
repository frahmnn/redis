<x-header/>
<x-bootstrap/>
<div class="container my-4" style="max-width: 700px; font-family: 'Nunito Sans', Arial, sans-serif;">
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h3 class="mb-3" style="color:#4F6F52; font-family:'Poppins',sans-serif;">Permintaan Pendampingan</h3>
            <h4 class="mb-3" style="color:#4F6F52;">Informasi Pemohon</h4>
            <table class="table table-bordered mb-0">
                <tr>
                    <th class="bg-light">Nama</th>
                    <td>{{ $reservation->name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Jenis Kelamin</th>
                    <td>{{ $reservation->gender }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Program Studi</th>
                    <td>{{ $reservation->major_name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Fakultas</th>
                    <td>{{ $reservation->faculty_name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Angkatan</th>
                    <td>{{ $reservation->generation }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Nomor WhatsApp</th>
                    <td><a href="https://wa.me/{{ $reservation->whatsapp_number }}" target="_blank">{{ $reservation->whatsapp_number }}</a></td>
                </tr>
                <tr>
                    <th class="bg-light">Email</th>
                    <td><a href="mailto:{{ $reservation->email }}">{{ $reservation->email }}</a></td>
                </tr>
                <tr>
                    <th class="bg-light">Disabilitas</th>
                    <td>{{ implode(', ', $reservation->disabilities) }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h4 class="mb-3" style="color:#4F6F52;">Detail Permintaan</h4>
            <table class="table table-bordered mb-3">
                <tr>
                    <th class="bg-light">Tanggal</th>
                    <td>{{ $formattedDate }}@if($isToday) <span class="badge bg-success ms-2">Hari Ini</span>@endif</td>
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
            <h4 class="mb-3" style="color:#4F6F52;">Status Prasyarat</h4>
            <div class="mb-2">Prasyarat: <span class="fw-bold">{{ $reservation->met_requirements }} dari {{ $reservation->total_requirements }}</span></div>
            @if(count($reservation->disabilities) > 0)
                <div class="mb-2"><strong>Kecocokan Disabilitas:</strong></div>
                @foreach($reservation->specialty_matches as $matchedDisability)
                    <span class="badge bg-success me-1 mb-1">✓ Disabilitas: {{ $matchedDisability }}</span>
                @endforeach
                @foreach(array_diff($reservation->disabilities, $reservation->specialty_matches) as $unmatchedDisability)
                    <span class="badge bg-secondary me-1 mb-1">✗ Disabilitas: {{ $unmatchedDisability }}</span>
                @endforeach
            @endif
            <div class="mb-2">
                @if($reservation->is_scheduled_this_day)
                    <span class="badge bg-success me-1">✓ Jadwal cocok</span>
                @else
                    <span class="badge bg-secondary me-1">✗ Jadwal tidak cocok</span>
                @endif
            </div>
            <div class="mb-2">
                @if($reservation->role_match)
                    <span class="badge bg-success me-1">✓ Divisi cocok ({{ $reservation->assistant_division ?? 'Tidak ada divisi' }} → {{ $reservation->type }})</span>
                @else
                    <span class="badge bg-secondary me-1">✗ Divisi tidak cocok ({{ $reservation->assistant_division ?? 'Tidak ada divisi' }} → {{ $reservation->type }})</span>
                @endif
            </div>
            <form action="{{ route('assistants.takeReservation', $reservation->id) }}" method="post" autocomplete="off" class="mt-3">@csrf
                <button type="submit" class="btn btn-primary btn-lg">Ambil tugas pendampingan</button>
            </form>
        </div>
    </div>
</div>