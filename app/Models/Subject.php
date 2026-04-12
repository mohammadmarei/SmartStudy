<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = "subjects";

    protected $fileable = [
        "user_id",
        "name",
        "color"
    ];
}
