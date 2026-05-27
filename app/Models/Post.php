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
        'instagram_post_id',
        'message',
        'instagram_message',
        'image',
        'status',
        'post_type',
        'instagram_status',
        'instagram_error',
        'likes_count',
        'comments_count',
        'is_archived',
        'is_fb_archived',
        'is_ig_archived',
        'hide_likes',
        'hide_comments',
        'post_to_facebook',
        'post_to_instagram',
        'scheduled_at',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_fb_archived' => 'boolean',
        'is_ig_archived' => 'boolean',
        'hide_likes' => 'boolean',
        'hide_comments' => 'boolean',
        'post_to_facebook' => 'boolean',
        'post_to_instagram' => 'boolean',
        'scheduled_at' => 'datetime',
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
