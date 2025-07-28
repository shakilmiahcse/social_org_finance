<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ShareNotifications
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $unreadCount = $user->unreadNotifications->count();

            view()->share([
                'auth' => [
                    'user' => $user,
                    'unread_notifications_count' => $unreadCount,
                ],
            ]);
        }

        return $next($request);
    }
}
