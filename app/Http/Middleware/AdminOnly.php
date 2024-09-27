<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and is an admin
        if (Auth::check() && Auth::user()->category === 'admin') {
            return $next($request);
        }
    
        // If the user is not an admin, redirect to the specific blade with an error message
        return redirect()->route('don_not_have_permission') // Assuming you have a named route
                         ->with('error', __('lang.don_not_have_permission_message'));
    }
    
}
