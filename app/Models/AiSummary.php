<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiSummary extends Model
{
    protected $table = 'ai_summaries';

    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'summary_text',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(AiJob::class, 'job_id');
    }

    public function keyPoints(): HasMany
    {
        return $this->hasMany(AiKeySummary::class, 'summary_id');
    }
}

