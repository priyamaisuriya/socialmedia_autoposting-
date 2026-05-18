<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facebook_page_id',
        'facebook_post_id',
        'message',
        'image',
        'status',
        'likes_count',
        'comments_count',
        'is_archived',
        'hide_likes',
        'hide_comments',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'hide_likes' => 'boolean',
        'hide_comments' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facebookPage()
    {
        return $this->belongsTo(FacebookPage::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
