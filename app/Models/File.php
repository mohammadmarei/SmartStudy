<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
     protected $table="files";
     protected $fillable=[
        "subject_id",
        "file_name",
        "file_path",
        
     ];

     public function subject():BelongsTo
     {
        return $this->belongsTo(Subject::class,'subject_id');
>>>>>>> origin/Marei
     }
}


        'file',
     ];

     public function subject():BelongsTo
     {
        return $this->belongsTo(Subject::class,'subject_id');
        
     ];

     public function subject(){
        return $this->belongsTo(Subject::class);
     }
}

