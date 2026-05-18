<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\FacebookPage;

class FacebookApiService
{
    protected $baseUrl = 'https://graph.facebook.com/v19.0';

    /**
     * Fetch all pages for a user using their user access token.
     */
    public function getPages($userAccessToken)
    {
        $response = Http::get("{$this->baseUrl}/me/accounts", [
            'access_token' => $userAccessToken
        ]);

        return $response->json();
    }

    /**
     * Publish a post to a page.
     */
    public function publishPost(FacebookPage $page, $message, $link = null)
    {
        $params = [
            'message' => $message,
            'access_token' => $page->access_token
        ];

        if ($link) {
            $params['link'] = $link;
        }

        $response = Http::post("{$this->baseUrl}/{$page->page_id}/feed", $params);

        return $response->json();
    }

    /**
     * Publish a photo to a page.
     */
    public function publishPhoto(FacebookPage $page, $message, $photoPath)
    {
        $response = Http::attach('source', file_get_contents($photoPath), 'photo.jpg')
            ->post("{$this->baseUrl}/{$page->page_id}/photos", [
                'message' => $message,
                'access_token' => $page->access_token
            ]);

        return $response->json();
    }

    /**
     * Publish a video to a page.
     */
    public function publishVideo(FacebookPage $page, $title, $description, $videoPath)
    {
        $response = Http::attach('source', file_get_contents($videoPath), 'video.mp4')
            ->post("{$this->baseUrl}/{$page->page_id}/videos", [
                'title' => $title,
                'description' => $description,
                'access_token' => $page->access_token
            ]);

        return $response->json();
    }

    /**
     * Fetch comments for a post.
     */
    public function getComments($facebookPostId, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/{$facebookPostId}/comments", [
                'fields' => 'id,message,from,created_time,comments{id,message,from,created_time}',
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Request timed out or failed: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Fetch details for a page (fan count / followers).
     */
    public function getPageDetails($pageId, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/{$pageId}", [
                'fields' => 'fan_count,followers_count',
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Fetch engagement metrics for a post.
     */
    public function getPostEngagement($facebookPostId, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/{$facebookPostId}", [
                'fields' => 'likes.summary(total_count).limit(0),comments.summary(total_count).limit(0)',
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Send a reply to a specific comment.
     */
    public function replyToComment($commentId, $message, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/{$commentId}/comments", [
                'message' => $message,
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Failed to send reply: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Delete a comment or reply on Facebook.
     */
    public function deleteComment($commentId, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->delete("{$this->baseUrl}/{$commentId}", [
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Failed to delete comment: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Update a post's message on Facebook.
     */
    public function updatePost($facebookPostId, $message, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/{$facebookPostId}", [
                'message' => $message,
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Failed to update post on Facebook: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Delete a post on Facebook.
     */
    public function deletePost($facebookPostId, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->delete("{$this->baseUrl}/{$facebookPostId}", [
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Failed to delete post from Facebook: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Archive/Unarchive (hide/unhide) a post on Facebook timeline.
     */
    public function togglePostVisibility($facebookPostId, $isHidden, $pageAccessToken)
    {
        try {
            $targetId = $facebookPostId;

            // If it's a photo node (no underscore), we must fetch the feed story ID to hide it from the timeline.
            if (strpos($facebookPostId, '_') === false) {
                $storyRes = Http::timeout(30)->get("{$this->baseUrl}/{$facebookPostId}", [
                    'fields' => 'page_story_id',
                    'access_token' => $pageAccessToken
                ]);
                
                $storyData = $storyRes->json();
                if (isset($storyData['page_story_id'])) {
                    $targetId = $storyData['page_story_id'];
                }
            }

            $response = Http::timeout(30)->post("{$this->baseUrl}/{$targetId}", [
                'timeline_visibility' => $isHidden ? 'hidden' : 'forced_allow',
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Failed to toggle visibility on Facebook: ' . $e->getMessage()
                ]
            ];
        }
    }
}
