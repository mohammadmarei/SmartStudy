<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    //
    protected $fillable = [
        'question_id',
        'option_label',
        'option_text',
        'is_correct'
    ];
    public $timestamps = false;
    public function question()
    {
        return $this->belongsTo(AiQuestion::class, 'question_id');
    }
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'option_id');
    }
}
