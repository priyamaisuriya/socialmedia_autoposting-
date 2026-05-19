<?php

namespace App\Http\Controllers;

use App\Models\AdCampaign;
use App\Models\FacebookPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdCampaignController extends Controller
{
    public function index()
    {
        $campaigns = auth()->user()->adCampaigns()->latest()->get();

        // Simulate daily/dynamic progress on active ads to make the demo feel alive
        foreach ($campaigns as $campaign) {
            if ($campaign->status === 'ACTIVE') {
                // Randomly add clicks and impressions
                $newImpressions = rand(50, 200);
                $newClicks = rand(2, 15);
                
                // Keep CTR realistic
                $campaign->impressions += $newImpressions;
                $campaign->clicks += $newClicks;
                
                // Spend accumulates based on budget
                $avgCostPerClick = 0.50; // Mock CPC
                $campaign->spend += ($newClicks * $avgCostPerClick);
                
                if ($campaign->spend > ($campaign->daily_budget * 5)) {
                    // Stop or cap demo spend at 5 days budget
                    $campaign->spend = $campaign->daily_budget * 5;
                }
                
                if ($campaign->impressions > 0) {
                    $campaign->ctr = ($campaign->clicks / $campaign->impressions) * 100;
                }
                
                $campaign->save();
            }
        }

        // Aggregate dashboard metrics
        $totalSpend = $campaigns->sum('spend');
        $totalImpressions = $campaigns->sum('impressions');
        $totalClicks = $campaigns->sum('clicks');
        $averageCtr = $campaigns->avg('ctr') ?? 0.00;

        // Mock historical data for rendering premium charts
        $chartData = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'spend' => [120, 230, 180, 340, 290, 410, $totalSpend > 0 ? min($totalSpend, 500) : 150],
            'clicks' => [45, 92, 70, 120, 110, 150, $totalClicks > 0 ? min($totalClicks, 200) : 60],
        ];

        return view('ads.index', compact('campaigns', 'totalSpend', 'totalImpressions', 'totalClicks', 'averageCtr', 'chartData'));
    }

    public function create()
    {
        $pages = auth()->user()->facebookPages;
        return view('ads.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'objective' => 'required|string',
            'daily_budget' => 'required|numeric|min:10',
            'target_location' => 'required|string',
            'target_age_min' => 'required|integer|min:13',
            'target_age_max' => 'required|integer|gte:target_age_min',
            'ad_text' => 'required|string',
            'ad_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('ad_image')) {
            $imagePath = $request->file('ad_image')->store('ads', 'public');
        }

        // Create Ad Campaign
        auth()->user()->adCampaigns()->create([
            'facebook_page_id' => $request->facebook_page_id,
            'name' => $request->name,
            'objective' => $request->objective,
            'daily_budget' => $request->daily_budget,
            'target_location' => $request->target_location,
            'target_age_min' => $request->target_age_min,
            'target_age_max' => $request->target_age_max,
            'ad_text' => $request->ad_text,
            'ad_image' => $imagePath,
            'status' => 'ACTIVE',
            // Starting demo values
            'clicks' => rand(5, 12),
            'impressions' => rand(100, 250),
            'spend' => rand(10, 30),
            'ctr' => rand(3, 8),
        ]);

        return redirect()->route('ads.index')->with('success', 'Meta Ad Campaign Launched in Sandbox Demo Mode!');
    }

    public function toggleStatus(AdCampaign $campaign)
    {
        $this->authorizeOwner($campaign);

        $campaign->status = $campaign->status === 'ACTIVE' ? 'PAUSED' : 'ACTIVE';
        $campaign->save();

        return redirect()->back()->with('success', 'Campaign status updated successfully.');
    }

    public function destroy(AdCampaign $campaign)
    {
        $this->authorizeOwner($campaign);

        if ($campaign->ad_image) {
            Storage::disk('public')->delete($campaign->ad_image);
        }
        $campaign->delete();

        return redirect()->route('ads.index')->with('success', 'Campaign deleted successfully.');
    }

    private function authorizeOwner(AdCampaign $campaign)
    {
        if ($campaign->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
