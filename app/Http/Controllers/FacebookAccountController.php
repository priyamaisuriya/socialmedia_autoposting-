<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacebookAccountController extends Controller
{

    public function redirect()
    {
        // Facebook login redirect here
        return "Redirect to Facebook Login";
    }

    public function callback(Request $request)
    {
        // Facebook response handle here
        return "Facebook data received";
    }

}