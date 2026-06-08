<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    public $incrementing = false;

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    protected $fillable = [
        "id",
        "month",
        "name"
    ];
}
