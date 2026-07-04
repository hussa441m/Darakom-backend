<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{    
    protected $fillable = [ 'path' , 'description' , 'user_id' , 'project_id' ,'document_type_id'];

    function documentable(){
        return $this->morphTo();
    }
}
