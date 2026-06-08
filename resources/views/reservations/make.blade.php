<x-header/>
<x-bootstrap/>
<main class="bg-light min-vh-100 py-4" style="font-family:'Nunito Sans',sans-serif;">
    <section class="container" style="max-width: 700px;">
        <div class="rounded-4 shadow-sm mb-4 p-4" style="background: linear-gradient(90deg, #4F6F52 0%, #A7D397 100%); color: #fff;">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h2 class="mb-0" style="font-family:'Poppins',sans-serif; font-weight:700; letter-spacing:0.01em;">Ajukan Permintaan Pendampingan</h2>
                    <div class="fs-5">Tanggal: <span class="fw-bold">{{ $formattedDate }}</span></div>
                </div>
            </div>
        </div>
        @if (!empty($reservationNotice))
            <div class="alert alert-warning">
                <strong>{{ $reservationNotice }}</strong>
            </div>
        @endif
        @if (session("errors") || $errors->any())
            <div class="alert alert-danger">
                <p><strong>Ada kesalahan dalam pengisian form:</strong></p>
                <ul>
                    @if (session("errors"))
                        @foreach (session("errors") as $field => $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <form action="{{ route('reservations.insert', [$dateid]) }}" method="POST" autocomplete="off">@csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tempat:</label><br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="place" id="place_sekretariat" value="Sekretariat Relawan Disabilitas UNJ" {{ old('place') == 'Sekretariat Relawan Disabilitas UNJ' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="place_sekretariat">Sekretariat Relawan Disabilitas UNJ</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="place" id="place_others" value="others" {{ old('place') && old('place') != 'Sekretariat Relawan Disabilitas UNJ' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="place_others">Lainnya</label>
                        </div>
                        <input type="text" class="form-control mt-2" name="other_place" id="other_place_input" placeholder="Tempat Lainnya" value="{{ old('other_place') }}" disabled required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="type_select">Tipe Pendampingan:</label>
                        <select class="form-select" name="type" id="type_select" required>
                            <option selected disabled hidden></option>
                            <option value="Pendampingan" {{ old('type') == 'Pendampingan' ? 'selected' : '' }}>Pendampingan</option>
                            <option value="Advokasi" {{ old('type') == 'Advokasi' ? 'selected' : '' }}>Advokasi</option>
                            <option value="Humas" {{ old('type') == 'Humas' ? 'selected' : '' }}>Humas</option>
                            <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <div class="form-text mt-2 mb-0">Pilih tipe yang paling sesuai dengan kebutuhan Anda.</div>
                    </div>
                    <div class="mb-4">
                        <table class="table table-sm table-bordered bg-white mb-2">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Pendampingan</td>
                                    <td>Bantuan langsung dalam kegiatan perkuliahan seperti mobilitas, komunikasi, atau kegiatan akademik.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Advokasi</td>
                                    <td>Bantuan dalam penyelesaian masalah akademik, administrasi, atau situasi yang memerlukan mediasi.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Humas</td>
                                    <td>Bantuan komunikasi, sosialisasi, dokumentasi, dan hubungan masyarakat.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Lainnya</td>
                                    <td>Mohon sertakan deskripsi lengkap kebutuhan Anda di keterangan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="description">Keterangan:</label>
                        <textarea class="form-control" name="description" id="description" rows="4" required placeholder="Deskripsikan kebutuhan Anda secara lengkap, termasuk jenis bantuan yang diperlukan, waktu (jam), dan lokasi.">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100 py-2 fs-5">Ajukan Permintaan</button>
                </form>
            </div>
        </div>
    </section>
</main>
<script>
document.addEventListener("DOMContentLoaded", function(){
    const
        othersRadio = document.querySelector("input[name='place'][value='others']"),
        otherPlaceInput = document.getElementById("other_place_input");
    function toggleOtherPlaceInput(){
        if (othersRadio.checked){
            otherPlaceInput.disabled = false;
            if (!otherPlaceInput.value) otherPlaceInput.focus();}
        else{
            otherPlaceInput.disabled = true;
            if (!otherPlaceInput.value) otherPlaceInput.value = "";
        }}
    document.querySelector("input[name='place'][value='Sekretariat Relawan Disabilitas UNJ']").addEventListener("change", toggleOtherPlaceInput);
    othersRadio.addEventListener("change", toggleOtherPlaceInput);
    toggleOtherPlaceInput();
});
</script>