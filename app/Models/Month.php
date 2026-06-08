<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    public $incrementing = false;

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'month');
    }

    protected $fillable = [
        "id"
    ];
}
