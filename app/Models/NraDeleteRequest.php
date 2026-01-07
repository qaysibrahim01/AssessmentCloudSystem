<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NraDeleteRequest extends Model
{
    protected $fillable = [
        'nra_id',
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

    public function nra()
    {
        return $this->belongsTo(Nra::class);
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
