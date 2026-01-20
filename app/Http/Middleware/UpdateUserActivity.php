<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Cache;

class UpdateUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Update last_seen_at every minute to reduce DB writes
            if(Cache::add('user-is-online-' . $user->id, true, 60)){
                $user->last_seen_at = now();
                $user->save();
            }
        }
        return $next($request);
    }
}
