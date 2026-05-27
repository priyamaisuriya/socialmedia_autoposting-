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
            'fields' => 'name,access_token,instagram_business_account{id,username}',
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
        $localPath = storage_path('app/public/' . $photoPath);
        $response = Http::attach('source', file_get_contents($localPath), 'photo.jpg')
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
        $localPath = storage_path('app/public/' . $videoPath);
        $response = Http::attach('source', file_get_contents($localPath), 'video.mp4')
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

    public function getPublicMediaUrl($mediaPath, $type = 'photo')
    {
        // For local development with Ngrok, Facebook needs a publicly accessible URL
        $localPath = storage_path('app/public/' . $mediaPath);
        
        if (!file_exists($localPath)) {
            throw new \Exception("Media file not found at path: $localPath");
        }

        return \Illuminate\Support\Facades\Cache::remember('public_media_url_' . md5($mediaPath), 300, function () use ($localPath) {
            // Upload to tmpfiles.org to get a direct public link
            $response = \Illuminate\Support\Facades\Http::attach('file', file_get_contents($localPath), basename($localPath))
                            ->post('https://tmpfiles.org/api/v1/upload');
            
            if ($response->successful()) {
                $data = $response->json();
                $url = $data['data']['url'] ?? '';
                // Convert to direct URL by inserting /dl/
                return str_replace('tmpfiles.org/', 'tmpfiles.org/dl/', $url);
            }

            return '';
        }) ?: url('storage/' . $mediaPath);
    }

    public function publishFacebookReel(FacebookPage $page, $caption, $mediaPath)
    {
        try {
            $publicMediaUrl = $this->getPublicMediaUrl($mediaPath, 'video');

            // 1. Init Session
            $initRes = Http::post("{$this->baseUrl}/{$page->page_id}/video_reels", [
                'upload_phase' => 'start',
                'access_token' => $page->access_token
            ]);
            $initData = $initRes->json();
            if (isset($initData['error'])) return $initData;
            $videoId = $initData['video_id'] ?? null;
            $uploadUrl = $initData['upload_url'] ?? null;

            if (!$videoId || !$uploadUrl) return ['error' => ['message' => 'Failed to initialize reel upload']];

            // 2. Upload using file_url header
            $uploadRes = Http::withHeaders([
                'Authorization' => "OAuth {$page->access_token}",
                'file_url' => $publicMediaUrl
            ])->post($uploadUrl);

            // 3. Publish
            $publishRes = Http::post("{$this->baseUrl}/{$page->page_id}/video_reels", [
                'upload_phase' => 'finish',
                'video_id' => $videoId,
                'video_state' => 'PUBLISHED',
                'description' => $caption,
                'access_token' => $page->access_token
            ]);

            return $publishRes->json();

        } catch (\Exception $e) {
            return ['error' => ['message' => 'Failed to publish reel: ' . $e->getMessage()]];
        }
    }

    public function publishFacebookStory(FacebookPage $page, $mediaPath, $mediaType)
    {
        try {
            $publicMediaUrl = $this->getPublicMediaUrl($mediaPath, $mediaType);

            if ($mediaType === 'video') {
                // 1. Init Session
                $initRes = Http::post("{$this->baseUrl}/{$page->page_id}/video_stories", [
                    'upload_phase' => 'start',
                    'access_token' => $page->access_token
                ]);
                $initData = $initRes->json();
                if (isset($initData['error'])) return $initData;
                $videoId = $initData['video_id'] ?? null;
                $uploadUrl = $initData['upload_url'] ?? null;

                if (!$videoId || !$uploadUrl) return ['error' => ['message' => 'Failed to initialize video story upload']];

                // 2. Upload using file_url header
                $uploadRes = Http::withHeaders([
                    'Authorization' => "OAuth {$page->access_token}",
                    'file_url' => $publicMediaUrl
                ])->post($uploadUrl);

                // 3. Publish
                $publishRes = Http::post("{$this->baseUrl}/{$page->page_id}/video_stories", [
                    'upload_phase' => 'finish',
                    'video_id' => $videoId,
                    'access_token' => $page->access_token
                ]);
                return $publishRes->json();

            } else {
                // Photo Story
                // 1. Upload photo as unpublished
                $uploadRes = Http::post("{$this->baseUrl}/{$page->page_id}/photos", [
                    'url' => $publicMediaUrl,
                    'published' => 'false',
                    'access_token' => $page->access_token
                ]);
                $uploadData = $uploadRes->json();
                if (isset($uploadData['error'])) return $uploadData;
                
                $photoId = $uploadData['id'] ?? null;
                if (!$photoId) return ['error' => ['message' => 'Failed to upload photo for story']];

                // 2. Publish story
                $publishRes = Http::post("{$this->baseUrl}/{$page->page_id}/photo_stories", [
                    'photo_id' => $photoId,
                    'access_token' => $page->access_token
                ]);
                return $publishRes->json();
            }
        } catch (\Exception $e) {
            return ['error' => ['message' => 'Failed to publish story: ' . $e->getMessage()]];
        }
    }

    /**
     * Publish photo or video to Instagram Business Account.
     */
    public function publishToInstagram(FacebookPage $page, $caption, $mediaPath, $mediaType = 'image', $postType = 'feed')
    {
        try {
            $igAccountId = $page->instagram_account_id;
            if (!$igAccountId) {
                return ['error' => ['message' => 'No Instagram account connected to this Facebook Page.']];
            }

            // Clean up caption for Instagram (convert Facebook mention syntax @[tag] to standard @tag)
            $caption = preg_replace('/@\[([^\]]+)\]/', '@$1', $caption);

            $publicMediaUrl = $this->getPublicMediaUrl($mediaPath, $mediaType);

            // Step 1: Create the media container
            $containerParams = [
                'access_token' => $page->access_token,
            ];

            if ($postType !== 'story') {
                $containerParams['caption'] = $caption;
            }

            if ($postType === 'story') {
                $containerParams['media_type'] = 'STORIES';
                if ($mediaType === 'video') {
                    $containerParams['video_url'] = $publicMediaUrl;
                } else {
                    $containerParams['image_url'] = $publicMediaUrl;
                }
            } elseif ($postType === 'reel' || $mediaType === 'video') {
                $containerParams['media_type'] = 'REELS';
                $containerParams['video_url'] = $publicMediaUrl;
                $containerParams['share_to_feed'] = true;
            } else {
                $containerParams['image_url'] = $publicMediaUrl;
            }

            $containerRes = Http::post("{$this->baseUrl}/{$igAccountId}/media", $containerParams);
            $containerData = $containerRes->json();

            if (isset($containerData['error'])) {
                return $containerData;
            }

            $creationId = $containerData['id'] ?? null;
            if (!$creationId) {
                return ['error' => ['message' => 'Failed to create Instagram media container.']];
            }

            // For videos, we must wait and poll the container status until it is 'FINISHED'
            if ($mediaType === 'video') {
                $status = 'PENDING';
                $retries = 0;
                while ($status !== 'FINISHED' && $retries < 15) {
                    sleep(3);
                    $statusRes = Http::get("{$this->baseUrl}/{$creationId}", [
                        'fields' => 'status_code',
                        'access_token' => $page->access_token
                    ]);
                    $statusData = $statusRes->json();
                    $status = $statusData['status_code'] ?? 'PENDING';
                    if ($status === 'ERROR') {
                        return ['error' => ['message' => 'Instagram video container processing error.']];
                    }
                    $retries++;
                }
            }

            // Step 2: Publish the media container
            $publishRes = Http::post("{$this->baseUrl}/{$igAccountId}/media_publish", [
                'creation_id' => $creationId,
                'access_token' => $page->access_token
            ]);

            return $publishRes->json();

        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Failed to publish to Instagram: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Fetch comments for an Instagram Business Account media object.
     */
    public function getInstagramComments($igMediaId, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/{$igMediaId}/comments", [
                'fields' => 'id,text,username,timestamp,replies{id,text,username,timestamp}',
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Instagram API timeout/error: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Reply to an Instagram comment.
     */
    public function replyToInstagramComment($igCommentId, $message, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/{$igCommentId}/replies", [
                'message' => $message,
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Instagram reply failed: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Delete an Instagram comment.
     */
    public function deleteInstagramComment($igCommentId, $pageAccessToken)
    {
        try {
            $response = Http::timeout(30)->delete("{$this->baseUrl}/{$igCommentId}", [
                'access_token' => $pageAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => [
                    'message' => 'Instagram comment delete failed: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Fetch Ad Accounts for a user.
     */
    public function getAdAccounts($userAccessToken)
    {
        try {
            $response = Http::get("{$this->baseUrl}/me/adaccounts", [
                'fields' => 'account_id,name,currency,account_status',
                'access_token' => $userAccessToken
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => ['message' => 'Failed to fetch Ad Accounts: ' . $e->getMessage()]];
        }
    }

    /**
     * Create an Ad Campaign.
     */
    public function createAdCampaign($adAccountId, $name, $objective, $dailyBudget, $userAccessToken)
    {
        try {
            // Marketing API uses act_<account_id> format
            $actId = strpos($adAccountId, 'act_') === 0 ? $adAccountId : 'act_' . $adAccountId;
            
            $params = [
                'name' => $name,
                'objective' => $objective, // e.g. OUTCOME_TRAFFIC, OUTCOME_ENGAGEMENT
                'status' => 'PAUSED', // Always create as paused first
                'special_ad_categories' => [], // Must be an array
                'is_adset_budget_sharing_enabled' => false, // Required when not using campaign-level budget
                'access_token' => $userAccessToken
            ];

            // In Facebook Ads, budget is typically set at the Ad Set level, not the Campaign level.
            // Setting it at the Campaign level requires Campaign Budget Optimization (CBO) to be enabled.
            // To prevent "Invalid parameter" errors, we will skip sending the budget to Facebook 
            // for the Campaign object and just let the user set it up in the FB Ads Manager.

            $response = Http::post("{$this->baseUrl}/{$actId}/campaigns", $params);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => ['message' => 'Failed to create Ad Campaign: ' . $e->getMessage()]];
        }
    }

    /**
     * Create an Ad Set.
     */
    public function createAdSet($adAccountId, $campaignId, $name, $dailyBudget, $ageMin, $ageMax, $userAccessToken)
    {
        try {
            $actId = strpos($adAccountId, 'act_') === 0 ? $adAccountId : 'act_' . $adAccountId;
            
            $params = [
                'name' => $name,
                'campaign_id' => $campaignId,
                'status' => 'PAUSED',
                'optimization_goal' => 'REACH', // Generic goal
                'billing_event' => 'IMPRESSIONS',
                'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP', // Auto-bidding
                'daily_budget' => intval((float)$dailyBudget * 100), // in cents
                'targeting' => [
                    'geo_locations' => [
                        'countries' => ['IN'] // Default to India for simplicity
                    ],
                    'age_min' => $ageMin ?? 18,
                    'age_max' => $ageMax ?? 65,
                ],
                'access_token' => $userAccessToken
            ];

            $response = Http::post("{$this->baseUrl}/{$actId}/adsets", $params);
            return $response->json();
        } catch (\Exception $e) {
            return ['error' => ['message' => 'Failed to create Ad Set: ' . $e->getMessage()]];
        }
    }

    /**
     * Upload an image to the Ad Account Library.
     */
    public function uploadAdImage($adAccountId, $imagePath, $userAccessToken)
    {
        try {
            $actId = strpos($adAccountId, 'act_') === 0 ? $adAccountId : 'act_' . $adAccountId;
            $localPath = storage_path('app/public/' . $imagePath);
            
            $response = Http::attach('filename', file_get_contents($localPath), basename($localPath))
                ->post("{$this->baseUrl}/{$actId}/adimages", [
                    'access_token' => $userAccessToken
                ]);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => ['message' => 'Failed to upload Ad Image: ' . $e->getMessage()]];
        }
    }

    /**
     * Create an Ad Creative.
     */
    public function createAdCreative($adAccountId, $pageId, $name, $imageHash, $primaryText, $websiteUrl, $userAccessToken)
    {
        try {
            $actId = strpos($adAccountId, 'act_') === 0 ? $adAccountId : 'act_' . $adAccountId;
            
            // Build the Link Data
            $linkData = [
                'image_hash' => $imageHash,
                'link' => $websiteUrl ?: 'https://facebook.com',
            ];
            
            if ($primaryText) {
                $linkData['message'] = $primaryText;
            }

            $params = [
                'name' => $name,
                'object_story_spec' => [
                    'page_id' => $pageId,
                    'link_data' => $linkData
                ],
                'access_token' => $userAccessToken
            ];

            $response = Http::post("{$this->baseUrl}/{$actId}/adcreatives", $params);
            return $response->json();
        } catch (\Exception $e) {
            return ['error' => ['message' => 'Failed to create Ad Creative: ' . $e->getMessage()]];
        }
    }

    /**
     * Create the final Ad.
     */
    public function createAd($adAccountId, $adSetId, $creativeId, $name, $userAccessToken)
    {
        try {
            $actId = strpos($adAccountId, 'act_') === 0 ? $adAccountId : 'act_' . $adAccountId;
            
            $params = [
                'name' => $name,
                'adset_id' => $adSetId,
                'creative' => [
                    'creative_id' => $creativeId
                ],
                'status' => 'PAUSED',
                'access_token' => $userAccessToken
            ];

            $response = Http::post("{$this->baseUrl}/{$actId}/ads", $params);
            return $response->json();
        } catch (\Exception $e) {
            return ['error' => ['message' => 'Failed to publish Ad: ' . $e->getMessage()]];
        }
    }
}
