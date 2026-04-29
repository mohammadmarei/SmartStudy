<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'date_of_birth',
        'gender',
        'bio',
        'linkedin_url',
        'github_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
