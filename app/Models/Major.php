<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    public $incrementing = false;

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
    
    protected $fillable = [
        "id",
        "faculty_id",
        "name"
    ];
}