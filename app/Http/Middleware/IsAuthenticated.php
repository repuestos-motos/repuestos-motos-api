<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

class IsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::error('TOKEN: ' . $request->header('accessToken')
            . ' - Headers: ' . json_encode($request->headers->all())
        );

        if (JWToken::VerifyToken($request->header('accessToken'))) {
            $response = $next($request);
            return $response->header('accessToken', JWToken::CreateToken())->header('Access-Control-Expose-Headers', 'accessToken');
        }
        return response('', 401);
    }
}
