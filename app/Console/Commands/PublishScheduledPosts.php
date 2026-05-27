<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Services\FacebookApiService;
use Illuminate\Support\Facades\Log;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts to Facebook and Instagram';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\PostPublisherService $publisher)
    {
        $posts = Post::whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->where(function($query) {
                $query->where('status', 'scheduled')
                      ->orWhere('instagram_status', 'scheduled');
            })
            ->with(['facebookPage', 'media'])
            ->get();

        foreach ($posts as $post) {
            $page = $post->facebookPage;
            if (!$page) continue;

            $this->info("Publishing scheduled post ID: {$post->id}");
            
            // The service handles both Facebook and Instagram internally based on flags
            $publisher->publish($post);

            $this->info("Finished post ID: {$post->id}");
        }
        
        $this->info("Processed " . count($posts) . " scheduled posts.");
    }
}
