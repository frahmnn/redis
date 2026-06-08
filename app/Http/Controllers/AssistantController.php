<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Month;
use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Log;
use App\Models\Identity;
use Illuminate\Support\Str;

class AssistantController extends Controller
{
    public function index(){
        $year = date("Y");
        $month = date("n");
        $today = date("j"); // Current day of month (1-31)
        $sundays = [];
        $holidays = [];
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
        if ($monthsOrHolidaysCreated) return redirect()->route("assistants.index");
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
        $pendingReservationsRaw = Reservation::where("status", "Menunggu")->whereRaw("SUBSTR(dateid,3,6) = ?", [$monthId])->get();
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
        $userScheduledDays = [];
        $userSpecialties = [];
        if (auth()->user()->identity->specials->count() > 0){
            $userSpecialties = auth()->user()->identity->specials->pluck('special')->toArray();
            foreach (auth()->user()->schedules()->where("month", sprintf("%02d", $month) . $year)->get() as $schedule){
                $day = (int) $schedule->day;
                $userScheduledDays[] = $day;
            }}
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
        $pendingReservations = [];
        if (!empty($userSpecialties)){  
            $reservationsData = [];
            // Get current assistant's division
            $assistantDivision = auth()->user()->identity->division;
            
            foreach (
                Reservation::select("id", "user_id", "dateid", "place", "type", "created_at")
                    ->with(["user.identity.specials"])
                    ->where("status", "Menunggu")
                    ->whereNull("assistant")
                    ->whereRaw("SUBSTRING(dateid, 3, 2) = ?", [sprintf("%02d", $month)])
                    ->whereRaw("SUBSTRING(dateid, 5, 4) = ?", [$year])
                    ->whereRaw("CAST(SUBSTRING(dateid, 1, 2) AS UNSIGNED) >= ?", [$today])
                    ->get()
                as $reservation
            ){
                if ($reservation->user->identity->specials->count() > 0) {
                    $day = (int) substr($reservation->dateid, 0, 2);
                    $userDisabilities = $reservation->user->identity->specials->pluck("special")->toArray();
                    $specialtyMatches = array_intersect($userDisabilities, $userSpecialties);
                    $specialtyMatchCount = count($specialtyMatches);
                    $isScheduledThisDay = in_array($day, $userScheduledDays);
                    $scheduleMatch = $isScheduledThisDay ? 1 : 0;
                    
                    // Check assistant role/division compatibility with reservation type
                    $roleMatch = 0;
                    $reservationType = $reservation->type;
                    if ($assistantDivision) {
                        if ($reservationType === "Lainnya") {
                            // All divisions get free pass for "Lainnya"
                            $roleMatch = 1;
                        } elseif ($assistantDivision === "BPH") {
                            // BPH is compatible with all types
                            $roleMatch = 1;
                        } elseif (strtolower($assistantDivision) === strtolower($reservationType)) {
                            // Exact match: Pendampingan->Pendampingan, Advokasi->Advokasi, Humas->Humas
                            $roleMatch = 1;
                        }
                    }
                    
                    $totalRequirements = count($userDisabilities) + 1 + 1; // disabilities + schedule + role
                    $metRequirements = $specialtyMatchCount + $scheduleMatch + $roleMatch;
                    $reservationsData[] = [
                        "day" => $day,
                        "data" => [
                            "id" => $reservation->id,
                            "name" => $reservation->user->identity->name,
                            "place" => $reservation->place,
                            "type" => $reservation->type,
                            "created_at" => $reservation->created_at,
                            "disabilities" => $userDisabilities,
                            // Pre-calculated display values
                            "specialty_matches" => $specialtyMatches,
                            "specialty_match_count" => $specialtyMatchCount,
                            "is_scheduled_this_day" => $isScheduledThisDay,
                            "schedule_match" => $scheduleMatch,
                            "role_match" => $roleMatch,
                            "assistant_division" => $assistantDivision,
                            "met_requirements" => $metRequirements,
                            "total_requirements" => $totalRequirements,
                            "color" => ($metRequirements == $totalRequirements) ? "green" : "orange"
                        ],
                        "met_requirements" => $metRequirements,
                        "total_requirements" => $totalRequirements
                    ];
                }}
            usort($reservationsData, function($a, $b){
                if ($a["met_requirements"] !== $b["met_requirements"]) return $b["met_requirements"] - $a["met_requirements"];
                return $a["day"] - $b["day"];});
            $pendingReservations = [];
            foreach ($reservationsData as $reservation) {
                if (!isset($pendingReservations[$reservation["day"]])) $pendingReservations[$reservation["day"]] = [];
                $pendingReservations[$reservation["day"]][] = $reservation["data"];
            }}
        return view("assistants.index", compact(
            "latestDateThisMonth",
            "firstDayOfMonth",
            "latestDatePrevMonth",
            "sundays",
            "holidays",
            "today",
            "availableAssistance",
            "fullAvailableAssistance",
            "occupiedAssistance",
            "userScheduledDays",
            "userSpecialties",
            "pendingReservations",
            "year",
            "currentMonthName"
        ));
    }

    public function schedule(){
        $year = date("Y");
        $month = date("n");
        $today = date("j");
        $sundays = [];
        $holidays = [];
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
        if ($monthsOrHolidaysCreated) return redirect()->route("assistants.schedule");
        $allHolidays = Holiday::whereIn("month", [
            sprintf("%02d", $month) . $year,
            sprintf("%02d", $pastMonth) . $pastMonthYear,
            sprintf("%02d", $nextMonth) . $nextMonthYear
        ])->get();
        foreach ($allHolidays->where("month", sprintf("%02d", $month) . $year) as $holiday) $holidays[(int) substr($holiday->id, 0, 2)] = $holiday->name;
        foreach ($allHolidays->where("month", sprintf("%02d", $pastMonth) . $pastMonthYear) as $holiday) $holidays[(int) substr($holiday->id, 0, 2) - $latestDatePrevMonth - 1] = $holiday->name;
        foreach ($allHolidays->where("month", sprintf("%02d", $nextMonth) . $nextMonthYear) as $holiday) $holidays[$latestDateThisMonth + (int) substr($holiday->id, 0, 2)] = $holiday->name;
        $userSchedules = [];
        foreach (auth()->user()->schedules()->where("month", sprintf("%02d", $month) . $year)->get() as $schedule) $userSchedules[] = $schedule->day;
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
        ][$month];
        return view("assistants.schedule", compact("latestDateThisMonth", "firstDayOfMonth", "latestDatePrevMonth", "sundays", "holidays", "today", "userSchedules", "year", "currentMonthName"));
    }

    public function editSchedules(Request $request){
        $currentMonth = date("mY");
        $currentYear = date("Y");
        $currentMonthNum = date("n");
        $today = date("j");
        $latestDateThisMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonthNum, $currentYear);
        $errors = [];
        $validDays = [];
        foreach ($request->except("_token") as $dayString => $value){
            $day = (int) $dayString;
            if ($day < 1 || $day > $latestDateThisMonth){
                $errors[$dayString] = "Tanggal $day tidak valid untuk bulan ini.";
                continue;}
            if ($day < $today){
                $errors[$dayString] = "Tidak dapat memilih tanggal $day karena sudah berlalu.";
                continue;}
            $validDays[] = $day;}
        if (!empty($errors)) return redirect()->route("assistants.schedule")->with("errors", $errors);
        $requestedDays = array_map(fn($day) => sprintf("%02d", $day), $validDays);
        $existingDays = auth()->user()->schedules()->where("month", $currentMonth)->get()->pluck("day")->toArray();
        $daysToAdd = array_diff($requestedDays, $existingDays);
        $daysToRemove = array_diff($existingDays, $requestedDays);
        if (!empty($daysToAdd)){
            $now = now();
            foreach ($daysToAdd as $day){
                $addedSchedule[] = [
                    "id" => Str::uuid(),
                    "user_id" => auth()->user()->id,
                    "month" => $currentMonth,
                    "day" => $day,
                    "created_at" => $now,
                    "updated_at" => $now
                ];}
            Schedule::insert($addedSchedule ?? []);}
        if (!empty($daysToRemove)){
            auth()
                ->user()
                ->schedules()
                ->where("month", $currentMonth)
                ->whereIn("day", $daysToRemove)
                ->delete()
            ;}
        return redirect()->route("assistants.schedule")->with("success", "Jadwal berhasil diperbarui.");
    }

    public function request($id){
        $reservation = Reservation::with(["user.identity.specials", "user.identity.major.faculty"])->find($id);
        
        if (!$reservation) {
            abort(404, 'Reservation not found');
        }
        
        // Get assistant's specialties and division
        $userSpecialties = auth()->user()->identity->specials->pluck('special')->toArray();
        $assistantDivision = auth()->user()->identity->division;
        
        // Get user's disabilities
        $userDisabilities = $reservation->user->identity->specials->pluck("special")->toArray();
        
        // Calculate requirements
        $day = (int) substr($reservation->dateid, 0, 2);
        $month = (int) substr($reservation->dateid, 2, 2);
        $year = (int) substr($reservation->dateid, 4, 4);

        // Create formatted date
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
        
        // Get scheduled days for current month
        $userScheduledDays = auth()->user()->schedules()
            ->where("month", sprintf("%02d%04d", $month, $year))
            ->pluck('day')
            ->map(function($day) { return (int) $day; })
            ->toArray();
        
        // Calculate matches
        $specialtyMatches = array_intersect($userDisabilities, $userSpecialties);
        $specialtyMatchCount = count($specialtyMatches);
        $isScheduledThisDay = in_array($day, $userScheduledDays);
        $scheduleMatch = $isScheduledThisDay ? 1 : 0;
        
        // Check assistant role/division compatibility with reservation type
        $roleMatch = 0;
        $reservationType = $reservation->type;
        if ($assistantDivision) {
            if ($reservationType === "Lainnya") {
                // All divisions get free pass for "Lainnya"
                $roleMatch = 1;
            } elseif ($assistantDivision === "BPH") {
                // BPH is compatible with all types
                $roleMatch = 1;
            } elseif (strtolower($assistantDivision) === strtolower($reservationType)) {
                // Exact match: Pendampingan->Pendampingan, Advokasi->Advokasi, Humas->Humas
                $roleMatch = 1;
            }
        }
        
        $totalRequirements = count($userDisabilities) + 1 + 1; // disabilities + schedule + role
        $metRequirements = $specialtyMatchCount + $scheduleMatch + $roleMatch;
        
        // Format date for display in Indonesian
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
        ];
        
        $dayOfWeek = date('w', mktime(0, 0, 0, $month, $day, $year));
        $formattedDate = $dayNames[$dayOfWeek] . ", " . $day . " " . $monthNames[$month] . " " . $year;
        
        // Format created_at timestamp for display
        $createdAt = $reservation->created_at;
        $createdDay = $createdAt->day;
        $createdMonth = $createdAt->month;
        $createdYear = $createdAt->year;
        $createdDayOfWeek = $createdAt->dayOfWeek;
        $createdTime = $createdAt->format('H:i:s');
        $formattedCreatedAt = $dayNames[$createdDayOfWeek] . ", " . $createdDay . " " . $monthNames[$createdMonth] . " " . $createdYear . " " . $createdTime;
        
        // Add calculated data to reservation
        $reservation->date = $formattedDate;
        $reservation->formatted_created_at = $formattedCreatedAt;
        $reservation->name = $reservation->user->identity->name;
        $reservation->gender = $reservation->user->identity->gender;
        $reservation->major_name = $reservation->user->identity->major->name ?? 'Tidak Ada';
        $reservation->faculty_name = $reservation->user->identity->major->faculty->name ?? 'Tidak Ada';
        $reservation->generation = $reservation->user->identity->generation;
        $reservation->whatsapp_number = $reservation->user->identity->whatsapp_number;
        $reservation->email = $reservation->user->identity->email;
        $reservation->disabilities = $userDisabilities;
        $reservation->specialty_matches = $specialtyMatches;
        $reservation->specialty_match_count = $specialtyMatchCount;
        $reservation->is_scheduled_this_day = $isScheduledThisDay;
        $reservation->schedule_match = $scheduleMatch;
        $reservation->role_match = $roleMatch;
        $reservation->assistant_division = $assistantDivision;
        $reservation->met_requirements = $metRequirements;
        $reservation->total_requirements = $totalRequirements;

        // Get today's date for comparison
        $today = date("dmY");
        $isToday = ($reservation->dateid === $today);
        
        return view("assistants.request", compact("reservation", "isToday", "formattedDate"));
    }

    public function takeReservation($id){
        $reservation = Reservation::find($id);
        if ($reservation->assistant) return redirect()->route("assistants.index")->with("error", "Janji ini sudah diambil oleh pendamping lain.");
        
        $reservationDay = (int) substr($reservation->dateid, 0, 2);
        $reservationMonth = (int) substr($reservation->dateid, 2, 2);
        $reservationYear = (int) substr($reservation->dateid, 4, 4);
        $today = date("j");
        $currentMonth = date("n");
        $currentYear = date("Y");
        
        if ($reservation->status != "Menunggu" || 
            $reservationYear < $currentYear || 
            ($reservationYear == $currentYear && $reservationMonth < $currentMonth) ||
            ($reservationYear == $currentYear && $reservationMonth == $currentMonth && $reservationDay < $today)) {
            return redirect()->route("assistants.index")->with("error", "Janji ini tidak dalam status yang dapat diambil.");
        }
        
        $reservation->assistant = auth()->user()->id;
        $reservation->save();

         Log::create([
            "id" => Str::uuid(),
            "reservation_id" => $reservation->id,
            "log" => "Pengajuan diambil oleh " . auth()->user()->identity->name,
        ]);

        return redirect()->route("users.reservation", $reservation->id)->with("success", "Reservasi berhasil diambil.");
    }

    public function completeReservation(Request $request, $id){
        $replacementStudentId = $request->input('replacement_studentid');
        $reservation = Reservation::find($id);
        // Only allow complete if status is 'Menunggu'
        if ($reservation->status !== 'Menunggu') {
            return back()->withErrors(['complete' => 'Reservasi tidak dapat diselesaikan.']);
        }
        if ($replacementStudentId) {
            $validated = $request->validate([
                'replacement_studentid' => [
                    'string',
                    'regex:/^\\d{10}$/',
                ],
            ], [
                'replacement_studentid.regex' => 'NIM harus terdiri dari 10 digit angka.'
            ]);

            $replacementIdentity = Identity::where('student_id', $replacementStudentId)
                ->whereNotNull('special_role')
                ->first();
            if (!$replacementIdentity) {
                return back()->withErrors(['replacement_studentid' => 'NIM tidak valid atau bukan milik pendamping yang sah.']);
            }
            $reservation->assistant = $replacementIdentity->user_id;
        } else {
            // Only the assigned assistant can complete if not using replacement
            if ($reservation->assistant !== auth()->user()->id) {
                return back()->withErrors(['complete' => 'Anda bukan pendamping yang ditugaskan untuk janji ini.']);
            }
        }
        // Mark the reservation as complete
        $reservation->status = 'Selesai';
        $reservation->save();

        Log::create([
            "id" => Str::uuid(),
            "reservation_id" => $reservation->id,
            "log" => $replacementStudentId
                ? ("Pendampingan diselesaikan oleh pendamping pengganti: " . $replacementIdentity->name)
                : "Pendampingan Selesai"
        ]);

        return redirect()->route('users.reservations')->with('success', 'Pendampingan berhasil diselesaikan.');
    }
}