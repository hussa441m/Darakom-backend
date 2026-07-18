<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{ 
    protected $fillable = [
        'project_code',       // كود المشروع الفريد لواجهة النجاح
        'title', 
        'work_type',          // إنشاء أو تشطيب
        'craftsman_type',     // نوع الحرفي (كهرباء، سباكة...)
        'tender_type',        // مستعجل أو عادي
        'tender_duration_days',
        'start_date', 
        'end_date', 
        'duration', 
        'area', 
        'location_details', 
        'building_no', 
        'description', 
        'visibility',
        'budget', 
        'status',             // pending, new, active, completed
        'execution_status',   // not_started, in_progress, paused, finished
        'rate', 
        'comment', 
        'project_type_id', 
        'client_id',          // تم تعديلها لتطابق الميجريشن (صاحب المشروع)
        'performed_by',       // المقاول المنفذ للمشروع
        'province_id',
        'provider_profile_id'
    ];
    
  
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }
    
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    // العميل صاحب المشروع (Client)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    
    // مزود الخدمة الذي ينفذ المشروع بعد قبول العرض
    public function performer()
    {
        return $this->belongsTo(Profile::class, 'performed_by');
    }
    
    // المرفقات والصور للمشروع (Polymorphic)
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }

    public function ratings() 
    {
        return $this->hasMany(Rating::class);
    }

    public function reports()
    {
        return $this->hasMany(ProjectReport::class);
    }

    // مزود الخدمة الذي وُجّهت إليه المناقصة الخاصة
    public function providerProfile()
    {
       return $this->belongsTo(Profile::class, 'provider_profile_id');
    }
}