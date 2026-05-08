<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiQuiz extends Model
{
    protected $table = 'ai_quizzes';
     protected $fillable = [
        'subject_id',
        'content_id',
        'job_id',
        'title',
        'difficulty',
        'time_limit' 
        
    ];
      public function questions()
    {
        return $this->hasMany(AiQuestion::class, 'quiz_id');
    }
    public function results(){
        return $this->hasMany(Result::class, 'quiz_id');
    }
    public function job(){
        return $this->belongsTo(AiJob::class, 'job_id');
    }
    public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }
     public function content(){
        return $this->belongsTo(File::class, 'content_id');
    }
}
