<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreviousWorkImage extends Model
{
    protected $fillable = [
        'path',
        'is_cover',
        'previous_work_id'
    ];

    public function previousWork()
    {
        return $this->belongsTo(PreviousWork::class);
    }
}