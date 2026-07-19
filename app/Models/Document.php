<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{    
    protected $fillable = [
    'path', 
    'description', 
    'document_type_id',
    'documentable_id',   
    'documentable_type'  
];

    function documentable(){
        return $this->morphTo();
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
