<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckPermissionRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Login to the application.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $check = $this->authService->login($credentials);
        if ($check['success']) {
            return $this->successResponse($check['data'], 'Successfully logged in');
        } else {
            return $this->errorResponse($check['message'], null, 401);
        }
    }

    /**
     * Logout the authenticated user and invalidate its JWT token.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->authService->logout();
        return $this->successResponse(null, 'Successfully logged out');
    }

    /**
     * Refresh the authenticated user's JWT token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->successResponse($this->authService->refresh());
    }

    /**
     * Check if the authenticated user has the given permissions.
     * 
     * @param CheckPermissionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPermissions(CheckPermissionRequest $request)
    {
        $request = $request->validated();

        return $this->successResponse($this->authService->checkPermissions($request['permissions']));
    }
}
