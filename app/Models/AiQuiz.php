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
        'subject_id',
        'content_id',
        'job_id',
        'title',
        'difficulty',
        'time_limit',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(AiJob::class, 'job_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(File::class, 'content_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AiQuestion::class, 'quiz_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'quiz_id');
    }
}