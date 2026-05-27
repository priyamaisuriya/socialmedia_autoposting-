<?php

namespace App\Http\Controllers;

use App\Services\GeminiApiService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    protected $geminiApi;

    public function __construct(GeminiApiService $geminiApi)
    {
        $this->geminiApi = $geminiApi;
    }

    /**
     * Generate captions and hashtags using AI.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'tone' => 'required|string|in:professional,casual,funny,exciting,witty,informative',
            'language' => 'required|string|in:english,gujarati,hindi,mixed',
            'density' => 'required|string|in:none,low,medium,high',
        ]);

        $result = $this->geminiApi->generateCaption(
            $request->prompt,
            $request->tone,
            $request->language,
            $request->density
        );

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json($result);
    }

    /**
     * Generate AI reply for a comment.
     */
    public function generateReply(Request $request)
    {
        $request->validate([
            'comment_text' => 'required|string|max:2000',
            'tone' => 'nullable|string|in:professional,casual,funny,witty',
            'language' => 'nullable|string|in:english,gujarati,hindi,mixed,auto',
        ]);

        $result = $this->geminiApi->generateReply(
            $request->comment_text,
            $request->tone ?? 'professional',
            $request->language ?? 'english'
        );

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json($result);
    }
}
