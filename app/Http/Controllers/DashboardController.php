<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FacebookAccount;
use App\Models\FacebookPage;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Media;
use App\Services\FacebookApiService;

class DashboardController extends Controller
{
    public function index(FacebookApiService $facebookApi)
    {
        $user = auth()->user();
        
        // Fetch all successful posts to aggregate live likes from Meta Graph API
        $posts = Post::where('user_id', $user->id)
            ->where('status', 'success')
            ->whereNotNull('facebook_post_id')
            ->with('facebookPage')
            ->get();

        $totalLikes = 0;

        foreach ($posts as $post) {
            if ($post->facebookPage) {
                try {
                    $engagement = $facebookApi->getPostEngagement(
                        $post->facebook_post_id,
                        $post->facebookPage->access_token
                    );
                    
                    $likes = $engagement['reactions']['summary']['total_count'] ?? $engagement['likes']['summary']['total_count'] ?? 0;
                    $totalLikes += $likes;
                } catch (\Exception $e) {
                    // Fail silently to keep the dashboard extremely resilient
                }
            }
        }

        // Fetch page details (fan count / total page likes) across all connected pages
        $totalPagesLikes = 0;
        $connectedPages = FacebookPage::where('user_id', $user->id)->get();

        foreach ($connectedPages as $page) {
            try {
                $details = $facebookApi->getPageDetails(
                    $page->page_id,
                    $page->access_token
                );
                $pageLikes = $details['fan_count'] ?? 0;
                $totalPagesLikes += $pageLikes;
            } catch (\Exception $e) {
                // Fail silently
            }
        }
        
        $stats = [
            'total_posts' => $user->posts()->count(),
            'total_likes' => $totalLikes,
            'page_likes' => $totalPagesLikes,
            'total_images' => Media::whereHas('post', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('media_type', 'image')->count(),
            'total_videos' => Media::whereHas('post', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('media_type', 'video')->count(),
            'total_comments' => Comment::whereHas('post', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
        ];

        $recentPosts = $user->posts()
            ->with(['facebookPage', 'media'])
            ->latest()
            ->take(8)
            ->get();

        $facebookAccounts = $user->facebookAccounts;

        $recentComments = Comment::whereHas('post', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('post')->latest()->take(5)->get();

        return view('dashboard', compact(
            'stats',
            'recentPosts',
            'facebookAccounts',
            'recentComments'
        ));
    }
}