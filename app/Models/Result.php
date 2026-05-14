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
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
 /*    public function user()
    {
        return $this->belongsTo(User::class);
    } */
    public function quiz()
    {
        return $this->belongsTo(AiQuiz::class,'quiz_id');
    }
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class,'result_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
