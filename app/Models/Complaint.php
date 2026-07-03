<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [  'text', 'user_id', 'project_id'];
    function project(){
        return $this->belongsTo(Project::class);
    }
    function user(){
        return $this->belongsTo(user::class);
    }
}
