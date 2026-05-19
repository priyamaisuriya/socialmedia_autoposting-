<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facebook_page_id',
        'campaign_id',
        'name',
        'objective',
        'daily_budget',
        'status',
        'clicks',
        'impressions',
        'spend',
        'ctr',
        'target_location',
        'target_age_min',
        'target_age_max',
        'ad_text',
        'ad_image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facebookPage()
    {
        return $this->belongsTo(FacebookPage::class);
    }
}
