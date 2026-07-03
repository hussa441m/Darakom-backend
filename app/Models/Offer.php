<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [  'cost', 'duration','details','isSelected' ,'project_id' , 'offered_by'];

    function documents(){
        return $this->morphMany(Document::class , 'documentable');
    }

}
