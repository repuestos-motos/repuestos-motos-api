<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\ResponseModels\User as UserResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

class AuthenticationController extends Controller
{
    /**
     * Clients login
     */
    public function Login(Request $request) {
        try {
            $userName = $request->input('userName');
            $password = $request->input('password');
            $user = User::where('NOMUSUARIO', $userName)
                ->where('CLAVE', $password)
                ->where('HABILITAWEB', 'S')
                ->first();
            
            if ($user) {
                $user = new UserResponse(
                    $user->IDCLIENTE,
                    $user->APENOM,
                    $user->TIPODOC,
                    $user->NRODOC,
                    $user->DOMICILIO,
                    $user->TELEFONO,
                    $user->CORREO,
                    $user->IIBB
                );
            } else {
                return response('', 403);
            }
            return response()->json($user);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response('', 500);
        }
        
    }

    /**
     * Seller login
     */
    public function SellerLogin(Request $request) {
        try {
            $userName = $request->input('userName');
            $password = $request->input('password');
            return response()->json(null);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response('', 500);
        }
        
    }

    /**
     * Check login
     */
    public function CheckLogin(Request $request) {
        try {
            return response()->json(null);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response('', 500);
        }
        
    }
}
