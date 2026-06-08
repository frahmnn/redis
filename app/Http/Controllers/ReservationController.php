<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Month;
use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\Reservation;
use App\Models\Log;
use App\Models\Identity;
use App\Mail\NotifyForRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userDisabilityTypes = ($user->identity->specials ?? collect())->pluck("special")->toArray();
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
        if ($monthsOrHolidaysCreated) return redirect()->route('reservations.index');
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
        $availableForUser = [];
        // 1. Full available assistance (all assistants scheduled that day)
        foreach (Schedule::with(["user.identity.specials"])->where("month", sprintf("%02d", $month) . $year)->get() as $schedule) {
            $day = (int) $schedule->day;
            if ($schedule->user->identity->specials->count() > 0) {
                foreach ($schedule->user->identity->specials as $special) {
                    $fullAvailableAssistance[$day][$special->special] = ($fullAvailableAssistance[$day][$special->special] ?? 0) + 1;
                    if (in_array($special->special, $userDisabilityTypes)) $availableForUser[$day] = true;
                }
            }
        }
        foreach ($fullAvailableAssistance as $day => $specialties) ksort($fullAvailableAssistance[$day]);

        // 2. Occupied assistance (assistant scheduled AND has reservation with status 'Menunggu' on that day)
        $monthId = sprintf("%02d", $month) . $year;
        $pendingReservations = Reservation::where("status", "Menunggu")->whereRaw("SUBSTR(dateid,3,6) = ?", [$monthId])->get();
        foreach ($pendingReservations as $reservation) {
            $resDay = (int) substr($reservation->dateid, 0, 2);
            $assistant = $reservation->assistant;
            if ($assistant) {
                $assistantUser = \App\Models\User::with(['identity.specials'])->find($assistant);
                if ($assistantUser && $assistantUser->identity && $assistantUser->identity->specials->count() > 0) {
                    foreach ($assistantUser->identity->specials as $special) {
                        $occupiedAssistance[$resDay][$special->special] = ($occupiedAssistance[$resDay][$special->special] ?? 0) + 1;
                    }
                }
            }
        }

        // For now, use fullAvailableAssistance as availableAssistance (for compatibility)
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
        return view("reservations.index", compact(
            "latestDateThisMonth",
            "firstDayOfMonth",
            "latestDatePrevMonth",
            "sundays",
            "holidays",
            "today",
            "availableAssistance",
            "fullAvailableAssistance",
            "occupiedAssistance",
            "availableForUser",
            "userDisabilityTypes",
            "year",
            "month",
            "currentMonthName"
        ));
    }

    public function make($dateid)
    {
        $user = auth()->user();
        $userDisabilities = $user->identity->specials ?? collect();
        $userDisabilityTypes = $userDisabilities->pluck('special')->toArray();
        
        $day = (int) substr($dateid, 0, 2);
        $month = (int) substr($dateid, 2, 2);
        $year = (int) substr($dateid, 4, 4);
        
        $currentMonth = (int) date("n");
        $currentYear = (int) date("Y");
        $today = (int) date("j");
        
        // Check if date format and values are valid
        $latestDateThisMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if (strlen($dateid) !== 8 || !is_numeric($dateid) || $day < 1 || $day > $latestDateThisMonth) {
            return redirect()->route('reservations.index')->with('error', 'Tanggal tidak valid.');
        }
        
        // Check if it's the current month and year only
        if ($month !== $currentMonth || $year !== $currentYear) {
            return redirect()->route('reservations.index')->with('error', 'Hanya dapat membuat reservasi untuk bulan ini.');
        }
        
        // Check if date is not in the past
        if ($day < $today) {
            return redirect()->route('reservations.index')->with('error', 'Tidak dapat membuat reservasi untuk tanggal yang sudah berlalu.');
        }
        
        // Check if there's availability on this date
        $availableAssistance = [];
        $canUserBeHelped = false;
        foreach (Schedule::with(["user.identity.specials"])->where("month", sprintf("%02d", $month) . $year)->where("day", $day)->get() as $schedule){
            if ($schedule->user->identity->specials->count() > 0) {
                foreach ($schedule->user->identity->specials as $special){
                    $availableAssistance[$day][$special->special] = ($availableAssistance[$day][$special->special] ?? 0) + 1;
                    if (in_array($special->special, $userDisabilityTypes)) $canUserBeHelped = true;
                }
            }
        }
        
        if (!isset($availableAssistance[$day]) || empty($availableAssistance[$day])) {
            return redirect()->route('reservations.index')->with('error', 'Tidak ada pendamping yang tersedia pada tanggal ini.');
        }
        
        // Check if any assistant can help with user's specific disabilities
        if (!$canUserBeHelped && !empty($userDisabilityTypes)) {
            return redirect()->route('reservations.index')->with('error', 'Tidak ada pendamping yang tersedia untuk jenis kebutuhan Anda pada tanggal ini.');
        }
        
        $formattedDate = [
            0 => "Minggu",
            1 => "Senin", 
            2 => "Selasa",
            3 => "Rabu",
            4 => "Kamis",
            5 => "Jumat",
            6 => "Sabtu"][
        date("w", mktime(0, 0, 0, $month, $day, $year))] . ", " . $day . " " . [
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
        ][$month] . " " . $year;

        // Check if user already has a reservation on this date
        $hasReservation = Reservation::where('user_id', $user->id)
            ->where('dateid', $dateid)
            ->exists();
        $reservationNotice = null;
        if ($hasReservation) {
            $reservationNotice = "Catatan: Anda sudah memiliki pengajuan pendampingan pada tanggal ini.";
        }

        return view("reservations.make", [
            "dateid" => $dateid,
            "formattedDate" => $formattedDate,
            "availableAssistance" => $availableAssistance[$day],
            "reservationNotice" => $reservationNotice
        ]);
    }

    public function insert(Request $request, $dateid)
    {
        if ($request->place == "others" && $request->other_place) $request->merge(['place' => $request->other_place]);
        $validated = $request->validate([
            "place" => "required",
            "type" => "required|in:Pendampingan,Advokasi,Humas,Lainnya",
            "description" => "required"
        ], [
            "place.required" => "Tempat harus dipilih atau diisi.",
            "type.required" => "Tipe pendamping harus dipilih.",
            "type.in" => "Tipe pendamping tidak valid.",
            "description.required" => "Keterangan harus diisi.",
        ]);
        if ($request->input("place") == "others" && empty($request->other_place)) return back()->withErrors(["other_place" => "Tempat lainnya harus diisi jika memilih \"Lainnya\"."])->withInput();
        $reservationId = Str::uuid();
        Reservation::create([
            "id" => $reservationId,
            "user_id" => auth()->user()->id,
            "dateid" => $dateid,
            "place" => $validated['place'],
            "type" => $validated['type'],
            "description" => $validated['description'],
            "status" => "Menunggu"
        ]);

        Log::create([
            "id" => Str::uuid(),
            "reservation_id" => $reservationId,
            "log" => "Pengajuan dibuat"
        ]);

        // Send email notifications to all assistants
        $this->sendEmailNotifications($reservationId);

        return redirect()->route("users.reservation", $reservationId)->with("success", "Berhasil ajukan pendampingan.");
    }

    private function sendEmailNotifications($reservationId)
    {
        $reservation = Reservation::with(['user.identity.specials', 'user.identity.major.faculty'])->find($reservationId);
        $day = (int) substr($reservation->dateid, 0, 2);
        $month = (int) substr($reservation->dateid, 2, 2);
        $year = (int) substr($reservation->dateid, 4, 4);
        
        $dayNames = [
            0 => "Minggu", 1 => "Senin", 2 => "Selasa", 3 => "Rabu",
            4 => "Kamis", 5 => "Jumat", 6 => "Sabtu"
        ];
        $monthNames = [
            1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
            5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
            9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
        ];
        
        $formattedDate = $dayNames[date("w", mktime(0, 0, 0, $month, $day, $year))] . ", " . $day . " " . $monthNames[$month] . " " . $year;
        $createdAt = $reservation->created_at;
        $formattedCreatedAt = $dayNames[$createdAt->dayOfWeek] . ", " . $createdAt->day . " " . $monthNames[$createdAt->month] . " " . $createdAt->year . " " . $createdAt->format('H:i:s');
        
        $userDisabilities = $reservation->user->identity->specials->pluck("special")->toArray();
        $reservationType = $reservation->type;
        
        // Get all assistants with their related data in one query
        $assistants = Identity::with(['specials', 'user.schedules' => function($query) use ($month, $year) {
            $query->where("month", sprintf("%02d%04d", $month, $year));
        }])
        ->whereNotNull('special_role')
        ->where('verified', 1)
        ->get();

        // Process emails in bulk
        $emails = [];
        foreach ($assistants as $assistant) {
            $assistantDivision = $assistant->division;
            $specialtyMatches = array_intersect($userDisabilities, $assistant->specials->pluck("special")->toArray());
            
            // Check if scheduled this day
            $userScheduledDays = $assistant->user->schedules->pluck('day')->map(function($day) { return (int) $day; })->toArray();
            $isScheduledThisDay = in_array($day, $userScheduledDays);
            
            // Calculate role match
            $roleMatch = ($assistantDivision && (
                $reservationType === "Lainnya" ||
                $assistantDivision === "BPH" ||
                strtolower($assistantDivision) === strtolower($reservationType)
            )) ? 1 : 0;
            
            $totalRequirements = count($userDisabilities) + 1 + 1;
            $metRequirements = count($specialtyMatches) + ($isScheduledThisDay ? 1 : 0) + $roleMatch;
            
            $requirements = [
                "disabilities" => $userDisabilities,
                "specialty_matches" => $specialtyMatches,
                "is_scheduled_this_day" => $isScheduledThisDay,
                "role_match" => $roleMatch,
                "assistant_division" => $assistantDivision,
                "met_requirements" => $metRequirements,
                "total_requirements" => $totalRequirements
            ];

            $emails[] = [
                'email' => $assistant->email,
                'mail' => new NotifyForRequest($reservation, $assistant, $formattedDate, $formattedCreatedAt, $requirements)
            ];
        }

        // Send all emails (this is still synchronous but optimized)
        foreach ($emails as $emailData) {
            Mail::to($emailData['email'])->send($emailData['mail']);
        }
    }

    public function cancelReservation(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        // Check if user is the requester or the assigned assistant
        $isRequester = $reservation->user_id === auth()->id();
        $isAssignedAssistant = $reservation->assistant === auth()->id();
        if (!$isRequester && !$isAssignedAssistant) {
            return redirect()->route('users.reservations')->with('error', 'Anda tidak memiliki izin untuk membatalkan reservasi ini.');
        }

        // Only allow cancel if status is 'Menunggu'
        if ($reservation->status !== 'Menunggu') {
            return redirect()->route('users.reservations')->with('error', 'Reservasi tidak dapat dibatalkan');
        }

        // Prevent cancellation if today is after the reservation date
        $todayInt = (int) now()->format('Ymd');
        $reservationDateInt = (int) $reservation->dateid;
        if ($todayInt > $reservationDateInt) {
            return redirect()->route('users.reservations')->with('error', 'Tidak dapat membatalkan, tanggal janji sudah lewat.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $reservation->status = 'Dibatalkan';
        $reservation->save();

        // Determine who is canceling based on the user role
        if ($isRequester) {
            $logMessage = 'Pengajuan dibatalkan oleh Pemohon. Alasan: ' . $request->reason;
        } else {
            $logMessage = 'Pengajuan dibatalkan oleh Pendamping. Alasan: ' . $request->reason;
        }

        Log::create([
            'id' => Str::uuid(),
            'reservation_id' => $reservation->id,
            'log' => $logMessage,
        ]);

        return redirect()->route('users.reservations')->with('success', 'Berhasil membatalkan pendampingan.');
    }
}