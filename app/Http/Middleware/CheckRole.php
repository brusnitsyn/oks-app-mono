<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roles = array_slice(func_get_args(), 2); // [admin, doctor, operator, etc...]

        foreach ($roles as $role) {
            try {
                Role::whereSlug($role)->firstOrFail(); // make sure we got a "real" role

                if (Auth::user()->hasRole($role)) {
                    return $next($request);
                }
            } catch (ModelNotFoundException $exception) {
                Log::error('Could not find role ' . $role);
            }
        }

        if ($request->url() === redirect()->back()->getTargetUrl()) {
            return redirect('/');
        }

        return redirect()->back();
    }
}
