<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'text',
        'type',
        'status',
        'admin_response',
        'project_id',
        'user_id',
        'against_user_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function againstUser()
    {
        return $this->belongsTo(User::class, 'against_user_id');
    }
}