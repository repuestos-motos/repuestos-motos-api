<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authentication
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
        $response = $next($request);
        if ($response->getStatusCode() === 200) {
            $token = JWToken::CreateToken();
            return $response->header("Authorization", $token)->header('Access-Control-Expose-Headers', 'Authorization');
        }
        return $response;
    }
}
