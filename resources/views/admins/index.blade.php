<x-header/>
<x-bootstrap/>

<main class="bg-light min-vh-100 py-5">
    <section class="container" style="max-width: 800px;">
        <h1 class="h3 fw-bold mb-4" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Dashboard Admin</h1>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Akun Aktif</div>
                        <div class="display-6 fw-bold" style="color:#1A4D2E;">{{$counts->total_users}}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Pengguna Disabilitas</div>
                        <div class="display-6 fw-bold" style="color:#1A4D2E;">{{$counts->users}}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Pendamping</div>
                        <div class="display-6 fw-bold" style="color:#1A4D2E;">{{$counts->staffs}}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Belum Diverifikasi</div>
                        <div class="display-6 fw-bold" style="color:#1A4D2E;">{{$counts->unverified_users}}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('admins.users') }}" class="btn btn-outline-primary w-100 mb-2">List User</a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('admins.faculties') }}" class="btn btn-outline-primary w-100 mb-2">Manajemen Fakultas</a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('admins.schedules', now()->format('mY')) }}" class="btn btn-outline-primary w-100 mb-2">Rekap Pendampingan</a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('admins.verify') }}" class="btn btn-outline-primary w-100 mb-2">Verifikasi User</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-light-subtle mb-2" style="max-width:500px; margin:auto;">
            <div class="card-body py-3">
                <form method="POST" action="{{ route('admins.changeContactPerson') }}" autocomplete="off" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-12 col-md-8">
                        <label for="whatsapp_number" class="form-label fw-semibold mb-1">Nomor WhatsApp Contact Person</label>
                        <input
                            type="text"
                            id="whatsapp_number"
                            name="whatsapp_number"
                            class="form-control form-control-sm @if($errors->has('whatsapp_number')) is-invalid @endif"
                            value="{{ old('whatsapp_number', $contact_person?->whatsapp_number) }}"
                            maxlength="15"
                            onkeydown="return /^[0-9]$/.test(event.key) || ['Backspace','ArrowLeft','ArrowRight','Tab','Delete'].includes(event.key) || (event.ctrlKey && ['a','c','v','x'].includes(event.key.toLowerCase()))"
                            required
                        >
                        @if ($errors->has('whatsapp_number'))
                            <div class="invalid-feedback d-block">{{ $errors->first('whatsapp_number') }}</div>
                        @endif
                        @if (session('success_contact_person'))
                            <div class="alert alert-success mt-2 py-1 px-2">{{ session('success_contact_person') }}</div>
                        @endif
                    </div>
                    <div class="col-12 col-md-4 d-grid">
                        <button type="submit" class="btn btn-outline-primary btn-sm fw-semibold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>