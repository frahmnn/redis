<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Identity;
use App\Models\Major;
use App\Models\Faculty;
use App\Models\Month;
use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller {
    // ...existing code...

    public function changeContactPerson(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_number' => [
                'required',
                'string',
                'regex:/^\d+$/',
            ],
        ], [
            'whatsapp_number.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
        ]);

        $number = $validated['whatsapp_number'];
        // Enforce 62-format
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (!str_starts_with($number, '62')) {
            $number = '62' . ltrim($number, '0');
        }

        $contactPerson = \App\Models\ContactPerson::find(1);
        if (!$contactPerson) {
            $contactPerson = new \App\Models\ContactPerson();
            $contactPerson->id = 1;
        }
        $contactPerson->whatsapp_number = $number;
        $contactPerson->save();

        return redirect()->route('admins.index')->with('success_contact_person', 'Nomor WhatsApp Contact Person berhasil diperbarui.');
    }
    public function index(){
        $counts = Identity::selectRaw("
            SUM(verified = 1) as total_users,
            SUM(verified = 1 AND special_role IS NULL) as users,
            SUM(verified = 1 AND special_role IS NOT NULL) as staffs,
            SUM(verified = 0) as unverified_users
        ")->first();
        $contact_person = \App\Models\ContactPerson::find(1);
        return view("admins.index", compact('counts', 'contact_person'));
    }

    public function users(){        
        $faculties = Faculty::with([
            "majors" => function ($major){ $major->orderBy("created_at"); }
        ])->orderBy("created_at")->get();
        return view("admins.users", [
            "users" =>      Identity::with(["major.faculty", "specials"])->select(
                                "id",
                                "name",
                                "gender",
                                "special_role",
                                "division",
                                "student_id",
                                "major_id",
                                "generation",
                                "whatsapp_number",
                                "email"
                            )->where("verified", 1)->orderBy("name")->get(),
            "faculties" =>  $faculties->map(function ($faculty){
                                return [
                                    "id" => $faculty->id,
                                    "name" => $faculty->name,
                                ];
                            }),
            "majors" =>     $faculties->mapWithKeys(function ($faculty){
                                return [
                                    $faculty->id => $faculty->majors->map(function ($major){
                                        return [
                                            "id" =>     $major->id,
                                            "name" =>   $major->name,
                                        ];
                                    })->values(),
                                ];
                            })
        ]);
    }

    public function editUsers(Request $request){
        foreach ($request->except("_token") as $name => $value){
            [$column, $uuid] = explode("__", $name);
            switch ($column){
                case "delete": $deletes[] = Identity::find($uuid)->user_id;break;
                case "specials": $updates[$uuid][$column] = is_array($value) ? $value : [$value];break;
                default: $updates[$uuid][$column] = $value;
            }}
        if (isset($deletes)) User::destroy($deletes);
        foreach ($updates ?? [] as $uuid => $fields){
            $identity = Identity::find($uuid);
            $validData = [];
            foreach ($fields as $field => $value){
                $rules = match ($field){
                    "name" => [
                        "string",
                        "regex:/^[a-zA-Z\' ]{1,255}$/"],
                    "special_role" => [
                        "nullable",
                        "string",
                        "in:Pendamping,Admin"],
                    "division" => [
                        "nullable",
                        "string",
                        "in:Pendampingan,Advokasi,Humas,BPH"],
                    "gender" => [
                        "string",
                        "regex:/^(Laki-Laki|Perempuan)$/"],
                    "student_id" => [
                        "string",
                        "regex:/^\d{10}$/",
                        Rule::unique("identities", "student_id")->ignore($uuid)],
                    "major_id" => [
                        "string",
                        "required",
                        "exists:majors,id"],
                    "generation" => [
                        "string",
                        "regex:/^\d{4}$/"],
                    "whatsapp_number" => [
                        "string",
                        "regex:/^\d+$/"],
                    "specials" => [
                        "array",
                        "required",
                        Rule::in(['placeholder1', 'placeholder2'])],
                    "email" => [
                        "required",
                        "email"
                    ]};
                $messages = [
                    "name.regex" =>             "Nama wajib diisi, hanya boleh berisi huruf, spasi, tanda petik, dan maksimal 255 karakter.",
                    "special_role.in" =>        "Izin Akses harus salah satu dari pilihan yang disediakan.",
                    "division.in" =>            "Divisi harus salah satu dari pilihan yang disediakan.",
                    "gender.regex" =>           "Jenis kelamin wajib dipilih dan harus salah satu dari pilihan yang disediakan.",
                    "student_id.regex" =>       "NIM wajib diisi dan harus terdiri dari 10 angka.",
                    "student_id.unique" =>      "NIM sudah dipakai oleh akun lain.",
                    "major_id.required" =>      "Program studi wajib dipilih.",
                    "major_id.exists" =>        "Program studi tidak ditemukan.",
                    "generation.regex" =>       "Tahun Angkatan wajib diisi dan harus terdiri dari 4 digit.",
                    "whatsapp_number.regex" =>  "Nomor WhatsApp wajib diisi dan hanya boleh berisi angka.",
                    "specials.required" =>      "Informasi tentang disabilitas/spesialisasi wajib diisi.",
                    "specials.in" =>            "Disabilitas/Spesialisasi harus setidaknya satu dari pilihan yang disediakan.",
                    "email.required" =>         "Email wajib diisi.",
                    "email.email" =>            "Format email tidak valid."];
                $validator = Validator::make(
                    [$field => $value],
                    [$field => $rules],
                    $messages);
                if ($validator->fails()) $errors[$identity->student_id][$field] = $validator->errors()->first($field);
                else $validData[$field] = $value;}
            
            // Custom business logic validation for special_role and division relationship
            if (isset($validData["special_role"]) || isset($validData["division"])) {
                $currentRole = $validData["special_role"] ?? $identity->special_role;
                $currentDivision = $validData["division"] ?? $identity->division;
                
                // If role is changed to empty/null (pengguna biasa), automatically nullify division
                if (isset($validData["special_role"]) && empty($validData["special_role"])) {
                    $validData["division"] = null;
                }
                // If role is empty/null, division must also be empty/null
                elseif (empty($currentRole) && !empty($currentDivision)) {
                    $errors[$identity->student_id]["division"] = "Divisi harus kosong untuk pengguna reguler.";
                }
                // If role is not empty, division can be anything (including empty)
                // No additional validation needed for this case
            }
            
            if (isset($validData["specials"])){
                $currentSpecials = $identity->specials->pluck("special")->toArray();
                $specialsToRemove = array_diff($currentSpecials, $validData["specials"]);
                if (!empty($specialsToRemove))$identity->specials()->whereIn("special", $specialsToRemove)->delete();
                foreach (array_diff($validData["specials"], $currentSpecials) as $special){
                    $identity->specials()->create([
                        "id" =>             Str::uuid(),
                        "identity_id" =>    $identity->id,
                        "special" =>        $special
                    ]);}
                unset($validData["specials"]);}
            if (!empty($validData)){
                if (isset($validData["whatsapp_number"]) && str_starts_with($validData["whatsapp_number"], "0")) $validData["whatsapp_number"] = "62" . substr($validData["whatsapp_number"], 1);
                $dirtyData = collect($validData)->filter(fn($val, $key) => $identity->{$key} != $val);
                if ($dirtyData->isNotEmpty()) $identity->update($dirtyData->all());
            }}
        if (isset($errors))return back()->with(["errors" => $errors]);
        return redirect()->route("admins.users")->with("success", "Data berhasil diperbarui.");
    }

    public function verify(){
        return view("admins.verify", [
            "users" =>  Identity::with(["major.faculty", "specials"])
                ->select(
                    "id",
                    "name",
                    "gender",
                    "student_id",
                    "major_id",
                    "generation",
                    "whatsapp_number",
                    "email"
                )
                ->where("verified", 0)
                ->whereNotNull("requested")
                ->get()
        ]);
    }

    public function setVerify(Request $request){
        $identity = Identity::find($request->user);
        $identity->update(["verified" => 1]);
        return redirect()->route("admins.verify")->with("success", $identity->name . " berhasil diverifikasi.");
    }

    public function faculties(){
        $faculties = Faculty::select("id", "name", "created_at")
            ->orderBy("created_at")
            ->get();
        $users = Identity::join("majors", "identities.major_id", "=", "majors.id")
            ->where("identities.verified", 1)
            ->select("identities.id", "identities.special_role", "majors.faculty_id")
            ->get()
            ->groupBy("faculty_id");
        $faculties->each(function ($faculty) use ($users){
            $facultyIdentities = $users->get($faculty->id, collect());
            $faculty->users = $facultyIdentities->whereNull('special_role')->count();
            $faculty->staffs = $facultyIdentities->whereNotNull('special_role')->count();});
        return view("admins.faculties", [
            "faculties" => $faculties
        ]);
    }

    public function faculty($facultyName){
        $faculty = Faculty::where("name", $facultyName)
            ->select("id", "name", "created_at")
            ->first();
        $users = Identity::join("majors", "identities.major_id", "=", "majors.id")
            ->where("identities.verified", 1)
            ->where("majors.faculty_id", $faculty->id)
            ->select("identities.id", "identities.special_role", "majors.id as major_id")
            ->get()
            ->groupBy("major_id");
        return view("admins.faculty", [
            "faculty" => $faculty,
            "majors" =>
                Major::where("faculty_id", $faculty->id)
                    ->orderBy("created_at")
                    ->get()
                    ->each(function ($major) use ($users) {
                        $majorIdentities = $users->get($major->id, collect());
                        $major->users = $majorIdentities->whereNull('special_role')->count();
                        $major->staffs = $majorIdentities->whereNotNull('special_role')->count();
                    })
        ]);
    }

    public function editFaculty(Request $request, $facultyName){
        Faculty::where("name", $facultyName)->update(["name" => $request->name]);
        return redirect()->route("admin.faculty", $request->name)->with("success", "Fakultas berhasil diperbarui.");
    }

    public function schedules($monthid){
        $month = (int) substr($monthid, 0, 2);
        $year = (int) substr($monthid, 2, 4);
        // Prevent access to future months
        $nowYear = (int) date('Y');
        $nowMonth = (int) date('n');
        if ($year > $nowYear || ($year == $nowYear && $month > $nowMonth)) {
            abort(403, 'Tidak boleh mengakses data bulan mendatang.');
        }
        // Get all users with special_role != null and their count of available schedules for this month
        $assistants = \App\Models\User::with(['identity.specials', 'schedules'])
            ->whereHas('identity', function($q) {
                $q->whereNotNull('special_role');
            })
            ->get();

        $assistantSchedules = [];
        foreach ($assistants as $assistant) {
            $count = \App\Models\Schedule::where('user_id', $assistant->id)
                ->where('month', sprintf('%02d', $month) . $year)
                ->count();
            $specializations = $assistant->identity && $assistant->identity->specials ? $assistant->identity->specials->pluck('special')->toArray() : [];
            $assistantSchedules[] = [
                'name' => $assistant->identity->name ?? '-',
                'specializations' => $specializations,
                'count' => $count
            ];
        }
        $month = (int) substr($monthid, 0, 2);
        $year = (int) substr($monthid, 2, 4);
        $today = date("j");
        $currentMonth = date("n");
        $currentYear = date("Y");
        $sundays = [];
        $holidays = [];

        // Fetch all non-pending reservations for this month
        $nonPendingReservations = Reservation::with([
            'user.identity',
            'assistantUser.identity',
        ])->whereRaw('SUBSTR(dateid,3,6) = ?', [sprintf('%02d', $month) . $year])
        ->where(function($query) {
            $query->whereNotIn('status', ['Menunggu'])
                ->orWhere(function($q) {
                    $q->where('status', 'Menunggu')
                    ->where(function($qq) {
                        $qq->whereNotNull('assistant')
                            ->orWhereRaw("STR_TO_DATE(dateid, '%d%m%Y') < CURDATE()");
                    });
                });
        })
        ->orderBy('dateid', 'asc')
        ->get();
        $pastMonth = $month == 1 ? 12 : $month - 1;
        $pastMonthYear = $month == 1 ? $year - 1 : $year;
        $nextMonth = $month == 12 ? 1 : $month + 1;
        $nextMonthYear = $month == 12 ? $year + 1 : $year;
        $latestDateThisMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDayOfMonth = date('w', mktime(0, 0, 0, $month, 1, $year));
        $latestDatePrevMonth = cal_days_in_month(CAL_GREGORIAN, ($month == 1 ? 12 : $month - 1), ($month == 1 ? $year - 1 : $year));
        for ($offset = ($firstDayOfMonth == 0 ? 0 : (7 - $firstDayOfMonth)) - 7; $offset < 42; $offset += 7) $sundays[] = $offset + 1;
        $monthsToCreate = [];
        $holidaysToCreate = [];
        foreach ([
            [
                sprintf("%02d", $month) . $year,
                $month,
                $year],
            [
                sprintf("%02d", $pastMonth) . $pastMonthYear,
                $pastMonth,
                $pastMonthYear],
            [
                sprintf("%02d", $nextMonth) . $nextMonthYear,
                $nextMonth,
                $nextMonthYear
            ]]
        as [$monthId, $monthNum, $yearNum]){
            if (!Month::find($monthId)){
                $monthsToCreate[] = ["id" => $monthId];
                $publicHolidays = array_filter(json_decode(file_get_contents("https://calendarific.com/api/v2/holidays?api_key=" . env('CALENDARIFIC_API_KEY') . "&country=ID&year=" . $yearNum . "&month=" . $monthNum . "&language=id"), true)["response"]["holidays"] ?? [], function($holiday){
                    return $holiday["primary_type"] == "Public Holiday";});
                foreach ($publicHolidays as $holiday) {
                    $holidaysToCreate[] = [
                        "id" => sprintf("%02d", $holiday["date"]["datetime"]["day"]) . $monthId,
                        "month" => $monthId,
                        "name" => $holiday["name"]
                    ];
                }
            }}
        $monthsOrHolidaysCreated = false;
        if (!empty($monthsToCreate)){
            $now = now();
            foreach ($monthsToCreate as &$month){
                $month["created_at"] = $now;
                $month["updated_at"] = $now;}
            Month::insert($monthsToCreate);
            $monthsOrHolidaysCreated = true;}
        if (!empty($holidaysToCreate)){
            $now = now();
            foreach ($holidaysToCreate as &$holiday){
                $holiday["created_at"] = $now;
                $holiday["updated_at"] = $now;}
            Holiday::insert($holidaysToCreate);
            $monthsOrHolidaysCreated = true;}
        if ($monthsOrHolidaysCreated) return redirect()->route('admins.schedules', $monthid);
        $allHolidays = Holiday::whereIn("month", [
            sprintf("%02d", $month) . $year,
            sprintf("%02d", $pastMonth) . $pastMonthYear,
            sprintf("%02d", $nextMonth) . $nextMonthYear
        ])->get();
        foreach ($allHolidays->where("month", sprintf("%02d", $month) . $year) as $holiday) $holidays[(int) substr($holiday->id, 0, 2)] = $holiday->name;
        foreach ($allHolidays->where("month", sprintf("%02d", $pastMonth) . $pastMonthYear) as $holiday) $holidays[(int) substr($holiday->id, 0, 2) - $latestDatePrevMonth - 1] = $holiday->name;
        foreach ($allHolidays->where("month", sprintf("%02d", $nextMonth) . $nextMonthYear) as $holiday) $holidays[$latestDateThisMonth + (int) substr($holiday->id, 0, 2)] = $holiday->name;
        $fullAvailableAssistance = [];
        $occupiedAssistance = [];
        foreach (Schedule::with(["user.identity.specials"])->where("month", sprintf("%02d", $month) . $year)->get() as $schedule){
            $day = (int) $schedule->day;
            if ($schedule->user->identity->specials->count() > 0) {
                foreach ($schedule->user->identity->specials as $special) {
                    $fullAvailableAssistance[$day][$special->special] = ($fullAvailableAssistance[$day][$special->special] ?? 0) + 1;
                }
            }
        }
        foreach ($fullAvailableAssistance as $day => $specialties) ksort($fullAvailableAssistance[$day]);
        // Occupied assistance: assistants scheduled and have reservation with status 'Menunggu' on that day
        $monthId = sprintf("%02d", $month) . $year;
        $pendingReservationsRaw = \App\Models\Reservation::where("status", "Menunggu")->whereRaw("SUBSTR(dateid,3,6) = ?", [$monthId])->get();
        foreach ($pendingReservationsRaw as $reservation) {
            $resDay = (int) substr($reservation->dateid, 0, 2);
            $assistant = $reservation->assistant;
            if ($assistant) {
                $assistantUser = User::with(['identity.specials'])->find($assistant);
                if ($assistantUser && $assistantUser->identity && $assistantUser->identity->specials->count() > 0) {
                    foreach ($assistantUser->identity->specials as $special) {
                        $occupiedAssistance[$resDay][$special->special] = ($occupiedAssistance[$resDay][$special->special] ?? 0) + 1;
                    }
                }
            }
        }
        // For compatibility
        $availableAssistance = $fullAvailableAssistance;
        
        $currentMonthName = [
            1 =>    "Januari",
            2 =>    "Februari",
            3 =>    "Maret",
            4 =>    "April",
            5 =>    "Mei",
            6 =>    "Juni",
            7 =>    "Juli",
            8 =>    "Agustus",
            9 =>    "September",
            10 =>   "Oktober",
            11 =>   "November",
            12 =>   "Desember"
        ][(int) $month];
        
        return view("admins.schedules", compact(
            "latestDateThisMonth",
            "firstDayOfMonth",
            "latestDatePrevMonth",
            "sundays",
            "holidays",
            "today",
            "availableAssistance",
            "fullAvailableAssistance",
            "occupiedAssistance",
            "month",
            "year",
            "monthid",
            "currentMonthName",
            "nonPendingReservations",
            "assistantSchedules",
            "assistants"
        ));
    }

    public function reservation($id)
    {
        $reservation = Reservation::with([
            'logs',
            'assistantUser.identity.major.faculty',
            'user.identity.specials',
            'user.identity.major.faculty',
        ])->find($id);

        if (!$reservation) {
            abort(404, 'Reservation not found');
        }

        $day = (int) substr($reservation->dateid, 0, 2);
        $month = (int) substr($reservation->dateid, 2, 2);
        $year = (int) substr($reservation->dateid, 4, 4);

        $dayNames = [
            0 => "Minggu",
            1 => "Senin",
            2 => "Selasa",
            3 => "Rabu",
            4 => "Kamis",
            5 => "Jumat",
            6 => "Sabtu"
        ];
        $monthNames = [
            1 => "Januari",
            2 => "Februari",
            3 => "Maret",
            4 => "April",
            5 => "Mei",
            6 => "Juni",
            7 => "Juli",
            8 => "Agustus",
            9 => "September",
            10 => "Oktober",
            11 => "November",
            12 => "Desember"
        ];
        $formattedDate = $dayNames[date("w", mktime(0, 0, 0, $month, $day, $year))] . ", " . $day . " " . $monthNames[$month] . " " . $year;
        $today = date("dmY");

        return view("admins.reservation", compact("reservation", "formattedDate", "today"));
    }
    
    public function newMajor($faculty){
        return view("admins.newmajor")->with(["faculty" => $faculty]);
    }

    public function newFaculty(){
        return view("admins.newfaculty");
    }
}