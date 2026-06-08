<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Special extends Model
{
    public $incrementing = false;
    public function identity(){
        return $this->belongsTo(Identity::class);
    }

    protected $fillable = [
        "id",
        "identity_id",
        "special"
    ];
}
