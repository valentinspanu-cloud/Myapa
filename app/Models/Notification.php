<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    const SENT = 1;
    const NOTSENT = 2;
    const PENDING = 3;
    const STATUSES = [
        self::SENT => 'Trimisa',
        self::NOTSENT => 'Nu a fost trimisa',
        self::PENDING => 'In curs de trimitere'
    ];

    protected $dates = ['date_from', 'date_to'];

    protected $casts = [
        'sectors' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('\App\Models\User');
    }

    public function type(){
        return $this->belongsTo('\App\Models\NotificationType');
    }

    public function status()
    {
        return $this->hasOne('\App\Models\NotificationStatus', 'id', 'status_id');
    }

    public function getSectorsAttribute($value)
    {
        return json_decode($value, 1);
    }
}
