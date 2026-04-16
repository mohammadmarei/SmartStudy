<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeakArea extends Model
{
    protected $table = 'weak_areas';

    protected $fillable = [
        'user_id',
        'subject_id',
        'topic_name',
        'weakness_level',
        'times_mistaken',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}