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
        return Socialite::driver('facebook')
            ->scopes([
                'public_profile',
                'email',
                'pages_show_list',
                'pages_read_engagement',
                'pages_manage_posts',
                'pages_manage_engagement',
                'publish_video'
            ])
            ->with(['auth_type' => 'rerequest'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();

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
            $pagesSynced = 0;

            if (isset($pagesData['error'])) {
                return redirect()->route('facebook.index')->with('error', 'Facebook API Error: ' . $pagesData['error']['message']);
            }

            if (isset($pagesData['data']) && count($pagesData['data']) > 0) {
                foreach ($pagesData['data'] as $page) {
                    FacebookPage::updateOrCreate(
                        ['page_id' => $page['id']],
                        [
                            'user_id' => $user->id,
                            'name' => $page['name'],
                            'access_token' => $page['access_token'],
                        ]
                    );
                    $pagesSynced++;
                }
                return redirect()->route('dashboard')->with('success', "Facebook Account Connected! $pagesSynced pages synced.");
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

                return redirect()->route('facebook.index')->with('error', "No Pages Found. Granted permissions: [{$permsString}]. Ensure you select the pages during login.");
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