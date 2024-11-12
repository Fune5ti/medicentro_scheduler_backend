<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       if(!Auth::check()){
           $errorResponse = [
               "success" => false,
               "message" => "User not authenticated",
           ];
           abort(response()->json($errorResponse, Response::HTTP_UNAUTHORIZED));
       }
       return $next($request);
    }
}
