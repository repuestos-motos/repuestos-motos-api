<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class test extends Controller
{

    public function TestAction(Request $request) {
        return response('Server is working fine');
    }

    public static function TestDBAction(Request $request) {
        $user = Client::all();
        return response()->json($user);
    }
}
