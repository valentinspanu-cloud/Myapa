<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'phone', 'category', 'notify', 'ruta',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $with = [
        'codes',
        'receivedNotifications',
        'status',
        'sectors'
    ];

    public function codes($request = '')
    {
        if(session('selectedLocation') != '')
        {
            $request = session('selectedLocation');
            return $this->hasMany('\App\Models\ClientCode')->where('client_code','=', $request);
        }
        else {
            return $this->hasMany('\App\Models\ClientCode');
        }

    }

    public function codes_all()
    {
        return $this->hasMany('\App\Models\ClientCode');
    }

    public function sectors()
    {
        return $this->hasMany('\App\Models\Sector');
    }

    public function status()
    {
        return $this->hasOne('\App\Models\UserStatus', 'id', 'status');
    }

    public function statusdata()
    {
        return $this->hasOne('\App\Models\UserStatus', 'id', 'status');
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->attributes['created_at'] = (new Carbon($value))->format('d.m.Y');
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->attributes['updated_at'] = (new Carbon($value))->format('d.m.Y');
    }

    public function receivedNotifications()
    {
        return $this->hasMany('\App\Models\UserNotification', 'user_id', 'id');
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }
}


