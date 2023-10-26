<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name','last_name','email','mobile_no','password','decrypted_password','gender','role','bio','estatus','g_coin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getProfilePicAttribute(){
        if($this->attributes['role'] == 5 || $this->attributes['role'] == 3)
        {
            if($this->attributes['profile_pic'] != null){
                return $this->attributes['profile_pic'];
            }else{
                return null;
            }
        }
        else
        {
            if($this->attributes['profile_pic'] != null){
                return asset('images/profile_pic/'.$this->attributes['profile_pic']);
            }else{
                return null;
            }
        }
    }

    public function created_at_mdY()
    {
       return Carbon::parse($this->created_at)->format('m/d/Y');
    }

    public function user_language(){
        return $this->hasMany(UserLanguage::class,'user_id','id');
    }

    public function agency()
    {
        return $this->hasOne('App\Models\Agency', 'id', 'agency_id');
    }

    public function tokenExpired()
    {
        if (Carbon::parse($this->attributes['subscription_end_date']) < Carbon::now()) {
            return false;
        }
        return true;
    }

}
