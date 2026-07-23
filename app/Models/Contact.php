<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['value',  'user_id', 'contact_type_id'];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contactType()
    {
       return $this->belongsTo(ContactType::class);
    }
}
