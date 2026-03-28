<?php
        
namespace App\Services;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Attempt to authenticate a user using the provided credentials and return a response with the JWT token.
     *
     * @param array $credentials The user credentials for authentication.
     * @return array The response containing the JWT token and its type.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If authentication fails.
     */

    public function login(array $credentials): array
    {
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Unauthorized',
            ];
        }

        return [
            'success' => true,
            'data' => $this->respondWithToken($token)
        ];
    }

    /**
     * Logout the authenticated user and invalidate its JWT token.
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Refresh the JWT token for the authenticated user.
     *
     * @return array The response containing the new JWT token and its type.
     * @throws \Tymon\JWTAuth\Exceptions\JWTException If the token cannot be refreshed.
     */

     public function refresh(): array
     {
         try {
             return $this->respondWithToken(JWTAuth::parseToken()->refresh());
         } catch (JWTException $e) {
             return [
                 'success' => false,
                 'message' => 'Unauthorized',
             ];
         }
     }

    /**
     * Check if the authenticated user has the given permissions.
     *
     * @param array $permissions The permissions to check.
     * @return array An array of permission checks, with keys of the permission name
     * and values of a boolean indicating whether the user has the permission.
     */
    public function checkPermissions(array $permissions)
    {
        // cek jika user memiliki permission all_access
        if (auth()->user()->hasPermission('all_access')) {
            return collect($permissions)->map(function ($permission) {
                return [
                    'permission' => $permission,
                    'access' => true,
                ];
            });
        }

        return collect($permissions)->map(function ($permission) {
            return [
                'permission' => $permission,
                'access' => auth()->user()->hasPermission($permission),
            ];
        })->all();
    }


    /**
     * Format the given JWT token into a response array.
     *
     * @param  string  $token
     * @return array
     */
    protected function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL()
        ];
    }
}