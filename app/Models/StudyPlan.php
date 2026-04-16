<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyPlan extends Model
{
    protected $table = 'study_plans';

    protected $fillable = [
        'user_id',
        'subject_id',
        'goal',
        'start_date',
        'end_date',
        'status',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}