<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
   protected $fillable = [
    'cost',
    'duration',
    'duration_unit',
    'provider_comment',
    'details',
    'project_id',
    'offered_by',
    'status',
    'reject_reason',
];

    // العرض ينتمي إلى مشروع معين
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // العرض مقدم من قِبل مزود الخدمة (المقاول/المهندس)
    public function provider()
    {
        return $this->belongsTo(Profile::class, 'offered_by');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}