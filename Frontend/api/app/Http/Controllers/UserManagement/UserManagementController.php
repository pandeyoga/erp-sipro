<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Services\UserManagementService;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct(protected UserManagementService $service) {}

    public function index(GetUserRequest $request)
    {
        $request = $request->validated();
        $req = [
            'search' => $request['search'] ?? null,
            'page' => $request['page'] ?? 1,
            'per_page' => $request['per_page'] ?? 20,
            'role_id' => $request['role_id'] ?? null,
            'is_active' => $request['is_active'] ?? null,
        ];

        $data = $this->service->getAllUsers($req);

        return $this->paginatedResponse($data);
    }

    public function create(StoreUserRequest $request)
    {
        $requestData = $request->validated();
        
        $user = $this->service->createUser($requestData);

        return $this->successResponse($user);
    }
}
