<x-header/>
<x-bootstrap/>
<x-jquery/>
<x-datatables/>

<main class="bg-light min-vh-100 py-4">
    <section class="container-fluid">
        <h1 class="h4 fw-bold mb-4" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Manajemen Fakultas</h1>
        @if (session("success"))
            <div class="alert alert-success">{{ session("success") }}</div>
        @endif
        <div class="mb-3">
            <a href="{{ route('admins.newFaculty') }}" class="btn btn-primary">Tambah Fakultas</a>
        </div>
        <div class="table-responsive rounded-3 shadow-sm">
            <table class="table table-bordered table-hover align-middle bg-white mb-0" style="font-size:0.98rem;">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Pengguna Disabilitas</th>
                        <th scope="col">Pendamping</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faculties as $index => $faculty)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $faculty->name }}</td>
                            <td>
                                @if($faculty->users > 0)
                                    <a href="{{ route('admins.users', ['search' => '(?=.*Akses:Pengguna)(?=.*Fakultas:' . $faculty->name . ')']) }}" class="text-decoration-underline">{{ $faculty->users }}</a>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                @if($faculty->staffs > 0)
                                    <a href="{{ route('admins.users', ['search' => '(?=.*(Akses:Admin|Akses:Pendamping))(?=.*Fakultas:' . $faculty->name . ')']) }}" class="text-decoration-underline">{{ $faculty->staffs }}</a>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admins.faculty', $faculty->name) }}" class="btn btn-outline-primary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</main>
<script>
    let table = new DataTable("table");
</script>