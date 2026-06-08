<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Identity;
use App\Models\Faculty;
use App\Models\Special;
use App\Models\Reservation;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function login()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        $finduser = User::find($user->id);
     
        if($finduser){

            Auth::login($finduser);
            return redirect()->route("index");
     
        }
        else{
            $newUser = User::updateOrCreate(["id" => $user->id]);
            Identity::create([
                "id" =>         Str::uuid(),
                "user_id" =>    $user->id,
                "name" =>       $user->name,
                "email" =>      $user->email,
                "verified" =>   0
            ]);
     
            Auth::login($newUser);
            return redirect()->route("users.verify");
    
        }
    }

    public function verify(){
        $faculties = Faculty::with([
            "majors" => function ($major){ $major->orderBy("created_at"); }
        ])->orderBy("created_at")->get();

        return view("users.verify", [
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

    public function sendVerification(Request $request){
        $validatedRequest = $request->validate(
            [
                "name" => [
                    "string",
                    "regex:/^[a-zA-Z\' ]{1,255}$/"],
                "gender" => [
                    "string",
                    "regex:/^(Laki-Laki|Perempuan)$/"],
                "student_id" => [
                    "string",
                    "regex:/^\d{10}$/",
                    Rule::unique("identities", "student_id")->ignore(auth()->user()->identity->id)],
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
                    Rule::in(["placeholder1", "placeholder2"])],
                "email" => [
                    "email",
                    "required"
                ]],
            [
                "name.regex" =>             "Nama wajib diisi, hanya boleh berisi huruf, spasi, tanda petik, dan maksimal 255 karakter.",
                "gender.regex" =>           "Jenis kelamin wajib dipilih dan harus salah satu dari pilihan yang disediakan.",
                "student_id.regex" =>       "NIM wajib diisi dan harus terdiri dari 10 angka.",
                "student_id.unique" =>      "NIM sudah dipakai oleh akun lain.",
                "major_id.required" =>      "Program studi wajib dipilih.",
                "major_id.exists" =>        "Program studi tidak ditemukan.",
                "generation.regex" =>       "Tahun Angkatan wajib diisi dan harus terdiri dari 4 digit.",
                "whatsapp_number.regex" =>  "Nomor WhatsApp wajib diisi dan hanya boleh berisi angka.",
                "specials.required" =>      "Informasi tentang disabilitas wajib diisi.",
                "specials.in" =>            "Disabilitas harus salah satu dari pilihan yang disediakan.",
                "email.required" =>         "Email wajib diisi.",
                "email.email" =>            "Format email tidak valid."
            ]);
        if (str_starts_with($validatedRequest["whatsapp_number"], "0")) $validatedRequest["whatsapp_number"] = "62" . substr($validatedRequest["whatsapp_number"], 1);
        $currentSpecials = auth()->user()->identity->specials->pluck("special")->toArray();
        $specialsToRemove = array_diff($currentSpecials, $validatedRequest["specials"]);
        if (!empty($specialsToRemove)) auth()->user()->identity->specials()->whereIn("special", $specialsToRemove)->delete();
        foreach (array_diff($validatedRequest['specials'], $currentSpecials) as $special) {
            Special::create([
                "id" =>         Str::uuid(),
                "identity_id"=> auth()->user()->identity->id,
                "special" =>    $special
            ]);}
        unset($validatedRequest["specials"]);
        $validatedRequest["requested"] = now();
        Identity::where("user_id", auth()->id())->update($validatedRequest);
        return back();
    }

    public function reservations()
    {
        if (auth()->user()->identity->special_role == null){
            $reservations = auth()->user()->reservations()
                ->with('assistantUser.identity')
                ->select("id", "dateid", "place", "type", "status", "assistant")
                ->orderBy("created_at", "desc")
                ->get();
            $todayYmd = (int) date('Ymd');
            foreach ($reservations as $reservation) {
                if ($reservation->status === 'Menunggu') {
                    $resYmd = (int) (substr($reservation->dateid, 4, 4) . substr($reservation->dateid, 2, 2) . substr($reservation->dateid, 0, 2));
                    if ($resYmd < $todayYmd) {
                        $reservation->visual_status = 'Kedaluwarsa';
                    } else {
                        $reservation->visual_status = $reservation->status;
                    }
                } else {
                    $reservation->visual_status = $reservation->status;
                }
            }
            return view("users.reservations", compact("reservations"));
        } else {
            // Get reservations where the assistant is assigned to the current user
            $reservations = Reservation::with('user.identity')
                ->where("assistant", auth()->id())
                ->select("id", "dateid", "place", "type", "status", "user_id")
                ->orderBy("created_at", "desc")
                ->get();
            $todayYmd = (int) date('Ymd');
            foreach ($reservations as $reservation) {
                if ($reservation->status === 'Menunggu') {
                    $resYmd = (int) (substr($reservation->dateid, 4, 4) . substr($reservation->dateid, 2, 2) . substr($reservation->dateid, 0, 2));
                    if ($resYmd < $todayYmd) {
                        $reservation->visual_status = 'Kedaluwarsa';
                    } else {
                        $reservation->visual_status = $reservation->status;
                    }
                } else {
                    $reservation->visual_status = $reservation->status;
                }
            }
            return view("assistants.reservations", compact("reservations"));
        }
    }

    public function reservation($id)
    {
        $reservation = Reservation::with(['logs', 'assistantUser.identity.major.faculty', 'user.identity.specials'])->find($id);
        
        $day = (int) substr($reservation->dateid, 0, 2);
        $month = (int) substr($reservation->dateid, 2, 2);
        $year = (int) substr($reservation->dateid, 4, 4);

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

        // Get today's date for comparison
        $today = date("dmY");

        if (auth()->user()->identity->special_role){
            if ($reservation->assistant !== auth()->id()) abort(404, "Reservasi tidak ditemukan.");
            
            return view("assistants.reservation", compact("reservation", "formattedDate", "today"))->with("success", "Berhasil ambil jadwal pendampingan");
        } else {
            if ($reservation->user_id !== auth()->id()) abort(404, "Reservasi tidak ditemukan.");

            return view("users.reservation", compact("reservation", "formattedDate", "today"));
        }
    }
}