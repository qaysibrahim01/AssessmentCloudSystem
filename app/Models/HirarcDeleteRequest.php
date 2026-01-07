<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HirarcDeleteRequest extends Model
{
    protected $fillable = [
        'hirarc_id',
        'requested_by',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_remark',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function hirarc()
    {
        return $this->belongsTo(Hirarc::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
