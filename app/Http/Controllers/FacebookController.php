<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\FacebookAccount;
use App\Models\FacebookPage;
use App\Models\User;
use App\Services\FacebookApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FacebookController extends Controller
{
    protected $facebookApi;

    public function __construct(FacebookApiService $facebookApi)
    {
        $this->facebookApi = $facebookApi;
    }

    public function index()
    {
        $accounts = auth()->user()->facebookAccounts;
        return view('facebook.index', compact('accounts'));
    }

    public function redirect()
    {
        session(['oauth_flow' => 'facebook']);

        return Socialite::driver('facebook')
            ->scopes([
                'public_profile',
                'email',
                'pages_show_list',
                'pages_read_engagement',
                'pages_manage_posts',
                'pages_manage_engagement',
                'publish_video',
                'business_management',
                'instagram_basic',
                'instagram_content_publish',
                'instagram_manage_comments',
                'ads_management',
                'ads_read'
            ])
            ->with(['auth_type' => 'rerequest'])
            ->redirect();
    }

    public function redirectInstagram()
    {
        session(['oauth_flow' => 'instagram']);

        return Socialite::driver('facebook')
            ->scopes([
                'public_profile',
                'email',
                'pages_show_list',
                'pages_read_engagement',
                'pages_manage_posts',
                'pages_manage_engagement',
                'publish_video',
                'business_management',
                'instagram_basic',
                'instagram_content_publish',
                'instagram_manage_comments',
                'ads_management',
                'ads_read'
            ])
            ->with(['auth_type' => 'rerequest'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();
            $flow = session('oauth_flow', 'facebook');

            // 1. Get the current user or find/create one
            $user = Auth::user();
            
            if (!$user) {
                $user = User::where('facebook_id', $fbUser->getId())
                            ->orWhere('email', $fbUser->getEmail())
                            ->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $fbUser->getName(),
                        'email' => $fbUser->getEmail(),
                        'facebook_id' => $fbUser->getId(),
                        'password' => bcrypt(Str::random(16)),
                    ]);
                }
                Auth::login($user);
            }

            // 2. Link or Update Facebook Account
            $account = FacebookAccount::updateOrCreate(
                ['facebook_id' => $fbUser->getId()],
                [
                    'user_id' => $user->id,
                    'name' => $fbUser->getName(),
                    'access_token' => $fbUser->token,
                ]
            );

            // 3. Fetch Pages
            $pagesData = $this->facebookApi->getPages($fbUser->token);
            \Illuminate\Support\Facades\Log::info('Meta Pages Data:', ['pages' => $pagesData, 'flow' => $flow]);
            $pagesSynced = 0;
            $igSynced = 0;

            if (isset($pagesData['error'])) {
                $targetIndex = $flow === 'instagram' ? 'instagram.index' : 'facebook.index';
                return redirect()->route($targetIndex)->with('error', 'Meta API Error: ' . $pagesData['error']['message']);
            }

            if (isset($pagesData['data']) && count($pagesData['data']) > 0) {
                foreach ($pagesData['data'] as $page) {
                    $igId = $page['instagram_business_account']['id'] ?? null;
                    $igUser = $page['instagram_business_account']['username'] ?? null;

                    // If it is the Instagram flow, we filter and only import pages with associated IG profiles
                    if ($flow === 'instagram' && !$igId) {
                        continue;
                    }

                    // Retrieve existing page to keep is_instagram_connected status if it was already configured
                    $existingPage = FacebookPage::where('user_id', $user->id)
                        ->where('page_id', $page['id'])
                        ->first();

                    $isIgConnected = $existingPage ? $existingPage->is_instagram_connected : false;
                    if ($flow === 'instagram' && $igId) {
                        $isIgConnected = true;
                    }

                    FacebookPage::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'page_id' => $page['id'],
                        ],
                        [
                            'name' => $page['name'],
                            'access_token' => $page['access_token'],
                            'instagram_account_id' => $igId,
                            'instagram_username' => $igUser,
                            'is_instagram_connected' => $isIgConnected,
                        ]
                    );

                    if ($flow === 'instagram' && $igId) {
                        $igSynced++;
                    } else {
                        $pagesSynced++;
                    }
                }

                if ($flow === 'instagram') {
                    if ($igSynced > 0) {
                        return redirect()->route('instagram.index')->with('success', "Instagram Account Connected! $igSynced accounts synced.");
                    } else {
                        return redirect()->route('instagram.index')->with('error', "No linked Instagram Business accounts found on your Facebook Pages.");
                    }
                } else {
                    return redirect()->route('facebook.index')->with('success', "Facebook Account Connected! $pagesSynced pages synced.");
                }
            } else {
                // Fetch granted permissions to see what Facebook actually approved
                $permissions = Http::get("https://graph.facebook.com/v19.0/me/permissions", [
                    'access_token' => $fbUser->token
                ])->json();

                \Illuminate\Support\Facades\Log::error('Facebook NO PAGES:', ['pages' => $pagesData, 'permissions' => $permissions]);

                $permsString = '';
                if (isset($permissions['data'])) {
                    $granted = array_filter($permissions['data'], fn($p) => $p['status'] === 'granted');
                    $permsString = implode(', ', array_column($granted, 'permission'));
                }

                $targetIndex = $flow === 'instagram' ? 'instagram.index' : 'facebook.index';
                return redirect()->route($targetIndex)->with('error', "No Pages Found. Granted permissions: [{$permsString}]. Ensure you select the pages during login.");
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    public function destroy(FacebookAccount $account)
    {
        // Delete associated pages first
        FacebookPage::where('user_id', auth()->id())->delete();
        
        // Delete the account
        $account->delete();

        return redirect()->back()->with('success', 'Facebook account disconnected successfully.');
    }
}