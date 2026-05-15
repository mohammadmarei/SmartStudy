<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiQuestion extends Model
{
    protected $table = 'ai_questions';

    public $timestamps = false;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'explanation',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(AiQuiz::class, 'quiz_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('id');
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class, 'question_id');
    }
}