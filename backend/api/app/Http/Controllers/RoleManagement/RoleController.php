<?php

namespace App\Http\Controllers\RoleManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct(protected RoleService $service) {}
    
    /**
     * Retrieve all roles.
     *
     * @return \Illuminate\Http\JsonResponse A successful response containing the list of all roles.
     */
    public function index()
    {
        return $this->successResponse($this->service->getAllRoles());
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

        return $this->successResponse($this->service->getAllRoleForSelect($search));
    }
    
    /**
     * Create a new role.
     *
     * @param \App\Http\Requests\StoreRoleRequest $request
     * @return \Illuminate\Http\JsonResponse A successful response containing the created role.
     */
    public function store(StoreRoleRequest $request)
    {
        $requestData = $request->validated();
        if (!$this->service->permissionExists($request['permissions'])) {
            return $this->errorResponse(
                "Validation error", 
                ["permissions" => "Invalid permissions"], 
                400
            );
        }
        return $this->successResponse(
            $this->service->createRole($requestData),
            'Role created successfully', 201
        );
    }
    
    /**
     * Display the specified role by ID.
     *
     * @param string $id The UUID of the role to retrieve.
     * @return \Illuminate\Http\JsonResponse A response containing the role data or an error message if the ID is invalid.
     */
    public function show(string $id)
    {
        if (!Str::isUuid($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        if ($this->service->IsAdminID($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }
        return $this->successResponse($this->service->getRole($id));
    }
    
    /**
     * Update the specified role.
     *
     * @param \App\Http\Requests\StoreRoleRequest $request
     * @param string $id The UUID of the role to update.
     * @return \Illuminate\Http\JsonResponse A response containing the updated role data or an error message if the ID is invalid.
     */
    public function update(StoreRoleRequest $request, string $id)
    {
        if (!Str::isUuid($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        if ($this->service->IsAdminID($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        $requestData = $request->validated();
        if (!$this->service->permissionExists($request['permissions'])) {
            return $this->errorResponse(
                "Validation error", 
                ["permissions" => "Invalid permissions"], 
                400
            );
        }

        return $this->successResponse($this->service->updateRole($id, $requestData), 'Role updated successfully');
    }
    
    /**
     * Remove the specified role by ID.
     *
     * @param string $id The UUID of the role to delete.
     * @return \Illuminate\Http\JsonResponse A response containing the result of the deletion or an error message if the ID is invalid.
     */
    public function destroy(string $id)
    {
        if (!Str::isUuid($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        if ($this->service->IsAdminID($id)) {
            return $this->errorResponse("Invalid role id", null, 400);
        }

        $this->service->deleteRole($id);
        return $this->successResponse(null, 'Role deleted successfully');
    }

    /**
     * Return all permission items in the system.
     *
     * @return \Illuminate\Http\JsonResponse A response containing the array of permission items.
     */
    public function getAllPermissionItems()
    {
        return $this->successResponse($this->service->getAllPermissionItems());
    }
}
