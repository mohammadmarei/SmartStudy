<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    protected $fillable = [
        'question_id',
        'result_id',
        'option_id',
        'is_correct',
    ];
     protected $casts = [
        'is_correct' => 'boolean',
    ];
    public function question()
    {
        return $this->belongsTo(AiQuestion::class,'question_id');
    }
    public function option()
    {
        return $this->belongsTo(QuestionOption::class,'option_id');
    }
    public function result()
    {
        return $this->belongsTo(Result::class,'result_id');
    }
/*      protected $with=['user','quiz','question','option']; */
    //
}
