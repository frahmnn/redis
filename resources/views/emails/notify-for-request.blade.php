<!DOCTYPE html>
<html>
<head>
    <title>Permintaan Pendampingan Baru</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 5px; }
        .content { padding: 20px 0; }
        .detail-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .detail-table th, .detail-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .detail-table th { background-color: #f8f9fa; font-weight: bold; }
        .requirements { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .requirement-item { margin: 5px 0; }
        .check-mark { color: green; font-weight: bold; }
        .cross-mark { color: red; font-weight: bold; }
        .button { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔔 Permintaan Pendampingan Baru</h2>
            <p>Ada permintaan pendampingan baru yang memerlukan perhatian Anda</p>
        </div>
        
        <div class="content">
            <h3>Detail Permintaan</h3>
            <table class="detail-table">
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $formattedDate }}</td>
                </tr>
                <tr>
                    <th>Nama Pemohon</th>
                    <td>{{ $reservation->user->identity->name }}</td>
                </tr>
                <tr>
                    <th>Tempat</th>
                    <td>{{ $reservation->place }}</td>
                </tr>
                <tr>
                    <th>Tipe Pendamping</th>
                    <td>{{ $reservation->type }}</td>
                </tr>
                <tr>
                    <th>Waktu Pengajuan</th>
                    <td>{{ $formattedCreatedAt }}</td>
                </tr>
            </table>

            <div class="requirements">
                <h4>Status Prasyarat</h4>
                <p>{{ $requirements['met_requirements'] }} dari {{ $requirements['total_requirements'] }} prasyarat terpenuhi</p>
                
                @if(count($requirements['disabilities']) > 0)
                    <h5>Kecocokan Disabilitas:</h5>
                    @foreach($requirements['specialty_matches'] as $match)
                        <div class="requirement-item">
                            <span class="check-mark">✓</span> Disabilitas: {{ $match }}
                        </div>
                    @endforeach
                    @foreach(array_diff($requirements['disabilities'], $requirements['specialty_matches']) as $unmatch)
                        <div class="requirement-item">
                            <span class="cross-mark">✗</span> Disabilitas: {{ $unmatch }}
                        </div>
                    @endforeach
                @endif

                <div class="requirement-item">
                    @if($requirements['is_scheduled_this_day'])
                        <span class="check-mark">✓</span> Jadwal cocok
                    @else
                        <span class="cross-mark">✗</span> Jadwal tidak cocok
                    @endif
                </div>

                <div class="requirement-item">
                    @if($requirements['role_match'])
                        <span class="check-mark">✓</span> Divisi cocok ({{ $requirements['assistant_division'] ?? 'Tidak ada divisi' }} → {{ $reservation->type }})
                    @else
                        <span class="cross-mark">✗</span> Divisi tidak cocok ({{ $requirements['assistant_division'] ?? 'Tidak ada divisi' }} → {{ $reservation->type }})
                    @endif
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('assistants.request', $reservation->id) }}" class="button">
                    Lihat Detail & Ambil Tugas
                </a>
            </div>

            <p><small>Email ini dikirim secara otomatis. Silakan login ke dashboard untuk melihat detail lengkap dan mengambil tugas pendampingan.</small></p>
        </div>
    </div>
</body>
</html>
