<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'quiz_id',
        'question_id',
        'result_id',
        'option_id',
        'is_correct',
    ];
     protected $casts = [
        'is_correct' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function quiz()
    {
        return $this->belongsTo(AiQuiz::class,'quiz_id');
    }
    public function question()
    {
        return $this->belongsTo(AiQuestion::class,'question_id');
    }
    public function option()
    {
        return $this->belongsTo(AiOption::class,'option_id');
    }
    public function result()
    {
        return $this->belongsTo(Result::class,'result_id');
    }
     protected $with=['user','quiz','question','option'];
    //
}
