<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;

    protected $with = [
        'status',
        'comments'
    ];

    // L11: SoftDeletes gestionează deleted_at automat

    const UNSOLVED = 1;
    const PENDING = 2;
    const DONE = 3;

    public function status()
    {
        return $this->hasOne('\App\Models\ComplaintStatus', 'id', 'status_id');
    }

    public function comments()
    {
        return $this->hasMany('\App\Models\ComplaintComment', 'complaint_id', 'id');
    }

    public function reporter()
    {
        return $this->hasOne('\App\Models\User', 'id', 'user_id');
    }

    public function orderReporter() {
        return $this->reporter()->where('name','=', 1);
    }

    public function solver()
    {
        return $this->hasOne('\App\Models\User', 'id', 'resp_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->attributes['created_at'] = (new Carbon($value))->format('Y-m-d');
    }


    public function type(){
        return $this->belongsTo('\App\Models\ComplaintType');
    }
}
