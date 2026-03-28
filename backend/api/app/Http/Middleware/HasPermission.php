<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to verify if the authenticated user has one or more required permissions.
 *
 * Supports multiple permissions separated by commas (e.g., 'create-post,delete-post').
 * Access is granted if the user has at least one of the listed permissions.
 *
 * Usage:
 * - Route:
 *   Route::get('/admin', [AdminController::class, 'index'])->middleware('permission:view-admin,edit-admin');
 *
 * - Controller:
 *   $this->middleware('permission:view-admin,edit-admin');
 *
 * @package App\Http\Middleware
 */
class HasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request  The current HTTP request instance.
     * @param  \Closure  $next  The next middleware or final request handler.
     * @param  string  $permissions  A comma-separated list of required permissions.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        $user = Auth::user();
        $permissionList = explode(',', $permissions);

        $availablePermissions = pluckAllPermissionItems();
        foreach ($permissionList as $permission) {
            if (!in_array($permission, $availablePermissions)) {
                abort(500, 'Permission ' . $permission . '(in api.php) not found in config');
            }
        }

        if ($user->hasPermission('all_access')) {
            return $next($request);
        }

        if ($user) {
            foreach ($permissionList as $permission) {
                if ($user->hasPermission(trim($permission))) {
                    return $next($request); // Grant access if at least one permission matches
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized (You do not have permission to access this resource)',
        ], 401);
    }
}
