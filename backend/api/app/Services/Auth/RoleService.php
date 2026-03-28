<?php
        
namespace App\Services\Auth;

use App\Repositories\Auth\RoleRepository;

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
    public function getAllRoles() : array
    {
        $allRoles = $this->repository->all();
        if ($allRoles['error']) {
            return $allRoles;
        }
        $allRoles = $allRoles['result'];

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

        return [
            'error' => null,
            'code' => 200,
            'result' => $allRoles
        ];
    }

    // getGroupRoles
    public function getGroupRoles()
    {
        try {
            return [
                'error' => null,
                'code' => 200,
                'result' => config('app.role_group')
            ];
        } catch (\Throwable $th) {
            return [
                'error' => 'Group roles not found in config',
                'code' => 404,
                'result' => null
            ];
        }
    }

    /**
     * Checks if a role with the given ID has any users associated with it.
     *
     * @param string $id The ID of the role to check.
     * @return bool True if the role has any users associated with it, false otherwise.
     */
    public function hasUser($id)
    {
        $hasUser = $this->repository->hasUser($id);
        if ($hasUser['error']) {
            return $hasUser;
        }
        $hasUser = $hasUser['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $hasUser
        ];
    }

    // getAllRoleForSelect
    public function getAllRoleForSelect($search = null)
    {
        $allRole = $this->repository->getAllRoleForSelect($search);
        if ($allRole['error']) {
            return $allRole;
        }
        $allRole = $allRole['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $allRole
        ];
    }

    /**
     * Checks if the given ID is the ID of the admin role.
     *
     * @param string $id The ID to check.
     * @return bool True if the given ID is the ID of the admin role, false otherwise.
     */
    public function IsAdminID($id)
    {
        $adminRoleId = $this->repository->getAdminRoleID();
        if ($adminRoleId['error']) {
            return $adminRoleId;
        }
        $adminRoleId = $adminRoleId['result'];
        return [
            'error' => null,
            'code' => 200,
            'result' => $adminRoleId === $id
        ];
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
        $role = $this->repository->find($id);
        if ($role['error']) {
            return $role;
        }
        $role = $role['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $role
        ];
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
        $created = $this->repository->create($data);
        if ($created['error']) {
            return $created;
        }
        $created = $created['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $created
        ];
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
        $updated = $this->repository->update($id, $data);
        if ($updated['error']) {
            return $updated;
        }
        $updated = $updated['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $updated
        ];
    }

    /**
     * Delete a role by its ID.
     *
     * @param string $id The ID of the role to delete.
     * @return bool True if the role was deleted successfully, false otherwise.
     */
    public function deleteRole(string $id)
    {
        $deleted = $this->repository->delete($id);
        if ($deleted['error']) {
            return $deleted;
        }
        $deleted = $deleted['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $deleted
        ];
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

        return [
            'error' => null,
            'code' => 200,
            'result' => $permissions->toArray()
        ];
    }
}