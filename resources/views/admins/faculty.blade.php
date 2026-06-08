<x-header/>
<x-bootstrap/>
<x-jquery/>
<x-datatables/>

<main class="bg-light min-vh-100 py-4">
    <section class="container-fluid" style="max-width: 900px;">
        <h1 class="h4 fw-bold mb-4" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Detail Fakultas</h1>
        @if (session("success"))
            <div class="alert alert-success">{{ session("success") }}</div>
        @endif
        @if (session("errors"))
            <div class="alert alert-danger">
                <p class="mb-1 fw-semibold">Ada data yang gagal diperbarui:</p>
                <ul class="mb-0">
                    @foreach (session("errors") as $student_id => $fieldErrors)
                        @foreach ($fieldErrors as $field => $message)
                            <li>{{ $student_id }}: {{ $message }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="submitForm" method="post" autocomplete="off" class="mb-3">@csrf
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body d-flex flex-wrap gap-3 align-items-center">
                    <div class="flex-grow-1">
                        <label class="form-label fw-semibold mb-1">Nama Fakultas</label>
                        <div class="d-flex align-items-center gap-2">
                            <a suppose="delete" majorId="{{ $faculty->name }}"></a>
                            <a oldValue="{{ $faculty->name }}" class="fw-bold fs-5">{{ $faculty->name }}</a>
                            <a suppose="name" majorId="{{ $faculty->name }}"></a>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admins.newMajor', $faculty->name) }}" class="btn btn-primary btn-sm">Tambah Program Studi</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive rounded-3 shadow-sm">
                <table id="table" class="table table-bordered table-hover align-middle bg-white mb-0" style="font-size:0.98rem;">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Program Studi</th>
                            <th scope="col">Pengguna Disabilitas</th>
                            <th scope="col">Pendamping</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($majors as $index => $major)
                            <tr major="{{ $major->name }}">
                                <td>
                                    {{ $index + 1 }}
                                    <a suppose="delete" majorId="{{ $major->id }}"></a>
                                    <a style="display: none;">{{ $faculty->name }}</a>
                                </td>
                                <td>
                                    <a oldValue="{{ $major->name }}">{{ $major->name }}</a>
                                    <a suppose="name" majorId="{{ $major->id }}"></a>
                                </td>
                                <td>
                                    @if($major->users > 0)
                                        <a href="{{ route('admins.users', ['search' => '(?=.*Akses:Pengguna)(?=.*Program Studi:' . $major->name . ')']) }}" class="text-decoration-underline">{{ $major->users }}</a>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($major->staffs > 0)
                                        <a href="{{ route('admins.users', ['search' => '(?=.*(Akses:Admin|Akses:Pendamping))(?=.*Program Studi:' . $major->name . ')']) }}" class="text-decoration-underline">{{ $major->staffs }}</a>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </section>
</main>
<script>
    let
        editing = preparingDatas = submit = findingInvalid = false,
        oldPageLength,
        currentPage,
        pageSearch;
    const
        editButton = document.getElementById("edit"),
        deletes = document.querySelectorAll("a[suppose='delete']"),
        views = document.querySelectorAll("[oldValue]"),
        facultyName = @json($faculty->name),
        majorMembers = (() => {
            const restructured = {};
            @json($majors).forEach(major => {
                restructured[major.id] = major.staffs + major.users;
            });
            return restructured;
        })(),
        totalMembers = Object.values(majorMembers).reduce((sum, val) => sum + val, 0),
        table = new DataTable("#table", {
            columnDefs:[
                {type: "num",
                targets: 0}],
            initComplete: function (){
                this.api().search(
                    sessionErrors = @json(
                        array_map(
                            function($major) { return "(?=.*{$major})"; },
                            array_keys(session("errors") ?? [])
                        )
                    ).filter(error => !error.includes(facultyName)).join("|")
                    ||
                    new URLSearchParams(window.location.search).get('search')
                    ||
                    "",
                true).draw();},
            drawCallback: function (){
                if (preparingDatas){
                    preparingDatas = false;
                    editing = !editing;
                    editButton.textContent = editing ? "Batal Edit" : "Edit";
                    if (editing){
                        submitForm.action = "{{ route('admins.faculty', $faculty->name) }}";
                        const submitButton = document.createElement("button");
                        submitButton.type = "button";
                        submitButton.textContent = "Simpan Perubahan";
                        submitButton.addEventListener("click", function(){
                            oldPageLength = table.page.len();
                            submit = true;
                            table.page.len(-1).search("").draw();});
                        submitEdit.appendChild(submitButton);
                        deletes.forEach(deleteInput => {
                            const majorId = deleteInput.getAttribute("majorId");
                            setupDeleteButton(deleteInput, majorId);
                        });
                        prepareEditableFields(views);}
                    else{
                        submitEdit.innerHTML = "";
                        submitForm.action = "";
                        resetViewsAndForms(views, document);}
                    table.page.len(oldPageLength).search(pageSearch).page(currentPage).draw('page');}
                if (submit){
                    submit = false;
                    if (!submitForm.checkValidity()){
                        const invalidField = submitForm.querySelector(":invalid");
                        if (invalidField){
                            const major = invalidField.closest("tr").getAttribute("major");
                            findingInvalid = true;
                            table.page.len(oldPageLength).search(major).draw();
                        }}
                    else submitForm.requestSubmit();}
                if (findingInvalid){
                    findingInvalid = false;
                    submitForm.requestSubmit();
                }
            }
        });
        console.log( majorMembers);

    editButton.addEventListener("click", function (){
        currentPage = table.page.info().page;
        oldPageLength = table.page.len();
        pageSearch = table.search();
        preparingDatas = true;
        table.page.len(-1).search("").draw();});

    document.addEventListener("keydown", function (e){
        if ((e.target.tagName == "INPUT" || e.target.tagName == "SELECT") && e.key == "Enter") e.preventDefault();});

    function createDeleteCheckbox(majorId, isForMajor = true) {
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.name = "delete__" + majorId;
        checkbox.classList.add("iniForm");
        checkbox.addEventListener("change", () => {
            const tr = checkbox.closest("tr");
            if (checkbox.checked) {
                resetViewsAndForms(tr.querySelectorAll("[oldValue]"), tr);
                if (!isForMajor) {
                    // Faculty delete - also reset all table content
                    resetViewsAndForms(document.getElementById("table").querySelectorAll("[oldValue]"), document.getElementById("table"));
                    document.getElementById("table").querySelectorAll("[suppose]").forEach(suppose => {suppose.innerHTML = "";});
                }
            } else {
                prepareEditableFields(tr.querySelectorAll("[oldValue]"));
                if (!isForMajor) {
                    // Faculty uncheck - restore table editing and delete buttons
                    prepareEditableFields(document.getElementById("table").querySelectorAll("[oldValue]"));
                    restoreTableDeleteButtons();
                }
            }
        });
        return checkbox;
    }

    function restoreTableDeleteButtons() {
        document.getElementById("table").querySelectorAll("a[suppose='delete']").forEach(deleteInput => {
            const majorId = deleteInput.getAttribute("majorId");
            if (majorMembers[majorId] == 0) {
                const checkbox = createDeleteCheckbox(majorId, true);
                deleteInput.append(checkbox, document.createTextNode("hapus"));
            } else {
                deleteInput.append(document.createTextNode("Prodi tidak kosong"));
            }
        });
    }

    function setupDeleteButton(deleteInput, majorId) {
        if (majorId == facultyName) {
            // This is the faculty delete button
            if (totalMembers == 0) {
                const checkbox = createDeleteCheckbox(majorId, false);
                deleteInput.append(checkbox, document.createTextNode("hapus"));
            } else {
                deleteInput.append(document.createTextNode("Fakultas tidak kosong."));
            }
        } else {
            // This is a major delete button
            if (majorMembers[majorId] == 0) {
                const checkbox = createDeleteCheckbox(majorId, true);
                deleteInput.append(checkbox, document.createTextNode("hapus"));
            } else {
                deleteInput.append(document.createTextNode("Prodi tidak kosong"));
            }
        }
    }

    function prepareEditableFields(viewsCollection){
        viewsCollection.forEach(view => {
            view.style.textDecoration = "underline";
            view.style.cursor = "pointer";
            if (view.hasAttribute("target")) view.removeAttribute("href");
            view.onclick = () => {
                const
                    suppose = view.closest("td").querySelector("a[suppose]"),
                    cancelEdit = document.createElement("u"),
                    input = document.createElement("input"),
                    value = view.getAttribute("oldValue")
                    editor = (() => {
                        input.type = "text";
                        input.required = true;
                        input.value = value;
                        input.onkeydown = event => 
                        /[a-z0-9 \-']/i.test(event.key);
                        input.addEventListener("blur", () => {
                            if (input.value == value) input.removeAttribute("name");
                            else input.name = suppose.getAttribute("suppose") + "__" + suppose.getAttribute("majorId");
                        });
                        return input;
                    })();
                view.style.display = "none";
                cancelEdit.textContent = "Batal Edit";
                cancelEdit.style.cursor = "pointer";
                cancelEdit.classList.add("iniForm");
                editor.classList.add("iniForm");
                cancelEdit.onclick = () => {
                    const appending = suppose.querySelector(".iniForm");
                    appending.remove();
                    cancelEdit.remove();
                    view.style.display = "";};
                suppose.append(editor, cancelEdit);
                editor.focus();
            };
        });}

    function resetViewsAndForms(viewsCollection, container){
        viewsCollection.forEach(view => {
            view.style.textDecoration = "none";
            view.style.cursor = "default";
            view.onclick = null;
            view.style.display = "";
            const selector = container == document ? "[suppose]" : "[suppose]:not([suppose='delete'])";
            container.querySelectorAll(selector).forEach(suppose => {suppose.innerHTML = "";});
        });}
</script>