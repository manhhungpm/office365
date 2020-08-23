<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['app_name', 'description', 'client_id', 'tenant_id', 'client_secret', 'status', 'user_id'];
}