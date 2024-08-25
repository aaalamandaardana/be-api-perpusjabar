<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentUser = auth()->user();

        $roleOwner = Role::where('name', 'owner')->first();

        if ($currentUser->role_id === $roleOwner->id) {
            return $next($request);
        }

        return response()->json([
            "message"   =>  'Mohon Maaf, Fitur yang Anda Maksud Hanya Dikhususkan untuk Role Owner, dan Role Anda Bukanlah Owner'
        ], 401);
    }
}
