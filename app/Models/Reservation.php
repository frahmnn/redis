<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "id",
        "user_id",
        "dateid",
        "place",
        "type",
        "description",
        "assistant",
        "coassistant",
        "status"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function assistantUser()
    {
        return $this->belongsTo(User::class, "assistant", "id");
    }

    public function logs()
    {
        return $this->hasMany(Log::class, "reservation_id", "id");
    }

    public function getFormattedCreatedAtAttribute()
    {
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

        $createdAt = $this->created_at;
        $dayOfWeek = $createdAt->dayOfWeek;
        $day = $createdAt->day;
        $month = $createdAt->month;
        $year = $createdAt->year;
        $hour = $createdAt->format('H');
        $minute = $createdAt->format('i');

        return $dayNames[$dayOfWeek] . ", " . $day . " " . $monthNames[$month] . " " . $year . " pukul " . $hour . ":" . $minute;
    }
}
