<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiJob extends Model
{
    //
     protected $fillable = [
        'material_id',
        'subject_id',
        'model_used',
        'error_message',
        'retry_count',
        'user_id',
        'status',
        'created_at',
        'completed_at'
    ];
    public $timestamps = false;
    public function quizzes()
{
    return $this->hasMany(AiQuiz::class, 'job_id');
}
 public function material()
    {
        return $this->belongsTo(File::class, 'material_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
