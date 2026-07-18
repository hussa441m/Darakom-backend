<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
     protected $fillable = [
          'first_name',
          'last_name',
          'email',
          'address',
          'password',
          'type',        // admin, client, provider
          'status',      // pending, active, closed, locked
          'avatar',
          'fcm_token',
          'is_notifications_enabled',
    ];
   

    /**
     * Accessor لجلب الاسم الكامل للمستخدم مباشرة
     */
    public function getFullNameAttribute()
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
     public function projectReports()
    {
       return $this->hasMany(ProjectReport::class);
    }


    // التقييمات التي حصل عليها المستخدم
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'to_user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_notifications_enabled' => 'boolean',
            'password' => 'hashed',
        ];
    }
}