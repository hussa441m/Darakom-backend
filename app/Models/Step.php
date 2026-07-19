<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
     
    protected $fillable = [
    'title',
    'description',
    'date',
    'progress_percent',
    'status',
    'project_id',
];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // علاقة الخطوة بالتقارير المرفوعة عنها (جديد)
    public function reports()
    {
        return $this->hasMany(ProjectReport::class);
    }

    function documents()
    {
        return $this->morphMany(Document::class , 'documentable');
    }
}
