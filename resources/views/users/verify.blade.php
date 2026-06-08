
<x-header/>
<x-jquery/>
<x-select2/>
<x-bootstrap/>

<main class="bg-light min-vh-100 py-5 d-flex flex-column align-items-center justify-content-start" style="font-family:'Nunito Sans',sans-serif;">
    <section class="container" style="max-width: 600px;">
        <div class="bg-white rounded-4 shadow p-4 p-md-5 mb-4 mt-3">
            <h1 class="h3 fw-bold mb-3 text-center" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Verifikasi Identitas</h1>
            <p class="mb-4 text-center">Selamat datang di <span class="fw-bold">[nama aplikasi]</span>! Sebelum menggunakan layanan, silakan lengkapi data berikut.</p>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (auth()->user()->identity->requested)
                <div class="alert alert-success" role="alert">
                    <b>Identitas Anda berhasil disimpan. Mohon tunggu verifikasi admin. Selama belum diverifikasi, data masih bisa Anda ubah.</b>
                </div>
            @endif

            <form action="{{ route('users.sendVerification') }}" method="post" autocomplete="off" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                    <input id="name" name="name" type="text" class="form-control @if($errors->has('name')) is-invalid @endif" value="{{ old('name', auth()->user()->identity->name) }}" onkeydown="return/[a-z' ]/i.test(event.key)" required autocomplete="name">
                    @if ($errors->has('name'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('name') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="student_id" class="form-label fw-semibold">NIM</label>
                    <input id="student_id" name="student_id" type="text" class="form-control @if($errors->has('student_id')) is-invalid @endif" value="{{ old('student_id', auth()->user()->identity->student_id) }}" minlength="10" maxlength="10" onkeydown="return /^[0-9]$/.test(event.key) || ['Backspace','ArrowLeft','ArrowRight','Tab','Delete'].includes(event.key) || (event.ctrlKey && ['a','c','v','x'].includes(event.key.toLowerCase()))" required autocomplete="off">
                    @if ($errors->has('student_id'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('student_id') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>

                <fieldset class="mb-3">
                    <legend class="form-label fw-semibold mb-2">Jenis Kelamin</legend>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="genderL" value="Laki-Laki" {{ old('gender', auth()->user()->identity->gender) == 'Laki-Laki' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="genderL">Laki-Laki</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="genderP" value="Perempuan" {{ old('gender', auth()->user()->identity->gender) == 'Perempuan' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="genderP">Perempuan</label>
                    </div>
                    @if ($errors->has('gender'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('gender') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </fieldset>

                <div class="mb-3">
                    <label for="faculty" class="form-label fw-semibold">Fakultas</label>
                    <select id="faculty" class="form-select" required>
                        <option disabled selected hidden value=""></option>
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty['id'] }}">{{ $faculty['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="major_id" class="form-label fw-semibold">Program Studi</label>
                    <select name="major_id" id="major_id" class="form-select" required disabled></select>
                    @if ($errors->has('major_id'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('major_id') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>

                @php $currentYear = now()->year; @endphp
                <div class="mb-3">
                    <label for="generation" class="form-label fw-semibold">Angkatan</label>
                    <select name="generation" id="generation" class="form-select" required>
                        <option disabled selected hidden value=""></option>
                        <option value="{{ $currentYear }}" {{ old('generation', auth()->user()->identity->generation) == $currentYear ? 'selected' : '' }}>{{ $currentYear }}</option>
                        <option value="{{ $currentYear-1 }}" {{ old('generation', auth()->user()->identity->generation) == $currentYear-1 ? 'selected' : '' }}>{{ $currentYear-1 }}</option>
                        <option value="{{ $currentYear-2 }}" {{ old('generation', auth()->user()->identity->generation) == $currentYear-2 ? 'selected' : '' }}>{{ $currentYear-2 }}</option>
                        <option value="{{ $currentYear-3 }}" {{ old('generation', auth()->user()->identity->generation) == $currentYear-3 ? 'selected' : '' }}>{{ $currentYear-3 }}</option>
                        <option value="{{ $currentYear-4 }}" {{ old('generation', auth()->user()->identity->generation) == $currentYear-4 ? 'selected' : '' }}>{{ $currentYear-4 }}</option>
                        <option value="{{ $currentYear-5 }}" {{ old('generation', auth()->user()->identity->generation) == $currentYear-5 ? 'selected' : '' }}>{{ $currentYear-5 }}</option>
                        <option value="{{ $currentYear-6 }}" {{ old('generation', auth()->user()->identity->generation) == $currentYear-6 ? 'selected' : '' }}>{{ $currentYear-6 }}</option>
                    </select>
                    @if ($errors->has('generation'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('generation') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="specials" class="form-label fw-semibold">Disabilitas</label>
                    <select name="specials[]" id="specials" class="form-select" multiple required>
                        <option value="placeholder1" @if(in_array('placeholder1', old('specials', []))) selected @endif @if(!old() && auth()->user()->identity->specials->contains('special', 'placeholder1')) selected @endif>placeholder1</option>
                        <option value="placeholder2" @if(in_array('placeholder2', old('specials', []))) selected @endif @if(!old() && auth()->user()->identity->specials->contains('special', 'placeholder2')) selected @endif>placeholder2</option>
                    </select>
                    @if ($errors->has('specials.*'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('specials.*') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="alert alert-info mb-4" role="alert" style="font-size:1rem;">
                    Pastikan untuk mengisi <b>Nomor Whatsapp</b> dan <b>Email</b> yang aktif agar pendampingan dan notifikasi berjalan lancar.
                </div>

                <div class="mb-3">
                    <label for="whatsapp_number" class="form-label fw-semibold">Nomor Whatsapp</label>
                    <input id="whatsapp_number" name="whatsapp_number" type="text" class="form-control @if($errors->has('whatsapp_number')) is-invalid @endif" value="{{ old('whatsapp_number', auth()->user()->identity->whatsapp_number) }}" onkeydown="return /^[0-9]$/.test(event.key) || ['Backspace','ArrowLeft','ArrowRight','Tab','Delete'].includes(event.key) || (event.ctrlKey && ['a','c','v','x'].includes(event.key.toLowerCase()))" required autocomplete="tel">
                    @if ($errors->has('whatsapp_number'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('whatsapp_number') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input id="email" name="email" type="email" class="form-control @if($errors->has('email')) is-invalid @endif" value="{{ old('email', auth()->user()->identity->email) }}" required autocomplete="email">
                    @if ($errors->has('email'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('email') as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-semibold">Kirim</button>
                </div>
            </form>
        </div>
    </section>
</main>

<script>
    const majorsMap = @json($majors);
    const facultySelect = document.getElementById("faculty");
    const majorSelect = document.getElementById("major_id");

    $(document).ready(function() {
        $("[name='specials[]']").select2({
            width: '100%',
            placeholder: 'Pilih Disabilitas',
            allowClear: true
        });
    });

    function populateMajors(facultyId) {
        const majors = (majorsMap[facultyId] ?? []);
        majorSelect.innerHTML = "<option disabled selected hidden value=''>Pilih Program Studi</option>";
        majors.forEach(major => {
            const option = document.createElement("option");
            option.value = major.id;
            option.textContent = major.name;
            majorSelect.appendChild(option);
        });
        majorSelect.disabled = majors.length == 0;
        if (majors.length == 1) majorSelect.selectedIndex = 1;
    }

    facultySelect.addEventListener("change", function (){
        populateMajors(this.value);
    });

    window.addEventListener("DOMContentLoaded", () => {
        const oldMajorId = "{{ old('major_id', auth()->user()->identity->major_id) }}";
        if (oldMajorId){
            let facultyId = null;
            for (const [fid, majors] of Object.entries(majorsMap)){
                if (majors.some(major => major.id == oldMajorId)){
                    facultyId = fid;
                    break;
                }
            }

            if (facultyId){
                facultySelect.value = facultyId;
                populateMajors(facultyId);
                setTimeout(() => {
                    majorSelect.value = oldMajorId;
                }, 0);
            }
        }
    });
</script>