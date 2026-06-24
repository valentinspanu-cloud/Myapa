<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintComment extends Model
{
    protected $table = 'complaint_comments';

    public function status()
    {
        return $this->hasOne('\App\Models\ComplaintStatus', 'id', 'status_id');
    }

    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'user_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo('\App\Models\ComplaintType', 'type_id', 'id');
    }
}
