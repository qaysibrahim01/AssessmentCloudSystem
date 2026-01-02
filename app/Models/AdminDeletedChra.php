<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminDeletedChra extends Model
{
    protected $fillable = [
        'chra_id',
        'company_name',
        'requested_by',
        'deleted_by',
        'reason',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
