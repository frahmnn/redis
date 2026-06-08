<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    public $incrementing = false;

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function major(){
        return $this->belongsTo(Major::class);
    }
    public function specials(){
        return $this->hasMany(Special::class);
    }

    protected $fillable = [
        "id",
        "user_id",
        "name",
        "gender",
        "special_role",
        "division",
        "student_id",
        "major_id",
        "generation",
        "whatsapp_number",
        "disability",
        "email",
        "verified",
        "requested"
    ];
}
