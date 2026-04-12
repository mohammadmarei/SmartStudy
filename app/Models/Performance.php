<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    protected $fillable = [
    'user_id',
    'subject_id',
    'average_score',
    'success_rate',
    'weak_topics_count',
    'last_updated'
];
}
