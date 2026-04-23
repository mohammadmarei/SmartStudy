<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiQuiz extends Model
{
    protected $table = 'ai_quizzes';

    protected $fillable = [
        'job_id',
        'subject_id',
        'difficulty',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}