<?php
        
namespace App\Repositories\Auth;

use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleRepository
{
    public function all()
    {
        $adminRoleId = $this->getAdminRoleID();
        if ($adminRoleId['error']) {
            return $adminRoleId;
        }

        try {
            $adminRoleId = $adminRoleId['result'];
            $roles = Role::with('permissions:id,role_id,permission_code')
                ->select('id', 'name', 'description', 'group')
                ->where('id', '!=', $adminRoleId)
                ->get();
        } catch (\Throwable $th) {
            return [
                'error' => 'Admin role not found',
                'code' => 404,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $roles
        ];
    }

    /**
     * Retrieve all roles for selection excluding the admin role.
     *
     * This function fetches roles with their IDs and names, optionally filtering by search terms.
     *
     * @param string|null $search An optional search term to filter roles by name.
     * @return \Illuminate\Support\Collection A collection of roles, each containing 'id' and 'name'.
     */

    public function getAllRoleForSelect($search = null)
    {
        $adminRoleId = $this->getAdminRoleID();
        if ($adminRoleId['error']) {
            return $adminRoleId;
        }
        $adminRoleId = $adminRoleId['result'];

        try {
            $roles = Role::select('id', 'name', 'group')
                ->where('id', '!=', $adminRoleId)
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'ilike', "%{$search}%");
                })
                ->limit(20)
                ->orderBy('group', 'asc')
                ->get();
        } catch (\Throwable $th) {
            return [
                'error' => 'Admin role not found',
                'code' => 404,
                'result' => null
            ];
        }
        
        return [
            'error' => null,
            'code' => 200,
            'result' => $roles
        ];
    }

    
    /**
     * Check if a role has a user with the given ID.
     *
     * This function determines whether there is any user associated with a role
     * that has the specified user ID.
     *
     * @param mixed $id The ID of the user to check for association with the role.
     * @return bool True if the user is associated with the role, false otherwise.
     */

    public function hasUser($id)
    {
        try {
            $hasUser = User::where('role_id', $id)->exists();
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $hasUser
        ];
    }

    public function getAdminRoleID()
    {
        $adminPermission = RolePermission::where('permission_code', 'all_access')->first();
        if (!$adminPermission) {
            return [
                'error' => 'Admin role not found',
                'code' => 404,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $adminPermission->role_id
        ];
    }

    public function find(string $id)
    {
        $role = Role::with('permissions:id,role_id,permission_code')
            ->select('id', 'name', 'description')
            ->where('id', $id)->first();

        if (!$role) {
            return [
                'error' => 'Role not found',
                'code' => 404,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $role
        ];
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'group' => $data['group'] ?? null,
            ]);

            if (!empty($data['permissions'])) {
                $this->syncPermissions($role, $data['permissions']);
            }

            $created = $role->load('permissions');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $created
        ];
    }

    public function update(string $id, array $data)
    {
        try {
            DB::beginTransaction();

            $role = Role::findOrFail($id);
            $role->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'group' => $data['group'] ?? null,
            ]);
            if (!empty($data['permissions'])) {
                $this->syncPermissions($role, $data['permissions']);
            }
            $updated = $role->load('permissions');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $updated
        ];
    }

    public function delete(string $id)
    {
        try {
            DB::beginTransaction();

            $role = Role::where('id', $id)->first();
            if (!$role) {
                DB::rollBack();
                return [
                    'error' => 'Role not found',
                    'code' => 404,
                    'result' => null
                ];
            }

            $role->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }

    protected function syncPermissions(Role $role, array $permissions)
    {
        RolePermission::where('role_id', $role->id)->delete();

        $records = collect($permissions)
            ->unique()
            ->map(function ($code) use ($role) {
            return [
                'id' => Str::uuid(),
                'role_id' => $role->id,
                'permission_code' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        RolePermission::insert($records->toArray());
    }
}