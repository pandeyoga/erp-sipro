<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreRoleRequest;
use App\Services\Auth\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct(protected RoleService $service) {}
    
    public function index()
    {
        $allRole = $this->service->getAllRoles();

        if ($allRole['error']) {
            $errorMessage = (string) $allRole['error'];
            $errorCode = (int) $allRole['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $allRole = $allRole['result'];

        return $this->successResponse($allRole);
    }

    public function getGroupRoles()
    {
        $roleGroups = $this->service->getGroupRoles();
        if ($roleGroups['error']) {
            $errorMessage = (string) $roleGroups['error'];
            $errorCode = (int) $roleGroups['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $roleGroups = $roleGroups['result'];

        return $this->successResponse($roleGroups);
    }
    
    public function getAllRoleForSelect(Request $request)
    {
        $valiadtor = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255'
        ]);

        if ($valiadtor->fails()) {
            return $this->errorResponse(
                "Validation error", 
                $valiadtor->errors(), 
                400
            );
        }

        $search = $request->input('search') ?? null;

        $allRole = $this->service->getAllRoleForSelect($search);
        if ($allRole['error']) {
            $errorMessage = (string) $allRole['error'];
            $errorCode = (int) $allRole['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $allRole = $allRole['result'];

        return $this->successResponse($allRole);
    }
    
    public function store(StoreRoleRequest $request)
    {
        $requestData = $request->validated();
        
        if (!$this->service->permissionExists($request['permissions'] ?? [])) {
            return $this->errorResponse(
                "Validation error", 
                ["permissions" => "Invalid permissions"], 
                400
            );
        }

        $createRole = $this->service->createRole($requestData);
        if ($createRole['error']) {
            $errorMessage = (string) $createRole['error'];
            $errorCode = (int) $createRole['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $createRole = $createRole['result'];

        return $this->successResponse(
            $createRole,
            'Role created successfully', 201
        );
    }
    
    public function show(string $id)
    {
        if (!Str::isUuid($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        $isAdminId = $this->service->IsAdminID($id);
        if ($isAdminId['error']) {
            return $this->errorResponse("Invalid role id", null, 400);
        }
        $isAdminId = $isAdminId['result'];

        if ($isAdminId) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        $role = $this->service->getRole($id);
        if ($role['error']) {
            $errorMessage = (string) $role['error'];
            $errorCode = (int) $role['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $role = $role['result'];
        
        return $this->successResponse($role);
    }
    
    public function update(StoreRoleRequest $request, string $id)
    {
        if (!Str::isUuid($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        $isAdminId = $this->service->IsAdminID($id);
        if ($isAdminId['error']) {
            return $this->errorResponse("Invalid role id", null, 400);
        }
        $isAdminId = $isAdminId['result'];

        if ($isAdminId) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        $requestData = $request->validated();
        if ($request['permissions'] !== null) {
            if (!$this->service->permissionExists($request['permissions'])) {
                return $this->errorResponse(
                    "Validation error", 
                    ["permissions" => "Invalid permissions"], 
                    400
                );
            }
        }

        $updateRole = $this->service->updateRole($id, $requestData);
        if ($updateRole['error']) {
            $errorMessage = (string) $updateRole['error'];
            $errorCode = (int) $updateRole['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $updateRole = $updateRole['result'];

        return $this->successResponse($updateRole, 'Role updated successfully');
    }
    
    public function destroy(string $id)
    {
        if (!Str::isUuid($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        $isAdminId = $this->service->IsAdminID($id);
        if ($isAdminId['error']) {
            return $this->errorResponse("Invalid role id", null, 400);
        }
        $isAdminId = $isAdminId['result'];

        if ($isAdminId) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        $roleHasUsers = $this->service->hasUser($id);
        if ($roleHasUsers['error']) {
            $errorMessage = (string) $roleHasUsers['error'];
            $errorCode = (int) $roleHasUsers['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $roleHasUsers = $roleHasUsers['result'];

        if ($roleHasUsers) {
            return $this->errorResponse("Cannot delete role with associated users", null, 400);
        }

        $deleteRole = $this->service->deleteRole($id);
        if ($deleteRole['error']) {
            $errorMessage = (string) $deleteRole['error'];
            $errorCode = (int) $deleteRole['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        return $this->successResponse(null, 'Role deleted successfully');
    }

    /**
     * Return all permission items in the system.
     *
     * @return \Illuminate\Http\JsonResponse A response containing the array of permission items.
     */
    public function getAllPermissionItems()
    {
        $allPermissionItems = $this->service->getAllPermissionItems();
        if ($allPermissionItems['error']) {
            $errorMessage = (string) $allPermissionItems['error'];
            $errorCode = (int) $allPermissionItems['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $allPermissionItems = $allPermissionItems['result'];

        return $this->successResponse($allPermissionItems);
    }
}
