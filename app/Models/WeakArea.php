<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeakArea extends Model
{
    protected $fillable = [
    'user_id',
    'subject_id',
    'topic_name',
    'weakness_level',
    'times_mistaken'
];
}
