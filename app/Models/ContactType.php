<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
    ];

    function contacts(){
        return $this->hasMany(Contact::class);
    }

}
