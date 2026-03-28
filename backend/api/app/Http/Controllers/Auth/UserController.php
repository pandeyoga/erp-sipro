<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GetUserRequest;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Services\Auth\UserService;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct(protected UserService $service) {}

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
        if ($data['error']) {
            $errorMessage = (string) $data['error'];
            $errorCode = (int) $data['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $data = $data['result'];

        return $this->paginatedResponse($data);
    }

    public function create(StoreUserRequest $request)
    {
        $requestData = $request->validated();
        
        $user = $this->service->createUser($requestData);
        if ($user['error']) {
            $errorMessage = (string) $user['error'];
            $errorCode = (int) $user['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $user = $user['result'];

        return $this->successResponse($user, 'User created successfully', 201);
    }

    public function show($id)
    {
        $user = $this->service->getUserById($id);
        if ($user['error']) {
            $errorMessage = (string) $user['error'];
            $errorCode = (int) $user['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $user = $user['result'];

        return $this->successResponse($user, 'User retrieved successfully');
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $requestData = $request->validated();

        $user = $this->service->updateUser($id, $requestData);
        if ($user['error']) {
            $errorMessage = (string) $user['error'];
            $errorCode = (int) $user['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $user = $user['result'];

        return $this->successResponse($user);
    }

    public function destroy($id)
    {
        $delete = $this->service->deleteUser($id);
        if ($delete['error']) {
            $errorMessage = (string) $delete['error'];
            $errorCode = (int) $delete['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }

        return $this->successResponse(null, 'User deleted successfully');
    }

    /**
     * Toggle the active status of a user by ID.
     *
     * @param string $id The UUID of the user to toggle.
     * @return \Illuminate\Http\JsonResponse A response containing the new status of the user or an error message if the ID is invalid.
     */
    public function toggleStatus($id)
    {
        $newStatus = $this->service->toggleUserStatus($id);
        if ($newStatus['error']) {
            $errorMessage = (string) $newStatus['error'];
            $errorCode = (int) $newStatus['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $newStatus = $newStatus['result'];

        return $this->successResponse($newStatus, 'User status updated successfully');
    }
}
