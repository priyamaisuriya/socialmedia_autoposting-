<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $token;
    protected string $phoneId;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.url', 'https://graph.facebook.com/v17.0');
        $this->phoneId = config('services.whatsapp.phone_id');
        $this->token = config('services.whatsapp.token');
    }

    /**
     * Send a text message to a given phone number.
     *
     * @param string $to Phone number with country code (e.g., 15551234567)
     * @param string $message
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (empty($this->phoneId) || empty($this->token)) {
            Log::error('WhatsApp API credentials are not set.');
            return false;
        }

        $endpoint = "{$this->apiUrl}/{$this->phoneId}/messages";

        $response = Http::withToken($this->token)->post($endpoint, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message,
            ],
        ]);

        if ($response->successful()) {
            return true;
        }

        Log::error('WhatsApp API Error: ' . $response->body());
        return false;
    }
}
