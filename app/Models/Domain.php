<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $primaryKey = 'domain_id';
    protected $fillable = ['authenticationType', 'availabilityStatus', 'id', 'isAdminManaged', 'isDefault', 'isInitial', 'isRoot', 'isVerified', 'supportedServices', 'state', 'account_id', 'sync_at'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function resellers()
    {
        return $this->belongsToMany(User::class, 'user_domain', 'domain_id', 'user_id', 'domain_id', 'id');
    }
}