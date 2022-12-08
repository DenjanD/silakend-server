<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Models\UserRole;

class ValidatorMiddleware
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
        $userRoles = UserRole::with('role')->where('user_id', Auth::user()->user_id)->get();

        foreach ($userRoles as $role) {
            if($role->role->level == 2){
                return $next($request);
            }
        }
        
        return response()->json([
            'msg' => 'Unauthorized'
        ], 401);
    }
}
