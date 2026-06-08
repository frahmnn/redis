<x-header/>
<x-bootstrap/>

<main class="bg-light min-vh-100 py-5 d-flex flex-column align-items-center justify-content-start" style="font-family:'Nunito Sans',sans-serif;">
    <section class="container" style="max-width: 500px;">
        <div class="bg-white rounded-4 shadow p-4 p-md-5 mb-4 mt-3">
            <h1 class="h5 fw-bold mb-3 text-center" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Tambah Fakultas</h1>
            <p class="mb-3 text-center">Masukkan nama lengkap fakultas.<br><span class="text-muted small">Contoh: <b>Fakultas Ilmu Pendidikan</b></span></p>
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admins.insertFaculty') }}" method="post" autocomplete="off" class="needs-validation mt-3" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nama Fakultas</label>
                    <input type="text" id="name" name="name" class="form-control @if($errors->has('name')) is-invalid @endif" onkeydown="return/[a-zA-Z0-9'\- ]/i.test(event.key)" required>
                    @if ($errors->has('name'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('name') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary fw-semibold">Tambah</button>
                </div>
            </form>
        </div>
    </section>
</main>
