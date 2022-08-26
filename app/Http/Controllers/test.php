<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class test extends Controller
{

    public function TestAction(Request $request) {
        return response('Server is working fine');
    }

    public static function TestDBAction(Request $request) {
        $user = [];
        
        // $user = Product::GetProductsListWithPrices(1);
        return response()->json($user);
    }
}
