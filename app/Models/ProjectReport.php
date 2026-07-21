<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectReport extends Model
{
    protected $fillable = [
        'description',
        'status',
        'project_id',
        'user_id',
        'step_id',
        'reported_progress',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}