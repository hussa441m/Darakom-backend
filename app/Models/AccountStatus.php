<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
    ];
    function users(){
        return $this->hasMany(User::class);
    }
}
