<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_questions',
        'correct_answers',
        'start_time',
        'end_time',
    ];

    public function quiz()
    {
        return $this->belongsTo(AiQuiz::class, 'quiz_id');
    }
}