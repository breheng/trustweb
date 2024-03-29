<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function verifyUser(){
      return $this->hasOne('App\VerifyUser');
    }

    public function Partner(){
      return $this->belongsTo('App\Partner', 'partner_id');
    }

    public function Division(){
      return $this->belongsTo('App\Unit', 'unit_id');
    }

    public function divName(){
      if($this->isvendor == 1){
        return $this->Partner->comp_name;
      } else {
        return $this->Division->pporgunitdesc;
      }
    }
}
