<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class MSUser extends Model
{
    protected $table = 'ms_users';
    protected $primaryKey = 'ms_user_id';
    protected $fillable = ['id', 'displayName', 'givenName', 'mail', 'mobilePhone', 'userPrincipalName',
        'account_id', 'domain_id', 'sync_at', 'state', 'userType',
        'createdDateTime', 'surname', 'accountEnabled', 'user_id'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function reseller()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }
}