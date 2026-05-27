<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Media;
use App\Models\FacebookPage;
use App\Services\FacebookApiService;

class PostPublisherService
{
    protected $facebookApi;

    public function __construct(FacebookApiService $facebookApi)
    {
        $this->facebookApi = $facebookApi;
    }

    /**
     * Publish a single post that is ready to be published.
     * This handles both Feed posts and Stories.
     *
     * @param Post $post
     * @param bool $isStory Whether this post is specifically marked as a Story instead of a Feed post
     * @return void
     */
    public function publish(Post $post, $isStory = false)
    {
        // Get the associated page
        $page = FacebookPage::find($post->facebook_page_id);
        if (!$page) {
            $post->update(['status' => 'failed', 'instagram_status' => 'failed']);
            return;
        }

        // Get media files
        $mediaFiles = [];
        $mediaRecords = Media::where('post_id', $post->id)->get();
        foreach ($mediaRecords as $media) {
            $mediaFiles[] = ['path' => $media->file_path, 'type' => $media->media_type];
        }

        if ($isStory) {
            $this->publishStory($post, $page, $mediaFiles);
        } else {
            $this->publishFeed($post, $page, $mediaFiles);
        }
    }

    /**
     * Handles standard feed, video, and reel posts.
     */
    protected function publishFeed(Post $post, FacebookPage $page, array $mediaFiles)
    {
        $facebookSuccess = false;
        $instagramSuccess = false;

        // 1. Publish to Facebook
        if ($post->post_to_facebook) {
            try {
                if (empty($mediaFiles)) {
                    $result = $this->facebookApi->publishPost($page, $post->message);
                } else {
                    $firstMedia = $mediaFiles[0];
                    if ($post->post_type === 'reel') {
                        $result = $this->facebookApi->publishFacebookReel($page, $post->message, $firstMedia['path']);
                    } elseif ($firstMedia['type'] === 'image') {
                        $result = $this->facebookApi->publishPhoto($page, $post->message, $firstMedia['path']);
                    } else {
                        $result = $this->facebookApi->publishVideo($page, $post->message, $post->message, $firstMedia['path']);
                    }
                }

                if (isset($result['id']) || isset($result['post_id'])) {
                    $fbId = $result['id'] ?? $result['post_id'];
                    $post->update([
                        'facebook_post_id' => $fbId,
                        'status' => 'success',
                    ]);
                    $facebookSuccess = true;
                } else {
                    $fbErrorMsg = $result['error']['message'] ?? json_encode($result);
                    $post->update(['status' => 'failed']);
                }
            } catch (\Exception $e) {
                $post->update(['status' => 'failed']);
            }
        }

        // 2. Publish to Instagram
        if ($post->post_to_instagram && !empty($mediaFiles)) {
            try {
                $firstMedia = $mediaFiles[0];
                $result = $this->facebookApi->publishToInstagram(
                    $page, 
                    $post->message, 
                    $firstMedia['path'], 
                    $firstMedia['type'],
                    $post->post_type ?? 'feed'
                );

                if (isset($result['id'])) {
                    $post->update([
                        'instagram_post_id' => $result['id'],
                        'instagram_status' => 'success',
                    ]);
                    $instagramSuccess = true;
                } else {
                    $igErrorMsg = $result['error']['message'] ?? json_encode($result);
                    $post->update([
                        'instagram_status' => 'failed',
                        'instagram_error' => $igErrorMsg
                    ]);
                }
            } catch (\Exception $e) {
                $post->update([
                    'instagram_status' => 'failed',
                    'instagram_error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handles Story publishing
     */
    protected function publishStory(Post $post, FacebookPage $page, array $mediaFiles)
    {
        if (empty($mediaFiles)) {
            $post->update(['status' => 'failed', 'instagram_status' => 'failed']);
            return;
        }

        $firstMedia = $mediaFiles[0];

        // Facebook Story
        if ($post->post_to_facebook) {
            try {
                $result = $this->facebookApi->publishFacebookStory(
                    $page,
                    $firstMedia['path'],
                    $firstMedia['type']
                );

                if (isset($result['id']) || isset($result['success'])) {
                    $post->update([
                        'facebook_post_id' => $result['id'] ?? null,
                        'status' => 'success'
                    ]);
                } else {
                    $post->update(['status' => 'failed']);
                }
            } catch (\Exception $e) {
                $post->update(['status' => 'failed']);
            }
        }

        // Instagram Story
        if ($post->post_to_instagram) {
            try {
                $result = $this->facebookApi->publishToInstagram(
                    $page,
                    '', // Stories don't support captions
                    $firstMedia['path'],
                    $firstMedia['type'],
                    'story'
                );

                if (isset($result['id'])) {
                    $post->update([
                        'instagram_post_id' => $result['id'],
                        'instagram_status' => 'success'
                    ]);
                } else {
                    $igErrorMsg = $result['error']['message'] ?? json_encode($result);
                    $post->update([
                        'instagram_status' => 'failed',
                        'instagram_error' => $igErrorMsg
                    ]);
                }
            } catch (\Exception $e) {
                $post->update([
                    'instagram_status' => 'failed',
                    'instagram_error' => $e->getMessage()
                ]);
            }
        }
    }
}
