<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Select month and year, default to current month
        $month = intval($request->input('month', date('n')));
        $year = intval($request->input('year', date('Y')));
        $pageIdFilter = $request->input('facebook_page_id');
        
        // Create Carbon instance for current selected month
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $startOfWeek = $startOfMonth->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
        
        // Query user posts for the selected month
        $query = Post::where('user_id', $user->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($pageIdFilter) {
            $query->where('facebook_page_id', $pageIdFilter);
        }

        $posts = $query->with(['facebookPage', 'media'])->get();

        // Group posts by day and compute calendar-specific statistics
        $postsByDay = [];
        $successCount = 0;
        $pendingCount = 0;
        $failedCount = 0;

        foreach ($posts as $post) {
            $day = intval($post->created_at->format('j'));
            $postsByDay[$day][] = $post;

            if ($post->status === 'success') {
                $successCount++;
            } elseif ($post->status === 'pending') {
                $pendingCount++;
            } else {
                $failedCount++;
            }
        }

        $monthName = $startOfMonth->format('F Y');
        
        // Navigate months
        $prevMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();

        $connectedPages = \App\Models\FacebookPage::where('user_id', $user->id)->get();
        
        $plannerStats = [
            'total' => $posts->count(),
            'success' => $successCount,
            'pending' => $pendingCount,
            'failed' => $failedCount,
            'success_rate' => $posts->count() > 0 ? round(($successCount / $posts->count()) * 100) : 0,
        ];
        
        return view('analytics.index', compact(
            'postsByDay',
            'daysInMonth',
            'startOfWeek',
            'monthName',
            'year',
            'month',
            'prevMonth',
            'nextMonth',
            'connectedPages',
            'pageIdFilter',
            'plannerStats'
        ));
    }
}
