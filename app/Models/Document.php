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

    // إظهار حقل full_path تلقائياً عند تحويل الموديل إلى JSON
    protected $appends = ['full_path'];

    /**
     * Accessor لإرجاع الرابط الكامل للملف
     */
    public function getFullPathAttribute(): ?string
    {
        return $this->path ? asset('storage/' . $this->path) : null;
    }

    public function documentable()
    {
        return $this->morphTo();
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}