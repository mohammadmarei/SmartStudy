<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiQuiz extends Model
{
    protected $table = 'ai_quizzes';

    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'subject_id',
        'difficulty',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(AiJob::class, 'job_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AiQuestion::class, 'quiz_id')->orderBy('id');
    }
}

