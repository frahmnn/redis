<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public $incrementing = false;

    protected $fillable = [
        "id",
        "user_id",
        "month",
        "day"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function monthRelation()
    {
        return $this->belongsTo(Month::class, "month");
    }
}
