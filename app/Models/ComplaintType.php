<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintType extends Model
{
    protected $with = [
        'users'
    ];

    public function users()
    {
        return $this->hasMany('App\Models\ComplaintTypeUser');
    }
}
