<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookAccount extends Model
{

    protected $fillable = [

        'user_id',
        'facebook_id',
        'name',
        'access_token'

    ];

}