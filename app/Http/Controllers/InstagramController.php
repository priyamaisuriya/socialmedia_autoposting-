<?php

namespace App\Http\Controllers;

use App\Models\FacebookPage;
use Illuminate\Http\Request;

class InstagramController extends Controller
{
    public function index()
    {
        // Get all pages of the user that have a linked Instagram account on Meta
        $pages = auth()->user()->facebookPages()->whereNotNull('instagram_account_id')->get();
        return view('instagram.index', compact('pages'));
    }

    public function toggleConnection($id)
    {
        $page = auth()->user()->facebookPages()->findOrFail($id);

        if (!$page->instagram_account_id) {
            return redirect()->back()->with('error', 'This page does not have a linked Instagram account.');
        }

        $page->is_instagram_connected = !$page->is_instagram_connected;
        $page->save();

        $status = $page->is_instagram_connected ? 'connected' : 'disconnected';
        return redirect()->back()->with('success', "Instagram account @{$page->instagram_username} has been {$status} successfully!");
    }
}
