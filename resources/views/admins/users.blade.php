<x-header/>
<x-bootstrap/>
<x-jquery/>
<x-datatables/>
<x-select2/>

<main class="bg-light min-vh-100 py-4">
    <section class="container-fluid">
        <h1 class="h4 fw-bold mb-4" style="color:#1A4D2E; font-family:'Poppins',sans-serif;">Manajemen Pengguna</h1>
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
            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                <button type="button" id="edit" class="btn btn-outline-primary btn-sm">Edit</button>
                <span id="submitEdit"></span>
            </div>
            <div class="table-responsive rounded-3 shadow-sm">
                <table class="table table-bordered table-hover align-middle bg-white mb-0" style="font-size:0.98rem;">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Akses</th>
                            <th scope="col">Jenis Kelamin</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Program Studi</th>
                            <th scope="col">Angkatan</th>
                            <th scope="col">Disabilitas / Spesialisasi</th>
                            <th scope="col">Nomor Whatsapp</th>
                            <th scope="col">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $foundUser = false; @endphp
                        @foreach ($users as $index => $user)
                            <tr studentId="{{ $user->student_id }}">
                                <td>
                                    {{ $index + 1 }}
                                    @if ($foundUser || $user->id != auth()->user()->identity->id)
                                        <a suppose="delete" userId="{{ $user->id }}"></a>
                                    @endif
                                </td>
                                <td>
                                    <a oldValue="{{ $user->name }}">{{ $user->name }}</a>
                                    <a suppose="name" userId="{{ $user->id }}"></a>
                                </td>
                                <td>
                                    <a  
                                        @if (!$foundUser && $user->id == auth()->user()->identity->id)
                                        @php $foundUser = !$foundUser; @endphp
                                        oldValue="{{ $user->special_role }}" division="{{ $user->division }}" currentUser="true"
                                        @else oldValue="{{ $user->special_role }}" division="{{ $user->division }}" @endif
                                    >
                                        <b style="display:none">Akses:</b>{{ $user->special_role ?? "Pengguna" }}
                                        @if($user->special_role != null)
                                            <br><b style="display:none">Divisi:</b>{{ $user->division ?? 'Tidak Ada' }}
                                        @endif
                                    </a>
                                    <a suppose="special_role" userId="{{ $user->id }}"></a>
                                </td>
                                <td>
                                    <a style="display:none">Jenis Kelamin:</a><a oldValue="{{ $user->gender }}">{{ $user->gender }}</a>
                                    <a suppose="gender" userId="{{ $user->id }}"></a>
                                </td>
                                <td>
                                    <a style="display:none">NIM:</a><a oldValue="{{ $user->student_id }}">{{ $user->student_id }}</a>
                                    <a suppose="student_id" userId="{{ $user->id }}"></a>
                                </td>
                                <td>
                                    <a style="display:none">Program Studi:</a><a oldValue="{{ $user->major->id ?? '' }}" faculty="{{ $user->major->faculty->id ?? ''}}">{{ $user->major->name ?? 'Tidak Ada' }}<br><b style="display:none">Fakultas:</b>{{ $user->major->faculty->name ?? '' }}</a>
                                    <a suppose="major_id" userId="{{ $user->id }}"></a>
                                </td>
                                <td>
                                    <a style="display:none">Angkatan:</a><a oldValue="{{ $user->generation }}">{{ $user->generation }}</a>
                                    <a suppose="generation" userId="{{ $user->id }}"></a>
                                </td>
                                <td>
                                    <ul oldValue="{{ $user->specials->pluck('special')->toJson() }}" class="mb-0 ps-3">
                                        @foreach($user->specials as $special)
                                            <li><a style="display:none">Disabilitas:</a>{{ $special->special }}</li>
                                        @endforeach
                                    </ul>
                                    <a suppose="specials" userId="{{ $user->id }}"></a>
                                </td>
                                <td>
                                    <a oldValue="{{ $user->whatsapp_number }}" href="https://wa.me/{{ $user->whatsapp_number }}" target="_blank">{{ $user->whatsapp_number }}</a>
                                    <a suppose="whatsapp_number" userId="{{ $user->id }}"></a>
                                </td>
                                <td>
                                    <a oldValue="{{ $user->email }}">{{ $user->email }}</a>
                                    <a suppose="email" userId="{{ $user->id }}"></a>
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
        submitForm = document.getElementById("submitForm"),
        submitEdit = document.getElementById("submitEdit"),
        TBody = document.querySelector("tbody"),
        views = document.querySelectorAll("[oldValue]"),
        deletes = document.querySelectorAll("a[suppose='delete']"),
        faculties = @json($faculties),
        majors = @json($majors),
        year0 = new Date().getFullYear(),
        year1 = year0 - 1,
        year2 = year0 - 2,
        year3 = year0 - 3,
        year4 = year0 - 4,
        year5 = year0 - 5,
        year6 = year0 - 6,
        table = new DataTable("table", {
            columnDefs:[{
                type: "num",
                targets: 0}],
            initComplete: function (){
                this.api().search(
                    sessionErrors = @json(
                        array_map(
                            function($student_id) { return "(?=.*NIM:{$student_id})"; },
                            array_keys(session("errors") ?? [])
                        )
                    ).join("|")
                    ||
                    new URLSearchParams(window.location.search).get("search")
                    ||
                    "",
                true).draw();},
            drawCallback: function (){
                if (preparingDatas){
                    preparingDatas = false;
                    editing = !editing;
                    editButton.textContent = editing ? "Batal Edit" : "Edit";
                    if (editing){
                        submitForm.action = "{{ route('admins.editUsers') }}";
                        const submitButton = document.createElement("button");
                        submitButton.type = "button";
                        submitButton.textContent = "Simpan Perubahan";
                        submitButton.addEventListener("click", function(){
                            oldPageLength = table.page.len();
                            submit = true;
                            table.page.len(-1).search("").draw();});
                        submitEdit.appendChild(submitButton);
                        deletes.forEach(deleteInput => {
                            const checkbox = document.createElement("input");
                            checkbox.type = "checkbox";
                            checkbox.name = "delete__" + deleteInput.getAttribute("userId");
                            checkbox.classList.add("iniForm");
                            checkbox.addEventListener("change", () => {
                                const tr = checkbox.closest("tr");
                                if (checkbox.checked) resetViewsAndForms(tr.querySelectorAll("[oldValue]"), tr);
                                else prepareEditableFields(tr.querySelectorAll("[oldValue]"));});
                            deleteInput.append(checkbox, document.createTextNode("hapus"));});
                        prepareEditableFields(views);}
                    else{
                        submitEdit.innerHTML = "";
                        submitForm.action = "";
                        resetViewsAndForms(views, TBody);}
                    table.page.len(oldPageLength).search(pageSearch).page(currentPage).draw('page');}
                if (submit){
                    submit = false;
                    if (!submitForm.checkValidity()){
                        const invalidField = submitForm.querySelector(":invalid");
                        if (invalidField){
                            const studentId = invalidField.closest("tr").getAttribute("studentId");
                            findingInvalid = true;
                            table.page.len(oldPageLength).search("NIM:" + studentId).draw();
                        }}
                    else submitForm.requestSubmit();}
                if (findingInvalid){
                    findingInvalid = false;
                    submitForm.requestSubmit();
                }
            }
        });

    editButton.addEventListener("click", function (){
        currentPage = table.page.info().page;
        oldPageLength = table.page.len();
        pageSearch = table.search();
        preparingDatas = true;
        table.page.len(-1).search("").draw();});

    document.addEventListener("keydown", function (e){
        if ((e.target.tagName == "INPUT" || e.target.tagName == "SELECT") && e.key == "Enter") e.preventDefault();});

    function prepareEditableFields(viewsCollection){
        viewsCollection.forEach(view => {
            view.style.textDecoration = "underline";
            view.style.cursor = "pointer";
            if (view.hasAttribute("target")) view.removeAttribute("href");
            view.onclick = () => {
                const
                    suppose = view.closest("td").querySelector("a[suppose]"),
                    cancelEdit = document.createElement("u"),
                    userId = suppose.getAttribute("userId"),
                    fieldValue = view.getAttribute("oldValue"),
                    editor = (() => {
                        switch (suppose.getAttribute("suppose")){
                            case "name":
                                return createTextInput(
                                    "text",
                                    fieldValue,
                                    userId,
                                    "name",
                                    /[a-z' ]/i,
                                    true
                                );
                            case "special_role":
                                return (function(currentRole, currentDivision, userId, isCurrentUser){
                                    const
                                        container = document.createElement("div"),
                                        roleSelect = document.createElement("select"),
                                        divisionSelect = document.createElement("select");
                                    
                                    roleSelect.required = false;
                                    divisionSelect.required = false;
                                    
                                    // Role options
                                    [
                                        {value: "", text: "Pengguna", selected: currentRole == ""},
                                        {value: "Pendamping", text: "Pendamping", selected: currentRole == "Pendamping"},
                                        {value: "Admin", text: "Admin", selected: currentRole == "Admin"}
                                    ].forEach(opt => {
                                        const option = document.createElement("option");
                                        option.value = opt.value;
                                        option.textContent = opt.text;
                                        option.selected = opt.selected;
                                        roleSelect.appendChild(option);
                                    });
                                    
                                    // If current user, disable role selection but allow division changes
                                    if (isCurrentUser) {
                                        roleSelect.disabled = true;
                                        roleSelect.style.opacity = "0.6";
                                    }
                                    
                                    updateDivisionSelect(divisionSelect, currentRole, currentDivision);
                                    
                                    // Set initial required state based on current role
                                    if (currentRole === "" || currentRole === null) {
                                        divisionSelect.required = false;
                                    } else {
                                        divisionSelect.required = true;
                                    }
                                    
                                    roleSelect.addEventListener("change", function(){
                                        updateDivisionSelect(divisionSelect, this.value);
                                        // Make division required when special role is selected
                                        if (this.value === "" || this.value === null) {
                                            divisionSelect.required = false;
                                        } else {
                                            divisionSelect.required = true;
                                        }
                                    });
                                    
                                    roleSelect.addEventListener("blur", () => {
                                        if (!isCurrentUser) {
                                            handleBlurEvent(roleSelect, currentRole, "special_role", userId);
                                        }
                                    });
                                    
                                    divisionSelect.addEventListener("blur", () => {
                                        handleBlurEvent(divisionSelect, currentDivision, "division", userId);
                                    });
                                    
                                    container.append(roleSelect, divisionSelect);
                                    return container;
                                })(fieldValue, view.getAttribute("division"), userId, view.hasAttribute("currentUser"));
                            case "gender":{
                                    const container = document.createElement("div");
                                    [
                                        {value: "Laki-Laki", label: "Laki-Laki", checked: fieldValue == "Laki-Laki"},
                                        {value: "Perempuan", label: "Perempuan", checked: fieldValue == "Perempuan"}].forEach(opt => {
                                        const
                                            label = document.createElement("label"),
                                            radio = document.createElement("input");
                                        radio.type = "radio";
                                        radio.required = true;
                                        radio.name = "gender__" + userId;
                                        radio.value = opt.value;
                                        radio.checked = opt.checked;
                                        label.append(radio, document.createTextNode(opt.label + "\n"));
                                        container.appendChild(label);});
                                    return container;
                                }
                            case "student_id":
                                const studentIdInput = createTextInput(
                                    "text",
                                    fieldValue,
                                    userId,
                                    "student_id",
                                    /^[0-9]$/,
                                    true
                                );
                                studentIdInput.minLength = studentIdInput.maxLength = 10;
                                return studentIdInput;
                            case "major_id":
                                return (function(currentFaculty, currentMajor, userId){
                                    const
                                        container = document.createElement("div"),
                                        facultySelect = document.createElement("select"),
                                        majorSelect = document.createElement("select"),
                                        option = document.createElement("option");
                                    option.value = null;
                                    option.textContent = "Pilih Fakultas";
                                    option.selected = "" == currentFaculty;
                                    facultySelect.required = majorSelect.required = option.disabled = option.hidden = true;
                                    facultySelect.appendChild(option);
                                    faculties.forEach(faculty => {
                                        const option = document.createElement("option");
                                        option.value = faculty.id;
                                        option.textContent = faculty.name;
                                        option.selected = faculty.id == currentFaculty;
                                        facultySelect.appendChild(option);});
                                    updateMajorSelect(majorSelect, currentFaculty, currentMajor);
                                    facultySelect.addEventListener("change", function(){updateMajorSelect(majorSelect, this.value);});
                                    majorSelect.addEventListener("blur", () => {handleBlurEvent(majorSelect, currentMajor, "major_id", userId);});
                                    container.append(facultySelect, majorSelect);
                                    return container;
                                })(view.getAttribute("faculty"), fieldValue, userId);
                            case "generation":
                                return createSelectInput(
                                    [
                                        {value: year0, text: year0, selected: fieldValue == year0},
                                        {value: year1, text: year1, selected: fieldValue == year1},
                                        {value: year2, text: year2, selected: fieldValue == year2},
                                        {value: year3, text: year3, selected: fieldValue == year3},
                                        {value: year4, text: year4, selected: fieldValue == year4},
                                        {value: year5, text: year5, selected: fieldValue == year5},
                                        {value: year6, text: year6, selected: fieldValue == year6}],
                                    fieldValue,
                                    userId,
                                    "generation",
                                    true
                                );
                            case "specials":
                                const
                                    container = document.createElement("div"),
                                    select = document.createElement("select"),
                                    selectedSpecials = JSON.parse(view.getAttribute("oldValue") || "[]"),
                                    disabilityOptions = [
                                        {id: 'placeholder1', text: 'Placeholder 1'},
                                        {id: 'placeholder2', text: 'Placeholder 2'}
                                    ];
                                select.name = "specials__" + userId + "[]";
                                select.multiple = select.required = true;
                                select.classList.add("special-select");
                                disabilityOptions.forEach(option => {
                                    const opt = document.createElement("option");
                                    opt.value = option.id;
                                    opt.textContent = option.text;
                                    opt.selected = selectedSpecials.includes(option.text);
                                    select.appendChild(opt);});
                                container.appendChild(select);
                                container.classList.add("iniForm");
                                setTimeout(() => {
                                    $(select).select2();
                                    $(select).val(
                                        selectedSpecials.map(special => {
                                            const found = disabilityOptions.find(opt => opt.text == special);
                                            return found ? found.id : special;
                                        })
                                    ).trigger("change");
                                }, 100);
                                return container;
                            case "whatsapp_number":
                                return createTextInput(
                                    "text",
                                    fieldValue,
                                    userId,
                                    "whatsapp_number",
                                    /^[0-9]$/,
                                    true
                                );
                            case "email":
                                const emailInput = createTextInput(
                                    "email",
                                    fieldValue,
                                    userId,
                                    "email",
                                    null,
                                    true
                                );
                                emailInput.type = "email";
                            return emailInput;
                        }
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
            if (view.hasAttribute("target")) view.setAttribute("href", "https://wa.me/" + view.getAttribute("value"));
            else{
                view.style.textDecoration = "none";
                view.style.cursor = "default";}
            view.onclick = null;
            view.style.display = "";
            const selector = container == TBody ? "[suppose]" : "[suppose]:not([suppose='delete'])";
            container.querySelectorAll(selector).forEach(suppose => {suppose.innerHTML = "";});
        });}
    function createTextInput(type, value, userId, fieldName, keyRegex, isRequired){
        const input = document.createElement("input");
        input.type = type;
        input.required = isRequired;
        input.value = value;
        if (keyRegex){
            input.onkeydown = event => 
                keyRegex.test(event.key) || 
                ["Backspace", "ArrowLeft", "ArrowRight", "Tab", "Delete"].includes(event.key) || 
                (event.ctrlKey && ["a", "c", "v", "x"].includes(event.key.toLowerCase()));
            }
        input.addEventListener("blur", () => {handleBlurEvent(input, value, fieldName, userId);});
        return input;}
    function createSelectInput(options, currentValue, userId, fieldName, isRequired){
        const select = document.createElement("select");
        select.required = isRequired;
        options.forEach(opt => {
            const option = document.createElement("option");
            option.value = opt.value;
            option.textContent = opt.text;
            option.selected = opt.selected || opt.value == currentValue;
            select.appendChild(option);});
        select.addEventListener("blur", () => {handleBlurEvent(select, currentValue, fieldName, userId);});
        return select;}
    function updateDivisionSelect(select, roleValue, selectedDivision = null){
        const divisionOptions = ['Pendampingan','Advokasi','Humas','BPH'];
        
        // Always start with disabled hidden option
        select.innerHTML = "<option disabled selected hidden value=''>Pilih Divisi</option>";
        
        if (roleValue === "" || roleValue === null) {
            // For regular users (Pengguna), keep division select disabled and hidden
            select.disabled = true;
            select.style.display = "none";
            select.required = false;
        } else {
            // For special roles (Pendamping/Admin), show division options and make required
            select.disabled = false;
            select.style.display = "";
            select.required = true;
            
            divisionOptions.forEach(division => {
                const option = document.createElement("option");
                option.value = division;
                option.textContent = division;
                option.selected = division == selectedDivision;
                select.appendChild(option);
            });
        }
    }
    function updateMajorSelect(select, facultyId, selectedMajor = null){
        const filteredMajors = majors[facultyId] || [];
        select.innerHTML = "<option disabled selected hidden value=''>Pilih Program Studi</option>";
        filteredMajors.forEach(major => {
            const option = document.createElement("option");
            option.value = major.id;
            option.textContent = major.name;
            option.selected = major.id == selectedMajor;
            select.appendChild(option);});
        select.disabled = filteredMajors.length == 0;
        if (filteredMajors.length == 1){
            select.selectedIndex = 1;
            select.focus();
        }}
    function handleBlurEvent(element, originalValue, fieldName, userId){
        if (element.value == originalValue) element.removeAttribute("name");
        else element.name = fieldName + "__" + userId;
    }
</script>