<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'rate',
        'comment',
        'project_id',
        'user_id',
        'to_user_id'
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}