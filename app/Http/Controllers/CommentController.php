<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Services\FacebookApiService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $facebookApi;

    public function __construct(FacebookApiService $facebookApi)
    {
        $this->facebookApi = $facebookApi;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Auto-sync comments for selected post if post_id is provided
        if ($request->has('post_id')) {
            $post = Post::with(['facebookPage', 'media'])->where('user_id', $user->id)->find($request->post_id);
            if ($post && $post->facebook_post_id) {
                try {
                    $commentsData = $this->facebookApi->getComments(
                        $post->facebook_post_id, 
                        $post->facebookPage->access_token
                    );
                    
                    if (isset($commentsData['data'])) {
                        foreach ($commentsData['data'] as $commentData) {
                            $this->saveCommentAndReplies($commentData, $post->id);
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Auto-sync comments failed: ' . $e->getMessage());
                }
            }
        } else {
            // Auto-sync comments for all successful posts of this user
            $posts = Post::with('facebookPage')
                ->where('user_id', $user->id)
                ->where('status', 'success')
                ->whereNotNull('facebook_post_id')
                ->get();
            
            foreach ($posts as $post) {
                try {
                    $commentsData = $this->facebookApi->getComments(
                        $post->facebook_post_id, 
                        $post->facebookPage->access_token
                    );
                    
                    if (isset($commentsData['data'])) {
                        foreach ($commentsData['data'] as $commentData) {
                            $this->saveCommentAndReplies($commentData, $post->id);
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Auto-sync comments failed for post ' . $post->id . ': ' . $e->getMessage());
                }
            }
        }

        // Query all successful posts to populate the dropdown
        $allPosts = Post::where('user_id', $user->id)
            ->where('status', 'success')
            ->whereNotNull('facebook_post_id')
            ->with(['facebookPage', 'media'])
            ->latest()
            ->get();

        // Query only root comments (where parent_id is null)
        $query = Comment::whereNull('parent_id')->whereHas('post', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        if ($request->has('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        // Eager load post, facebookPage, media, and replies
        $comments = $query->with(['post.facebookPage', 'post.media', 'replies'])->latest()->get();

        return view('comments.index', compact('comments', 'post', 'allPosts'));
    }

    public function sync($postId)
    {
        $post = Post::with('facebookPage')->findOrFail($postId);
        
        if (!$post->facebook_post_id) {
            return back()->with('error', 'This post has no Facebook ID.');
        }

        $commentsData = $this->facebookApi->getComments(
            $post->facebook_post_id, 
            $post->facebookPage->access_token
        );

        \Illuminate\Support\Facades\Log::info('Facebook Comments Response:', ['response' => $commentsData, 'post_id' => $post->facebook_post_id]);

        if (isset($commentsData['error'])) {
            return redirect()->route('comments.index', ['post_id' => $post->id])->with('error', 'Facebook API Error: ' . $commentsData['error']['message']);
        }

        if (isset($commentsData['data'])) {
            foreach ($commentsData['data'] as $commentData) {
                $this->saveCommentAndReplies($commentData, $post->id);
            }
            return redirect()->route('comments.index', ['post_id' => $post->id])->with('success', 'Comments synchronized from Facebook!');
        }

        return redirect()->route('comments.index', ['post_id' => $post->id])->with('error', 'Failed to fetch comments. Unknown response.');
    }

    public function reply(Request $request, Comment $comment)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $post = $comment->post()->with('facebookPage')->first();
        if (!$post || !$post->facebookPage) {
            return back()->with('error', 'Connected Facebook page not found.');
        }

        $result = $this->facebookApi->replyToComment(
            $comment->facebook_comment_id,
            $request->message,
            $post->facebookPage->access_token
        );

        if (isset($result['error'])) {
            return back()->with('error', 'Facebook Error: ' . $result['error']['message']);
        }

        // Save reply locally as well to show it instantly!
        Comment::create([
            'post_id' => $post->id,
            'parent_id' => $comment->id,
            'facebook_comment_id' => $result['id'] ?? ($comment->facebook_comment_id . '_' . time()),
            'user_name' => $post->facebookPage->name, // Sent as the Page Admin!
            'message' => $request->message,
        ]);

        return back()->with('success', 'Reply sent successfully to Facebook!');
    }

    /**
     * Helper to save a comment and its nested replies.
     */
    private function saveCommentAndReplies($commentData, $postId)
    {
        $comment = Comment::updateOrCreate(
            ['facebook_comment_id' => $commentData['id']],
            [
                'post_id' => $postId,
                'user_name' => $commentData['from']['name'] ?? 'Anonymous',
                'message' => $commentData['message'],
                'created_at' => isset($commentData['created_time']) ? \Carbon\Carbon::parse($commentData['created_time']) : now(),
            ]
        );

        if (isset($commentData['comments']['data'])) {
            foreach ($commentData['comments']['data'] as $replyData) {
                Comment::updateOrCreate(
                    ['facebook_comment_id' => $replyData['id']],
                    [
                        'post_id' => $postId,
                        'parent_id' => $comment->id,
                        'user_name' => $replyData['from']['name'] ?? 'Anonymous',
                        'message' => $replyData['message'],
                        'created_at' => isset($replyData['created_time']) ? \Carbon\Carbon::parse($replyData['created_time']) : now(),
                    ]
                );
            }
        }
    }

    public function destroy(Comment $comment)
    {
        $post = $comment->post()->with('facebookPage')->first();
        if (!$post || !$post->facebookPage) {
            return back()->with('error', 'Connected Facebook page not found.');
        }

        // Call Facebook API to delete the comment
        $result = $this->facebookApi->deleteComment(
            $comment->facebook_comment_id,
            $post->facebookPage->access_token
        );

        if (isset($result['error'])) {
            \Illuminate\Support\Facades\Log::warning('Facebook comment delete failed: ' . $result['error']['message']);
        }

        // Delete from local database (including nested replies)
        $comment->replies()->delete();
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully from Facebook!');
    }
}
