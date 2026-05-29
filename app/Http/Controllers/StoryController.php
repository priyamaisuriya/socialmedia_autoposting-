<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacebookPage;
use App\Services\FacebookApiService;
use App\Models\Story;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StoryController extends Controller
{
    protected $facebookApi;

    public function __construct(FacebookApiService $facebookApi)
    {
        $this->facebookApi = $facebookApi;
    }

    public function index()
    {
        $stories = Story::where('user_id', auth()->id())
            ->with('facebookPage')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('stories.index', compact('stories'));
    }

    public function create()
    {
        // Get user's pages
        $pages = auth()->user()->facebookPages()->get();
        return view('stories.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4,webp,webm|max:51200',
        ]);

        $postToFacebook = $request->has('post_to_facebook');
        $postToInstagram = $request->has('post_to_instagram');

        if (!$postToFacebook && !$postToInstagram) {
            return redirect()->back()->withInput()->with('error', 'Please select at least one platform.');
        }

        $page = FacebookPage::where('id', $request->facebook_page_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Handle Media Upload
        $mediaPath = null;
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $extension = $file->getClientOriginalExtension();
            $type = in_array(strtolower($extension), ['mp4', 'mov', 'wmv', 'flv', 'avi', 'webm']) ? 'video' : 'photo';
            
            // Store file locally in public disk
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $mediaPath = $file->storeAs('uploads/media', $filename, 'public');
        }

        $successMessages = [];
        $errorMessages = [];
        $facebookPostId = null;

        // Publish to Facebook Story
        if ($postToFacebook) {
            try {
                $result = $this->facebookApi->publishFacebookStory($page, $mediaPath, $type);
                
                if (isset($result['error'])) {
                    $errorMessages[] = 'Facebook Error: ' . ($result['error']['message'] ?? json_encode($result['error']));
                } else {
                    $facebookPostId = $result['post_id'] ?? ($result['id'] ?? null);
                    $successMessages[] = 'Successfully posted to Facebook Story.';
                }
            } catch (\Exception $e) {
                Log::error('FB Story Publish Error: ' . $e->getMessage());
                $errorMessages[] = 'Facebook Error: ' . $e->getMessage();
            }
        }

        // Publish to Instagram Story
        if ($postToInstagram) {
            try {
                // post_type = 'story'
                $result = $this->facebookApi->publishToInstagram(
                    $page, 
                    '', // No caption for stories
                    $mediaPath, 
                    $type,
                    'story'
                );
                
                if (isset($result['error'])) {
                    $errorMessages[] = 'Instagram Error: ' . ($result['error']['message'] ?? json_encode($result['error']));
                } else {
                    $successMessages[] = 'Successfully posted to Instagram Story.';
                }
            } catch (\Exception $e) {
                Log::error('IG Story Publish Error: ' . $e->getMessage());
                $errorMessages[] = 'Instagram Error: ' . $e->getMessage();
            }
        }

        // Save record to DB for history
        if (!empty($successMessages) || !empty($errorMessages)) {
            $storyStatus = $postToFacebook ? (isset($facebookPostId) ? 'success' : 'failed') : 'skipped';
            $igStatus = $postToInstagram ? (in_array('Successfully posted to Instagram Story.', $successMessages) ? 'success' : 'failed') : 'skipped';
            
            Story::create([
                'user_id' => auth()->id(),
                'facebook_page_id' => $page->id,
                'facebook_story_id' => $facebookPostId,
                'instagram_story_id' => null, // Instagram API doesn't always return ID easily
                'media_path' => $mediaPath,
                'status' => $storyStatus,
                'instagram_status' => $igStatus,
                'error_message' => implode(' | ', $errorMessages)
            ]);
        }

        // Cleanup local file after uploading to platforms (since it was uploaded to Graph API directly)
        // If you want to keep the file for history, don't delete. Let's keep it for dashboard history.
        // if ($mediaPath && Storage::disk('public')->exists($mediaPath)) {
        //     Storage::disk('public')->delete($mediaPath);
        // }

        if (!empty($errorMessages)) {
            $msg = implode('<br>', $errorMessages);
            if (!empty($successMessages)) {
                return redirect()->route('dashboard')->with('warning', implode('<br>', $successMessages) . '<br>But had errors:<br>' . $msg);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        return redirect()->route('dashboard')->with('success', implode('<br>', $successMessages));
    }

    public function destroy(Story $story)
    {
        // Ensure user owns the story
        if ($story->user_id !== auth()->id()) {
            abort(403);
        }

        $page = $story->facebookPage;
        $errorMessages = [];
        $successMessages = [];

        // Attempt to delete from Facebook
        if ($story->facebook_story_id) {
            $fbResult = $this->facebookApi->deletePost($story->facebook_story_id, $page->access_token);
            if (isset($fbResult['error'])) {
                $errorMessages[] = 'Failed to delete from Facebook: ' . ($fbResult['error']['message'] ?? 'Unknown error');
            } else {
                $successMessages[] = 'Deleted from Facebook.';
            }
        }

        // Attempt to delete from Instagram (Instagram API uses the same Graph endpoint for deletion)
        if ($story->instagram_story_id) {
            $igResult = $this->facebookApi->deletePost($story->instagram_story_id, $page->access_token);
            if (isset($igResult['error'])) {
                $errorMessages[] = 'Failed to delete from Instagram: ' . ($igResult['error']['message'] ?? 'Unknown error');
            } else {
                $successMessages[] = 'Deleted from Instagram.';
            }
        }

        // Delete local media file if it exists
        if ($story->media_path && Storage::disk('public')->exists($story->media_path)) {
            Storage::disk('public')->delete($story->media_path);
        }

        // Delete the record from database
        $story->delete();

        if (!empty($errorMessages)) {
            return redirect()->back()->with('warning', 'Story deleted locally, but API errors occurred: <br>' . implode('<br>', $errorMessages));
        }

        return redirect()->back()->with('success', 'Story completely deleted successfully.');
    }
}
