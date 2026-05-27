<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    protected $fillable = [
        'user_id', 'ad_account_id', 'campaign_id', 'name', 
        'objective', 'status', 'daily_budget',
        'adset_id', 'creative_id', 'ad_id', 'page_id',
        'age_min', 'age_max', 'location', 'primary_text', 'website_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(AdAccount::class, 'ad_account_id');
    }
}
