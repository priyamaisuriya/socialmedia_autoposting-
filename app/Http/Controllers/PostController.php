<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Media;
use App\Models\FacebookPage;
use App\Services\FacebookApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    protected $facebookApi;

    public function __construct(FacebookApiService $facebookApi)
    {
        $this->facebookApi = $facebookApi;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $isArchived = $request->query('archived', false);
        
        $posts = Post::where('user_id', $user->id)
            ->where('is_archived', $isArchived)
            ->with(['facebookPage', 'media'])
            ->latest()
            ->get();

        $totalPosts = $posts->count();
        $totalLikes = 0;
        $totalComments = 0;
        foreach ($posts as $post) {
            if ($post->status === 'success' && $post->facebook_post_id && $post->facebookPage) {
                try {
                    $engagement = $this->facebookApi->getPostEngagement(
                        $post->facebook_post_id,
                        $post->facebookPage->access_token
                    );
                    
                    $likes = $engagement['reactions']['summary']['total_count'] ?? $engagement['likes']['summary']['total_count'] ?? 0;
                    $comments = $engagement['comments']['summary']['total_count'] ?? $engagement['comments_count'] ?? 0;
                    
                    $post->update([
                        'likes_count' => $likes,
                        'comments_count' => $comments,
                    ]);

                    $post->dynamic_likes = $likes;
                    $post->dynamic_comments = $comments;
                    
                    $totalLikes += $likes;
                    $totalComments += $comments;
                } catch (\Exception $e) {
                    $post->dynamic_likes = $post->likes_count;
                    $post->dynamic_comments = $post->comments_count;
                    $totalLikes += $post->likes_count;
                    $totalComments += $post->comments_count;
                }
            } else {
                $post->dynamic_likes = 0;
                $post->dynamic_comments = 0;
            }
        }

        $totalEngagement = $totalLikes + $totalComments;
        $avgEngagement = $totalPosts > 0 ? round($totalEngagement / $totalPosts, 1) : 0;

        $stats = [
            'total_posts' => $totalPosts,
            'total_likes' => $totalLikes,
            'total_comments' => $totalComments,
            'avg_engagement' => $avgEngagement
        ];

        return view('posts.index', compact('posts', 'stats'));
    }

    public function create()
    {
        $pages = auth()->user()->facebookPages;
        return view('posts.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'message' => 'required|string',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:51200',
        ]);

        $page = FacebookPage::findOrFail($request->facebook_page_id);

        // Process Message with Hashtags and Tags
        $fullMessage = $request->message;
        if ($request->tags) {
            $tags = array_map(function($tag) {
                return ltrim(trim($tag), '@');
            }, explode(',', $request->tags));
            foreach ($tags as $tag) {
                $fullMessage .= " @[" . $tag . "]";
            }
        }
        if ($request->hashtags) {
            $hashtags = array_map(function($tag) {
                $tag = trim($tag);
                return (strpos($tag, '#') === 0) ? $tag : '#' . $tag;
            }, explode(' ', $request->hashtags));
            $fullMessage .= "\n\n" . implode(' ', array_filter($hashtags));
        }

        // 1. Save Post Locally
        $post = Post::create([
            'user_id' => auth()->id(),
            'facebook_page_id' => $page->id,
            'message' => $fullMessage,
            'status' => 'pending',
            'hide_likes' => $request->has('hide_likes'),
            'hide_comments' => $request->has('hide_comments'),
        ]);

        // 2. Handle Media
        $facebookPostId = null;
        $mediaFiles = [];

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');
                $type = str_contains($file->getMimeType(), 'video') ? 'video' : 'image';
                
                Media::create([
                    'post_id' => $post->id,
                    'file_path' => $path,
                    'media_type' => $type,
                ]);

                $mediaFiles[] = ['path' => storage_path('app/public/' . $path), 'type' => $type];
            }
        }

        // 3. Publish to Facebook
        try {
            if (empty($mediaFiles)) {
                $result = $this->facebookApi->publishPost($page, $fullMessage);
            } else {
                $firstMedia = $mediaFiles[0];
                if ($firstMedia['type'] === 'image') {
                    $result = $this->facebookApi->publishPhoto($page, $fullMessage, $firstMedia['path']);
                } else {
                    $result = $this->facebookApi->publishVideo($page, $fullMessage, $fullMessage, $firstMedia['path']);
                }
            }

            if (isset($result['id']) || isset($result['post_id'])) {
                $fbId = $result['id'] ?? $result['post_id'];
                $post->update([
                    'facebook_post_id' => $fbId,
                    'status' => 'success',
                ]);
                return redirect()->route('dashboard')->with('success', 'Post published successfully!');
            } else {
                $post->update(['status' => 'failed']);
                return redirect()->back()->with('error', 'Facebook API Error: ' . json_encode($result));
            }

        } catch (\Exception $e) {
            $post->update(['status' => 'failed']);
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }

    public function show(Post $post)
    {
        $post->load(['facebookPage', 'media', 'comments' => function($query) {
            $query->whereNull('parent_id')->with('replies');
        }]);
        
        if ($post->status === 'success' && $post->facebook_post_id && $post->facebookPage) {
            try {
                $engagement = $this->facebookApi->getPostEngagement(
                    $post->facebook_post_id,
                    $post->facebookPage->access_token
                );
                
                $likes = $engagement['reactions']['summary']['total_count'] ?? $engagement['likes']['summary']['total_count'] ?? 0;
                $comments = $engagement['comments']['summary']['total_count'] ?? $engagement['comments_count'] ?? 0;
                
                $post->update([
                    'likes_count' => $likes,
                    'comments_count' => $comments,
                ]);

                $post->dynamic_likes = $likes;
                $post->dynamic_comments = $comments;
            } catch (\Exception $e) {
                $post->dynamic_likes = $post->likes_count;
                $post->dynamic_comments = $post->comments_count;
            }
        } else {
            $post->dynamic_likes = 0;
            $post->dynamic_comments = 0;
        }

        return view('posts.show', compact('post'));
    }

    public function destroy(Post $post)
    {
        $post->load('facebookPage');

        if ($post->status === 'success' && $post->facebook_post_id && $post->facebookPage) {
            $result = $this->facebookApi->deletePost(
                $post->facebook_post_id,
                $post->facebookPage->access_token
            );

            if (isset($result['error'])) {
                \Illuminate\Support\Facades\Log::warning('Facebook post delete failed: ' . $result['error']['message']);
            }
        }

        $post->media()->delete();
        $post->comments()->delete();
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }

    public function toggleArchive(Post $post)
    {
        $post->is_archived = !$post->is_archived;
        $post->save();

        if ($post->status === 'success' && $post->facebook_post_id && $post->facebookPage) {
            $result = $this->facebookApi->togglePostVisibility(
                $post->facebook_post_id,
                $post->is_archived,
                $post->facebookPage->access_token
            );

            if (isset($result['error'])) {
                \Illuminate\Support\Facades\Log::warning('Facebook post visibility toggle failed: ' . $result['error']['message']);
            }
        }

        $action = $post->is_archived ? 'archived' : 'unarchived';
        return redirect()->back()->with('success', "Post $action successfully and synced with Facebook!");
    }
}