<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authentication extends Model
{

    protected $table = 'auths';

    protected $fillable = [
        'username', 'password',
    ];
    protected $hidden = [
        'password', 'last_login', 'created_at', 'updated_at',
    ];

}
