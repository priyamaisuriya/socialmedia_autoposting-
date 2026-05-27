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
            if ($post) {
                // Sync Facebook Comments
                if ($post->facebook_post_id && $post->facebookPage) {
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
                        \Illuminate\Support\Facades\Log::error('Auto-sync Facebook comments failed: ' . $e->getMessage());
                    }
                }
                
                // Sync Instagram Comments
                if ($post->instagram_post_id && $post->facebookPage && $post->facebookPage->instagram_account_id) {
                    try {
                        $igCommentsData = $this->facebookApi->getInstagramComments(
                            $post->instagram_post_id,
                            $post->facebookPage->access_token
                        );
                        
                        if (isset($igCommentsData['data'])) {
                            foreach ($igCommentsData['data'] as $igCommentData) {
                                $this->saveInstagramCommentAndReplies($igCommentData, $post->id);
                            }
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Auto-sync Instagram comments failed: ' . $e->getMessage());
                    }
                }
            }
        } else {
            // Auto-sync comments for all successful posts of this user (successful on FB or IG)
            $posts = Post::with('facebookPage')
                ->where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('status', 'success')
                          ->orWhere('instagram_status', 'success');
                })
                ->get();
            
            foreach ($posts as $post) {
                // Facebook Comments Sync
                if ($post->facebook_post_id && $post->facebookPage) {
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
                        \Illuminate\Support\Facades\Log::error('Auto-sync comments failed for Facebook post ' . $post->id . ': ' . $e->getMessage());
                    }
                }

                // Instagram Comments Sync
                if ($post->instagram_post_id && $post->facebookPage && $post->facebookPage->instagram_account_id) {
                    try {
                        $igCommentsData = $this->facebookApi->getInstagramComments(
                            $post->instagram_post_id,
                            $post->facebookPage->access_token
                        );
                        
                        if (isset($igCommentsData['data'])) {
                            foreach ($igCommentsData['data'] as $igCommentData) {
                                $this->saveInstagramCommentAndReplies($igCommentData, $post->id);
                            }
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Auto-sync comments failed for Instagram post ' . $post->id . ': ' . $e->getMessage());
                    }
                }
            }
        }

        // Query all successful posts to populate the dropdown
        $allPosts = Post::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('status', 'success')
                      ->orWhere('instagram_status', 'success');
            })
            ->where(function($query) {
                $query->whereNotNull('facebook_post_id')
                      ->orWhereNotNull('instagram_post_id');
            })
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
        
        $syncedPlatforms = [];
        $errors = [];

        // Sync Facebook
        if ($post->facebook_post_id && $post->facebookPage) {
            $commentsData = $this->facebookApi->getComments(
                $post->facebook_post_id, 
                $post->facebookPage->access_token
            );

            if (isset($commentsData['error'])) {
                $errors[] = 'Facebook Error: ' . $commentsData['error']['message'];
            } elseif (isset($commentsData['data'])) {
                foreach ($commentsData['data'] as $commentData) {
                    $this->saveCommentAndReplies($commentData, $post->id);
                }
                $syncedPlatforms[] = 'Facebook';
            }
        }

        // Sync Instagram
        if ($post->instagram_post_id && $post->facebookPage && $post->facebookPage->instagram_account_id) {
            $igCommentsData = $this->facebookApi->getInstagramComments(
                $post->instagram_post_id,
                $post->facebookPage->access_token
            );

            if (isset($igCommentsData['error'])) {
                $errors[] = 'Instagram Error: ' . $igCommentsData['error']['message'];
            } elseif (isset($igCommentsData['data'])) {
                foreach ($igCommentsData['data'] as $igCommentData) {
                    $this->saveInstagramCommentAndReplies($igCommentData, $post->id);
                }
                $syncedPlatforms[] = 'Instagram';
            }
        }

        if (empty($syncedPlatforms) && !empty($errors)) {
            return redirect()->route('comments.index', ['post_id' => $post->id])->with('error', implode(' | ', $errors));
        }

        $successMsg = 'Comments synchronized for ' . implode(' & ', $syncedPlatforms) . '!';
        if (!empty($errors)) {
            $successMsg .= ' (with some errors: ' . implode(', ', $errors) . ')';
        }

        return redirect()->route('comments.index', ['post_id' => $post->id])->with('success', $successMsg);
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

        if ($comment->platform === 'instagram') {
            $result = $this->facebookApi->replyToInstagramComment(
                $comment->facebook_comment_id,
                $request->message,
                $post->facebookPage->access_token
            );

            if (isset($result['error'])) {
                return back()->with('error', 'Instagram Error: ' . $result['error']['message']);
            }

            // Save reply locally
            Comment::create([
                'post_id' => $post->id,
                'platform' => 'instagram',
                'parent_id' => $comment->id,
                'facebook_comment_id' => $result['id'] ?? ($comment->facebook_comment_id . '_' . time()),
                'user_name' => $post->facebookPage->instagram_username ?? 'Instagram Admin',
                'message' => $request->message,
            ]);

            return back()->with('success', 'Reply sent successfully to Instagram!');
        } else {
            $result = $this->facebookApi->replyToComment(
                $comment->facebook_comment_id,
                $request->message,
                $post->facebookPage->access_token
            );

            if (isset($result['error'])) {
                return back()->with('error', 'Facebook Error: ' . $result['error']['message']);
            }

            // Save reply locally
            Comment::create([
                'post_id' => $post->id,
                'platform' => 'facebook',
                'parent_id' => $comment->id,
                'facebook_comment_id' => $result['id'] ?? ($comment->facebook_comment_id . '_' . time()),
                'user_name' => $post->facebookPage->name,
                'message' => $request->message,
            ]);

            return back()->with('success', 'Reply sent successfully to Facebook!');
        }
    }

    /**
     * Helper to save a Facebook comment and its nested replies.
     */
    private function saveCommentAndReplies($commentData, $postId)
    {
        $comment = Comment::updateOrCreate(
            ['facebook_comment_id' => $commentData['id']],
            [
                'post_id' => $postId,
                'platform' => 'facebook',
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
                        'platform' => 'facebook',
                        'parent_id' => $comment->id,
                        'user_name' => $replyData['from']['name'] ?? 'Anonymous',
                        'message' => $replyData['message'],
                        'created_at' => isset($replyData['created_time']) ? \Carbon\Carbon::parse($replyData['created_time']) : now(),
                    ]
                );
            }
        }
    }

    /**
     * Helper to save an Instagram comment and its nested replies.
     */
    private function saveInstagramCommentAndReplies($commentData, $postId)
    {
        $comment = Comment::updateOrCreate(
            ['facebook_comment_id' => $commentData['id']],
            [
                'post_id' => $postId,
                'platform' => 'instagram',
                'user_name' => $commentData['username'] ?? 'Anonymous',
                'message' => $commentData['text'] ?? '',
                'created_at' => isset($commentData['timestamp']) ? \Carbon\Carbon::parse($commentData['timestamp']) : now(),
            ]
        );

        if (isset($commentData['replies']['data'])) {
            foreach ($commentData['replies']['data'] as $replyData) {
                Comment::updateOrCreate(
                    ['facebook_comment_id' => $replyData['id']],
                    [
                        'post_id' => $postId,
                        'platform' => 'instagram',
                        'parent_id' => $comment->id,
                        'user_name' => $replyData['username'] ?? 'Anonymous',
                        'message' => $replyData['text'] ?? '',
                        'created_at' => isset($replyData['timestamp']) ? \Carbon\Carbon::parse($replyData['timestamp']) : now(),
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

        if ($comment->platform === 'instagram') {
            // Call Instagram API to delete the comment
            $result = $this->facebookApi->deleteInstagramComment(
                $comment->facebook_comment_id,
                $post->facebookPage->access_token
            );

            if (isset($result['error'])) {
                \Illuminate\Support\Facades\Log::warning('Instagram comment delete failed: ' . $result['error']['message']);
            }
        } else {
            // Call Facebook API to delete the comment
            $result = $this->facebookApi->deleteComment(
                $comment->facebook_comment_id,
                $post->facebookPage->access_token
            );

            if (isset($result['error'])) {
                \Illuminate\Support\Facades\Log::warning('Facebook comment delete failed: ' . $result['error']['message']);
            }
        }

        // Delete from local database (including nested replies)
        $comment->replies()->delete();
        $comment->delete();

        $platformName = $comment->platform === 'instagram' ? 'Instagram' : 'Facebook';
        return back()->with('success', "Comment deleted successfully from {$platformName}!");
    }
}
