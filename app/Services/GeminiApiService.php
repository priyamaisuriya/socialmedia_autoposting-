<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiApiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Generate structured captions and hashtags using Gemini API.
     */
    public function generateCaption($prompt, $tone, $language, $density)
    {
        if (!$this->apiKey) {
            return [
                'error' => 'Gemini API key is not configured. Please add GEMINI_API_KEY in your .env file.'
            ];
        }

        // Construct target density context
        $densityNotes = match($density) {
            'none' => 'Do not include any hashtags inside the caption.',
            'low' => 'Include 1 to 5 highly relevant hashtags in the output array.',
            'medium' => 'Include 5 to 10 highly relevant hashtags in the output array.',
            'high' => 'Include 10 to 15 relevant and trending hashtags in the output array.',
            default => 'Include 5 to 8 hashtags.'
        };

        // Construct tone guidelines
        $toneNotes = match($tone) {
            'professional' => 'Tone must be highly professional, structured, reliable, and formal.',
            'casual' => 'Tone must be warm, friendly, easy-going, and highly approachable.',
            'funny' => 'Tone should be lighthearted, amusing, humorous, and full of character.',
            'exciting' => 'Tone should be high-energy, enthusiastic, call-to-action driven, and viral.',
            'witty' => 'Tone should be clever, sharp, smart, and wittily engaging.',
            'informative' => 'Tone should be clear, detailed, educational, and focused on facts.',
            default => 'Tone should be engaging and positive.'
        };

        // Construct language rules
        $langNotes = match($language) {
            'gujarati' => 'The caption MUST be written fully in elegant Gujarati language.',
            'hindi' => 'The caption MUST be written fully in Hindi language.',
            'mixed' => 'The caption should be written in a blended friendly style of English and Gujarati (Gujlish/Gujarati-infused English) representing a modern conversational tone.',
            default => 'The caption MUST be written in English.'
        };

        $systemPrompt = "You are a world-class professional social media copywriter and growth marketer.
Your goal is to write a highly engaging social media caption and generate optimized hashtags based on the user's input topic or description.

Guidelines:
- {$toneNotes}
- {$langNotes}
- {$densityNotes}
- Strategically insert appropriate, modern emojis to make the caption visually outstanding.
- Ensure the caption is structured cleanly with line breaks where appropriate.

CRITICAL: You MUST respond ONLY with a valid JSON object. Do not include markdown code block syntax (like ```json ... ```) or any pre/post text. Return raw JSON text matching this exact schema:
{
  \"caption\": \"The generated caption text here\",
  \"hashtags\": [\"tag1\", \"tag2\", \"tag3\"]
}";

        try {
            $response = Http::withoutVerifying()->timeout(60)->post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemPrompt . "\n\nUser Input Topic/Description: " . $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                $errorData = json_decode($response->body(), true);
                if (isset($errorData['error']['message'])) {
                    return ['error' => $errorData['error']['message']];
                }
                return ['error' => 'Gemini API connection failed (HTTP ' . $response->status() . ').'];
            }

            $data = $response->json();
            $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Clean up code block backticks if Gemini includes them
            $rawText = trim($rawText);
            if (str_starts_with($rawText, '```')) {
                $rawText = preg_replace('/^```(?:json)?|```$/i', '', $rawText);
                $rawText = trim($rawText);
            }

            $decoded = json_decode($rawText, true);
            if (!$decoded || !isset($decoded['caption'])) {
                // Fallback parsing if JSON parsing fails
                return [
                    'caption' => $rawText,
                    'hashtags' => []
                ];
            }

            return $decoded;

        } catch (\Exception $e) {
            return ['error' => 'Error generating caption: ' . $e->getMessage()];
        }
    }

    /**
     * Generate a concise reply to a social media comment using Gemini API.
     */
    public function generateReply($commentText, $tone = 'professional', $language = 'english')
    {
        if (!$this->apiKey) {
            return [
                'error' => 'Gemini API key is not configured.'
            ];
        }

        // Construct tone guidelines
        $toneNotes = match($tone) {
            'professional' => 'Tone must be highly professional, polite, and helpful.',
            'casual' => 'Tone must be warm, friendly, easy-going, and highly approachable.',
            'funny' => 'Tone should be lighthearted, amusing, humorous, and full of character.',
            'witty' => 'Tone should be clever, sharp, smart, and wittily engaging.',
            default => 'Tone should be engaging and positive.'
        };

        // Construct language rules
        $langNotes = match($language) {
            'gujarati' => 'The reply MUST be written fully in Gujarati language.',
            'hindi' => 'The reply MUST be written fully in Hindi language.',
            'english' => 'The reply MUST be written fully in English.',
            'mixed' => 'The reply should be written in a blended friendly style of English and Gujarati (Gujlish).',
            default => 'The reply MUST be written in the exact SAME LANGUAGE that the user used in their comment. Do not translate it, reply natively in their language.'
        };

        $systemPrompt = "You are a world-class professional social media community manager.
Your goal is to write a highly engaging, concise reply to a user's comment on a social media post.

Guidelines:
- {$toneNotes}
- {$langNotes}
- Keep the reply concise, typically 1 to 3 sentences max.
- Do NOT output any hashtags.
- Strategically insert 1 or 2 appropriate emojis if it fits the tone.

CRITICAL: You MUST respond ONLY with a valid JSON object. Do not include markdown code block syntax (like ```json ... ```) or any pre/post text. Return raw JSON text matching this exact schema:
{
  \"reply\": \"The generated reply text here\"
}";

        try {
            $response = Http::withoutVerifying()->timeout(60)->post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemPrompt . "\n\nUser's Comment: " . $commentText]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                $errorData = json_decode($response->body(), true);
                if (isset($errorData['error']['message'])) {
                    return ['error' => $errorData['error']['message']];
                }
                return ['error' => 'Gemini API connection failed (HTTP ' . $response->status() . ').'];
            }

            $data = $response->json();
            $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Clean up code block backticks if Gemini includes them
            $rawText = trim($rawText);
            if (str_starts_with($rawText, '```')) {
                $rawText = preg_replace('/^```(?:json)?|```$/i', '', $rawText);
                $rawText = trim($rawText);
            }

            $decoded = json_decode($rawText, true);
            if (!$decoded || !isset($decoded['reply'])) {
                // Fallback parsing if JSON parsing fails
                return [
                    'reply' => $rawText
                ];
            }

            return $decoded;

        } catch (\Exception $e) {
            return ['error' => 'Error generating reply: ' . $e->getMessage()];
        }
    }
}
