<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'text', 
        'status',                  // تم إضافته ليعبر عن (معلقة، تم حلها، مرفوضة)
        'admin_response',  // تم إضافته لتخزين رد الأدمن عند الحل أو الرفض
        'user_id', 
        'project_id',
       
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}