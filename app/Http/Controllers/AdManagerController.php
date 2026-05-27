<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdAccount;
use App\Models\AdCampaign;
use App\Models\FacebookAccount;
use App\Services\FacebookApiService;
use Illuminate\Support\Facades\Log;

class AdManagerController extends Controller
{
    protected $facebookApi;

    public function __construct(FacebookApiService $facebookApi)
    {
        $this->facebookApi = $facebookApi;
    }

    public function index()
    {
        $user = auth()->user();
        $accounts = AdAccount::where('user_id', $user->id)->get();
        $campaigns = AdCampaign::where('user_id', $user->id)->with('account')->latest()->get();

        return view('ads.index', compact('accounts', 'campaigns'));
    }

    public function fetchAccounts()
    {
        $user = auth()->user();
        // Get the first linked Facebook account to fetch ad accounts
        $fbAccount = FacebookAccount::where('user_id', $user->id)->first();

        if (!$fbAccount) {
            return redirect()->route('ads.index')->with('error', 'Please connect a Facebook account first.');
        }

        $response = $this->facebookApi->getAdAccounts($fbAccount->access_token);

        if (isset($response['error'])) {
            return redirect()->route('ads.index')->with('error', 'API Error: ' . $response['error']['message']);
        }

        $synced = 0;
        if (isset($response['data'])) {
            foreach ($response['data'] as $accountData) {
                AdAccount::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'account_id' => $accountData['account_id'],
                    ],
                    [
                        'name' => $accountData['name'] ?? 'Unknown Account',
                        'currency' => $accountData['currency'] ?? 'USD',
                    ]
                );
                $synced++;
            }
        }

        return redirect()->route('ads.index')->with('success', "Successfully synced {$synced} Ad Accounts.");
    }

    public function create()
    {
        $user = auth()->user();
        $accounts = AdAccount::where('user_id', $user->id)->get();
        $pages = \App\Models\FacebookPage::where('user_id', $user->id)->get();

        if ($accounts->isEmpty()) {
            return redirect()->route('ads.index')->with('error', 'Please fetch your Ad Accounts first.');
        }
        if ($pages->isEmpty()) {
            return redirect()->route('ads.index')->with('error', 'Please connect a Facebook Page first.');
        }

        return view('ads.create', compact('accounts', 'pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ad_account_id' => 'required|exists:ad_accounts,id',
            'page_id' => 'required|string',
            'name' => 'required|string|max:255',
            'objective' => 'required|string',
            'daily_budget' => 'required|numeric|min:1',
            'age_min' => 'required|integer|min:13',
            'age_max' => 'required|integer|max:65',
            'primary_text' => 'required|string',
            'website_url' => 'nullable|url',
            'media' => 'required|image|max:5120', // 5MB max
        ]);

        $user = auth()->user();
        $fbAccount = FacebookAccount::where('user_id', $user->id)->first();
        $adAccount = AdAccount::findOrFail($request->ad_account_id);

        if (!$fbAccount) {
            return redirect()->route('ads.index')->with('error', 'Facebook account not connected.');
        }

        $token = $fbAccount->access_token;
        $accountId = $adAccount->account_id;

        // 1. Create Campaign
        $campaignRes = $this->facebookApi->createAdCampaign(
            $accountId, $request->name, $request->objective, null, $token
        );
        if (isset($campaignRes['error'])) return redirect()->back()->withInput()->with('error', 'Campaign Error: ' . json_encode($campaignRes['error']));
        $campaignId = $campaignRes['id'];

        // 2. Create Ad Set
        $adSetRes = $this->facebookApi->createAdSet(
            $accountId, $campaignId, $request->name . ' - Ad Set', 
            $request->daily_budget, $request->age_min, $request->age_max, $token
        );
        if (isset($adSetRes['error'])) return redirect()->back()->withInput()->with('error', 'Ad Set Error: ' . json_encode($adSetRes['error']));
        $adSetId = $adSetRes['id'];

        // 3. Upload Media
        $mediaPath = $request->file('media')->store('ads_media', 'public');
        $fullMediaPath = storage_path('app/public/' . $mediaPath);
        
        $mediaRes = $this->facebookApi->uploadAdImage($accountId, $fullMediaPath, $token);
        if (isset($mediaRes['error'])) return redirect()->back()->withInput()->with('error', 'Media Upload Error: ' . json_encode($mediaRes['error']));
        $imageHash = $mediaRes['images'][basename($fullMediaPath)]['hash'] ?? $mediaRes['images'][array_key_first($mediaRes['images'])]['hash'];

        // 4. Create Ad Creative
        $creativeRes = $this->facebookApi->createAdCreative(
            $accountId, $request->page_id, $request->name . ' - Creative', 
            $imageHash, $request->primary_text, $request->website_url, $token
        );
        if (isset($creativeRes['error'])) return redirect()->back()->withInput()->with('error', 'Creative Error: ' . json_encode($creativeRes['error']));
        $creativeId = $creativeRes['id'];

        // 5. Create Final Ad
        $adRes = $this->facebookApi->createAd(
            $accountId, $adSetId, $creativeId, $request->name . ' - Ad', $token
        );

        if (isset($adRes['error'])) {
            $err = $adRes['error'];
            // Check for Facebook "No payment method" error (Subcode 1359188)
            if (isset($err['error_subcode']) && $err['error_subcode'] == 1359188) {
                return redirect()->back()->withInput()->with('payment_error', $accountId);
            }
            return redirect()->back()->withInput()->with('error', 'Publish Ad Error: ' . json_encode($err));
        }
        $adId = $adRes['id'];

        // Save locally
        AdCampaign::create([
            'user_id' => $user->id,
            'ad_account_id' => $adAccount->id,
            'campaign_id' => $campaignId,
            'adset_id' => $adSetId,
            'creative_id' => $creativeId,
            'ad_id' => $adId,
            'page_id' => $request->page_id,
            'name' => $request->name,
            'objective' => $request->objective,
            'status' => 'PAUSED',
            'daily_budget' => $request->daily_budget,
            'age_min' => $request->age_min,
            'age_max' => $request->age_max,
            'primary_text' => $request->primary_text,
            'website_url' => $request->website_url,
        ]);

        return redirect()->route('ads.index')->with('success', 'Full Ad Campaign created successfully! It is PAUSED in your Ads Manager.');
    }
}
