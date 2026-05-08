<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiQuestion extends Model
{
    protected $table = 'ai_questions';
    //
   protected $fillable = [
        'quiz_id',
        'question_text',
        'explanation'
        
    ];
    public $timestamps = false;
    public function quiz()
    {
        return $this->belongsTo(AiQuiz::class, 'quiz_id');
    }
    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'question_id');
    }
}
