<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckSessionExpiration
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Session::has('last_activity')) {
            $lastActivity = Session::get('last_activity');
            $sessionLifetime = config('session.lifetime') * 60; // Convert minutes to seconds
            
            if (time() - $lastActivity > $sessionLifetime) {
                Auth::logout();
                Session::flush();
                
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired'], 401);
                }
                
                return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
            }
        }
        
        Session::put('last_activity', time());
        return $next($request);
    }
} 