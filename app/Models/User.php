<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'address',
        'password',
        'phone',
        'type',      
        'status',    
        'avatar',
        'fcm_token',
        'is_notifications_enabled',
        'province_id',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function complaintsAgainstMe()
    {
        return $this->hasMany(Complaint::class, 'against_user_id');
    }

    public function projectReports()
    {
        return $this->hasMany(ProjectReport::class);
    }

    public function province()
    { 
        return $this->belongsTo(Province::class);
    }

    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'to_user_id');
    }

    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    // العروض التي تُلّقاها العميل على مشاريعه
    public function offers()
    {
        return $this->hasManyThrough(
            Offer::class,
            Project::class,
            'client_id',
            'project_id',
            'id',
            'id'
        );
    }

    // العروض التي قدمها المستخدم كـ Provider
    public function submittedOffers()
    {
        return $this->hasMany(Offer::class, 'offered_by');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_notifications_enabled' => 'boolean',
            'password' => 'hashed',
        ];
    }
}