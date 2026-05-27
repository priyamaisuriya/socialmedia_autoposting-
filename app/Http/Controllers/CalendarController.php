<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Story;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index()
    {
        $userId = auth()->id();
        
        $stats = [
            'total' => Post::where('user_id', $userId)->count(),
            'scheduled' => Post::where('user_id', $userId)->where('status', 'scheduled')->count(),
            'failed' => Post::where('user_id', $userId)->where('status', 'failed')->count(),
        ];

        return view('calendar.index', compact('stats'));
    }

    /**
     * Fetch events for the calendar via AJAX (used by FullCalendar).
     */
    public function events(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $userId = auth()->id();

        $events = [];

        // 1. Fetch standard posts
        $posts = Post::where('user_id', $userId)
            ->where(function($q) use ($start, $end) {
                // If it has a scheduled_at, use that
                $q->whereBetween('scheduled_at', [$start, $end])
                  // Otherwise use created_at
                  ->orWhere(function($query) use ($start, $end) {
                      $query->whereNull('scheduled_at')
                            ->whereBetween('created_at', [$start, $end]);
                  });
            })
            ->with('facebookPage')
            ->get();

        foreach ($posts as $post) {
            $date = $post->scheduled_at ? $post->scheduled_at->toIso8601String() : $post->created_at->toIso8601String();
            
            // Color coding based on status
            $color = '#3b82f6'; // Default Blue
            if ($post->status === 'success') $color = '#10b981'; // Green
            elseif ($post->status === 'failed') $color = '#ef4444'; // Red
            elseif ($post->status === 'scheduled') $color = '#f59e0b'; // Yellow/Orange
            
            $events[] = [
                'id' => 'post_' . $post->id,
                'title' => ($post->facebookPage ? $post->facebookPage->name . ': ' : '') . mb_substr($post->message, 0, 30) . '...',
                'start' => $date,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('posts.show', $post->id),
                'extendedProps' => [
                    'type' => 'post',
                    'status' => $post->status
                ]
            ];
        }

        // 2. Fetch stories (optional, if we want them on calendar)
        $stories = Story::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->with('facebookPage')
            ->get();
            
        foreach ($stories as $story) {
            $date = $story->created_at->toIso8601String();
            $color = '#ec4899'; // Pink for stories
            
            if ($story->status === 'failed' || $story->instagram_status === 'failed') $color = '#ef4444';
            elseif ($story->status === 'pending' || $story->instagram_status === 'pending') $color = '#f59e0b';

            $events[] = [
                'id' => 'story_' . $story->id,
                'title' => '📸 Story: ' . ($story->facebookPage->name ?? ''),
                'start' => $date,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('stories.index'),
                'extendedProps' => [
                    'type' => 'story',
                    'status' => $story->status
                ]
            ];
        }

        return response()->json($events);
    }
}
