<?php

namespace App\Models;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $connection = 'mysql';
    protected $fillable = ['name', 'display_name', 'description'];
    protected $hidden = ['pivot'];
}
