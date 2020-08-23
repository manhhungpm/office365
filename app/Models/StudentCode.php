<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class StudentCode extends Model
{
    protected $fillable = ['code', 'domain_id', 'status','max_user', 'expired_date'];

    public function reseller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }
}