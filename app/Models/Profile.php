<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'experience_start',
        'work_area',
        'bio',
        'experience_years',
        'admin_comment',
        'syndicate_number',
        'logo',
        'user_id',
        'role_id',
    ];


    // البروفايل تابع لمستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    // البروفايل له دور (مقاول، مهندس، حرفي...)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    // الملفات المرتبطة بالبروفايل
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // المشاريع التي ينفذها هذا المزود
    public function projects()
    {
        return $this->hasMany(Project::class, 'performed_by');
    }


    // الخدمات التي يقدمها الحرفي
    public function serviceCategories()
    {
        return $this->belongsToMany(
            ServiceCategory::class,
            'artisan_services',
            'profile_id',
            'service_category_id'
        );
    }
    public function offers()
    {
        return $this->hasMany(Offer::class, 'offered_by');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function qualifications()
{
    return $this->hasMany(Qualification::class);
}
}