<x-header/>
<x-bootstrap/>
<x-jquery/>
<x-datatables/>

<main class="bg-light min-vh-100 py-4" style="font-family:'Nunito Sans',sans-serif;">
    <section class="container-fluid">
        <h1 class="h4 fw-bold mb-4" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Verifikasi Pengguna</h1>
        @if (session("success"))
            <div class="alert alert-success">{{ session("success") }}</div>
        @endif
        <div class="table-responsive rounded-3 shadow-sm">
            <table id="users" class="table table-bordered table-hover align-middle bg-white mb-0" style="font-size:0.98rem;">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Jenis Kelamin</th>
                        <th scope="col">NIM</th>
                        <th scope="col">Program Studi</th>
                        <th scope="col">Angkatan</th>
                        <th scope="col">Disabilitas</th>
                        <th scope="col">Nomor Whatsapp</th>
                        <th scope="col">Email</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->student_id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span style="font-size:1rem;">{{ $user->major->name }}</span>
                                    <span class="text-primary" style="font-size:1rem;">{{ $user->major->faculty->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->generation }}</td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    @foreach($user->specials as $special)
                                        <li>{{ $special->special }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <a href="https://wa.me/{{ $user->whatsapp_number }}" target="_blank" rel="noopener" class="text-decoration-underline">{{ $user->whatsapp_number }}</a>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <form action="{{ route('admins.setVerify') }}" method="post" autocomplete="off" class="d-inline">@csrf
                                        <input type="hidden" name="user" value="{{ $user->id }}">
                                        <button type="submit" class="btn btn-success btn-sm w-100" aria-label="Verifikasi {{ $user->name }}" onclick="return confirm('Verifikasi akun {{ $user->name }}?')">Verifikasi</button>
                                    </form>
                                    <form action="{{ route('admins.editUsers') }}" method="post" autocomplete="off" class="d-inline">@csrf
                                        <input type="hidden" name="delete__{{ $user->id }}" value="on">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" aria-label="Hapus akun {{ $user->name }}" onclick="return confirm('Hapus akun {{ $user->name }}?')">Hapus Akun</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</main>
<script>
    let table = new DataTable("#users", {
        columnDefs:[{
            type:"num",
            targets: 0
        }]
    });
</script>