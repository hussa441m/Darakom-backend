<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountLog extends Model
{
    protected $table = 'account_log';

    protected $fillable = [
        'details',
        'created_by',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}