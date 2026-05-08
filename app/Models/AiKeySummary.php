<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiKeySummary extends Model
{
    protected $table = 'ai_key_summaries';

    public $timestamps = false;

    protected $fillable = [
        'summary_id',
        'concept',
        'concept_type',
    ];

    public function summary(): BelongsTo
    {
        return $this->belongsTo(AiSummary::class, 'summary_id');
    }
}

