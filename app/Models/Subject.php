<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = "subjects";

    protected $fillable = [
        "user_id",
        "name",
        "color"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function files(){
        return $this->hasMany(File::class);
    }
}
