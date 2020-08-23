<?php

namespace App;

use App\Models\Domain;
use App\Models\StudentCode;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'display_name', 'phone', 'num_user_max', 'status', 'code', 'note'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function domains()
    {
        return $this->belongsToMany(Domain::class, 'user_domain', 'user_id', 'domain_id', 'id', 'domain_id');
    }

    public function codes()
    {
        return $this->hasMany(StudentCode::class);
    }
}
