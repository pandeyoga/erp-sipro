<?php
        
namespace App\Services;

use App\Models\RolePermission;
use App\Repositories\RoleRepository;
use App\Repositories\RoleRepositoryInterface;

class RoleService
{
    public function __construct(protected RoleRepository $repository) {}

    /**
     * Get all roles with their permissions.
     *
     * @return \Illuminate\Support\Collection<int, array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     permissions: array{
     *         name: string,
     *         code: string,
     *     }[],
     * }>
     */
    public function getAllRoles()
    {
        $allRoles = $this->repository->all();
        $allRoles = $allRoles->map(function ($role) {
            $data = $role->toArray();
            $data['permissions'] = $role->permissions->map(function ($p) {
                $p = $p->toArray();
                $permission['name'] = getPermissionTitle($p['permission_code']);
                $permission['code'] = $p['permission_code'];
                return $permission;
            });
            return $data;
        });
        return $allRoles;
    }

    // getAllRoleForSelect
    public function getAllRoleForSelect($search = null)
    {
        return $this->repository->getAllRoleForSelect($search);
    }

    /**
     * Checks if the given ID is the ID of the admin role.
     *
     * @param string $id The ID to check.
     * @return bool True if the given ID is the ID of the admin role, false otherwise.
     */
    public function IsAdminID($id)
    {
        return $this->repository->getAdminRoleID() === $id;
    }

    /**
     * Get a role by ID.
     *
     * @param string $id The ID of the role to retrieve.
     * @return array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     permissions: array{
     *         name: string,
     *         code: string,
     *     }[],
     * }
     */
    public function getRole(string $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Creates a new role with the given data.
     *
     * @param array{
     *     name: string,
     *     description: string,
     *     permissions: array<string>,
     * } $data The data of the role to create.
     *
     * @return array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     permissions: array{
     *         name: string,
     *         code: string,
     *     }[],
     * }
     */
    public function createRole(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Updates a role with the given data.
     *
     * @param string $id The ID of the role to update.
     * @param array{
     *     name: string,
     *     description: string,
     *     permissions: array<string>,
     * } $data The data of the role to update.
     *
     * @return array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     permissions: array{
     *         name: string,
     *         code: string,
     *     }[],
     * }
     */
    public function updateRole(string $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a role by its ID.
     *
     * @param string $id The ID of the role to delete.
     * @return bool True if the role was deleted successfully, false otherwise.
     */
    public function deleteRole(string $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Checks if all the given permissions exist in the system.
     *
     * @param array $permissions The permissions to check for existence.
     * @return bool True if all permissions exist, false otherwise.
     */

    public function permissionExists(array $permissions)
    {
        $avaliablePermissions = pluckAllPermissionItems();
        foreach ($permissions as $permission) {
            if (!in_array($permission, $avaliablePermissions)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return an array of all permission items from the permissions configuration.
     * The permissions will be formatted as "feature.permission" and will be
     * returned as a flat array.
     *
     * @return array The array of permission items.
     */
    public function getAllPermissionItems() : array
    {
        $permissions = collect(config('permissions'))->map(function ($module) {
            return collect($module['features'])->map(function ($feature, $featureKey) {
                    return collect($feature)->map(function ($permission) use ($featureKey) {
                        return $permission;
                    })->values()->all();
                });
        });

        return $permissions->toArray();
    }
}