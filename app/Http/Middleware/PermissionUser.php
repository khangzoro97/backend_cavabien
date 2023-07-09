<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionUser
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
        $role = $request->user()->startup_id ? User::ROLE_START_UP : User::ROLE_INVESTOR;
        $routeName = request()->route()->getName();
        $arrayPermission = config("permission.user.$routeName");

        if (empty($arrayPermission) || in_array($role, $arrayPermission)) {
            return $next($request);
        }

        return response()->json(["message" => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]], Response::HTTP_UNAUTHORIZED);
    }
}
