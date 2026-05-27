<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;

class WhatsAppController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Show the form for creating a new WhatsApp message.
     */
    public function create()
    {
        return view('whatsapp.create');
    }

    /**
     * Send the WhatsApp message.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        // Clean the phone number (remove spaces, +, -, etc.)
        $phoneNumber = preg_replace('/[^0-9]/', '', $validated['phone_number']);

        $success = $this->whatsappService->sendMessage($phoneNumber, $validated['message']);

        if ($success) {
            return redirect()->back()->with('success', 'WhatsApp message sent successfully!');
        }

        return redirect()->back()->with('error', 'Failed to send WhatsApp message. Please check logs for details.');
    }
}
