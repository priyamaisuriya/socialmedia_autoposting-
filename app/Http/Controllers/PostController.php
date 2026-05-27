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
        // Increase execution time limit for long-running video uploads
        set_time_limit(300);

        $request->validate([
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'message' => 'nullable|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:51200',
            'scheduled_at' => 'nullable|date|after_or_equal:now',
        ]);

        $page = FacebookPage::findOrFail($request->facebook_page_id);

        $postToFacebook = $request->has('post_to_facebook');
        $postToInstagram = $request->has('post_to_instagram');

        // Validation 1: At least one platform must be selected
        if (!$postToFacebook && !$postToInstagram) {
            return redirect()->back()->withInput()->with('error', 'Please select at least one platform to publish your post.');
        }

        // Validation 2: If Instagram is selected, check connection and active status
        // Removed validation to allow forcing posts even if DB says it's not linked


        // Validation 3: Instagram requires media (photo or video)
        if ($postToInstagram && !$request->hasFile('media')) {
            return redirect()->back()->withInput()->with('error', 'Instagram requires a media attachment (image or video).');
        }

        // Validation 4: Reels require video
        if ($request->post_type === 'reel') {
            if (!$request->hasFile('media')) {
                return redirect()->back()->withInput()->with('error', 'Reels require a video file.');
            }
            $isInvalidReel = false;
            foreach ($request->file('media') as $file) {
                if (!str_contains($file->getMimeType(), 'video')) {
                    $isInvalidReel = true;
                    break;
                }
            }
            if ($isInvalidReel) {
                return redirect()->back()->withInput()->with('error', 'Reels only support video files (MP4, MOV, etc).');
            }
        }        // Process Message with Hashtags and Tags
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

        $isScheduled = !empty($request->scheduled_at);

        // 1. Save Post Locally
        $post = Post::create([
            'user_id' => auth()->id(),
            'facebook_page_id' => $page->id,
            'message' => $fullMessage,
            'status' => $postToFacebook ? ($isScheduled ? 'scheduled' : 'pending') : 'success',
            'instagram_status' => $postToInstagram ? ($isScheduled ? 'scheduled' : 'pending') : 'success',
            'post_to_facebook' => $postToFacebook,
            'post_to_instagram' => $postToInstagram,
            'hide_likes' => $request->has('hide_likes'),
            'hide_comments' => $request->has('hide_comments'),
            'scheduled_at' => $request->scheduled_at,
        ]);

        // 2. Handle Media
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

                $mediaFiles[] = ['path' => $path, 'type' => $type];
            }
        }

        // 3. Hand off to the Publisher Service (if not scheduled)
        if (!$isScheduled) {
            $publisherService = app(\App\Services\PostPublisherService::class);
            $publisherService->publish($post);

            // 4. Also add to story
            if ($request->has('also_add_to_story') && !empty($mediaFiles)) {
                $relativeMediaPath = $mediaFiles[0]['path'];
                
                $story = \App\Models\Story::create([
                    'user_id' => auth()->id(),
                    'facebook_page_id' => $page->id,
                    'media_path' => $relativeMediaPath,
                    'status' => $postToFacebook ? 'pending' : 'skipped',
                    'instagram_status' => $postToInstagram ? 'pending' : 'skipped',
                    'post_to_facebook' => $postToFacebook,
                    'post_to_instagram' => $postToInstagram,
                ]);

                // Create a temporary Post model representation for the story to use the generic publisher
                $storyPost = new \App\Models\Post([
                    'facebook_page_id' => $page->id,
                    'post_to_facebook' => $postToFacebook,
                    'post_to_instagram' => $postToInstagram,
                    'status' => 'pending',
                    'instagram_status' => 'pending'
                ]);
                $storyPost->id = $story->id; // Pseudo-ID just for media loading, wait no, publisher service reads Media.
                
                // Since our publisher service reads Media based on Post ID, for stories we'll just fall back to directly
                // publishing using the Facebook API inline here, to avoid complex refactoring of the Story model right now.
                
                $type = $mediaFiles[0]['type'];
                $fbStoryType = $type === 'image' ? 'photo' : 'video';

                if ($postToFacebook) {
                    try {
                        $result = $this->facebookApi->publishFacebookStory($page, $mediaFiles[0]['path'], $fbStoryType);
                        if (!isset($result['error'])) {
                            $story->update(['status' => 'success']);
                        } else {
                            $story->update(['status' => 'failed']);
                        }
                    } catch (\Exception $e) {
                        $story->update(['status' => 'failed']);
                    }
                }

                if ($postToInstagram) {
                    try {
                        $result = $this->facebookApi->publishToInstagram($page, '', $mediaFiles[0]['path'], $type, 'story');
                        if (isset($result['id'])) {
                            $story->update(['instagram_status' => 'success']);
                        } else {
                            $story->update(['instagram_status' => 'failed', 'error_message' => $result['error']['message'] ?? json_encode($result)]);
                        }
                    } catch (\Exception $e) {
                        $story->update(['instagram_status' => 'failed', 'error_message' => $e->getMessage()]);
                    }
                }
            }
        }

        // 6. Consolidated Feedback Messages
        $feedbacks = [];
        if ($isScheduled) {
            $feedbacks[] = 'Scheduled for ' . \Carbon\Carbon::parse($request->scheduled_at)->format('Y-m-d H:i');
        } else {
            if ($postToFacebook) {
                $feedbacks[] = $facebookSuccess ? 'Facebook: Success' : "Facebook Failed ({$fbErrorMsg})";
            }
            if ($postToInstagram) {
                $feedbacks[] = $instagramSuccess ? 'Instagram: Success' : "Instagram Failed ({$igErrorMsg})";
            }
        }

        $feedbackMsg = implode(' | ', $feedbacks);

        if (!$isScheduled && (($postToFacebook && !$facebookSuccess) || ($postToInstagram && !$instagramSuccess))) {
            return redirect()->route('dashboard')->with('error', 'Post publishing completed with errors: ' . $feedbackMsg);
        }

        $successMsg = $isScheduled ? 'Post scheduled successfully! ' : 'Post published successfully! ';
        return redirect()->route('dashboard')->with('success', $successMsg . $feedbackMsg);
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

    public function toggleArchive(Request $request, Post $post)
    {
        if (!$post->facebook_post_id) {
            return redirect()->back()->with('error', 'Archiving is only supported for Facebook posts. Instagram does not support programmatic archiving.');
        }

        $post->is_fb_archived = !$post->is_fb_archived;
        $post->is_archived = $post->is_fb_archived;
        
        $message = '';
        if ($post->facebookPage) {
            $result = $this->facebookApi->togglePostVisibility(
                $post->facebook_post_id,
                $post->is_fb_archived,
                $post->facebookPage->access_token
            );

            if (isset($result['error'])) {
                \Illuminate\Support\Facades\Log::warning('Facebook post visibility toggle failed: ' . $result['error']['message']);
                return redirect()->back()->with('error', 'Facebook visibility API error: ' . $result['error']['message']);
            }
            
            $action = $post->is_fb_archived ? 'archived (hidden from timeline)' : 'unarchived (visible on timeline)';
            $message = "Post successfully {$action} on Facebook!";
        }

        $post->save();

        return redirect()->back()->with('success', $message);
    }
}