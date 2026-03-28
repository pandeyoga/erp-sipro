<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CheckPermissionRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

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

    public function logout()
    {
        $this->authService->logout();
        return $this->successResponse(null, 'Successfully logged out');
    }

    public function refresh()
    {
        $result = $this->authService->refresh();
        if (!$result['success']) {
            return $this->errorResponse($result['message'], null, 401);
        }
        return $this->successResponse($result['data'], 'Successfully refreshed token');
    }

    public function checkPermissions(CheckPermissionRequest $request)
    {
        $request = $request->validated();

        return $this->successResponse($this->authService->checkPermissions($request['permissions']));
    }
}
