<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AiJob extends Model
{
    protected $table = 'ai_jobs';

    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'subject_id',
        'user_id',
        'model_used',
        'status',
        'error_message',
        'retry_count',
        'created_at',
        'completed_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(File::class, 'material_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function summary(): HasOne
    {
        return $this->hasOne(AiSummary::class, 'job_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(AiQuiz::class, 'job_id');
    }
}

