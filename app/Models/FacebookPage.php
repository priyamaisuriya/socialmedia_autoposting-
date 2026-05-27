<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'page_id',
        'name',
        'access_token',
        'instagram_account_id',
        'instagram_username',
        'is_instagram_connected',
    ];

    protected $casts = [
        'is_instagram_connected' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}