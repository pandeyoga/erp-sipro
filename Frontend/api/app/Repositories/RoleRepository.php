<?php
        
namespace App\Repositories;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleRepository
{
    public function all()
    {
        $adminRoleId = $this->getAdminRoleID();
        return Role::with('permissions:id,role_id,permission_code')
            ->select('id', 'name', 'description')
            ->where('id', '!=', $adminRoleId)
            ->get();
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
        return Role::select('id', 'name')
            ->where('id', '!=', $adminRoleId)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();
    }

    public function getAdminRoleID()
    {
        $adminPermission = RolePermission::where('permission_code', 'all_access')->first();
        return $adminPermission->role_id;
    }

    public function find(string $id)
    {
        return Role::with('permissions:id,role_id,permission_code')
            ->select('id', 'name', 'description')
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            if (!empty($data['permissions'])) {
                $this->syncPermissions($role, $data['permissions']);
            }

            return $role->load('permissions');
        });
    }

    public function update(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $this->syncPermissions($role, $data['permissions'] ?? []);

            return $role->load('permissions');
        });
    }

    public function delete(string $id)
    {
        $role = Role::findOrFail($id);
        return $role->delete();
    }

    protected function syncPermissions(Role $role, array $permissions)
    {
        RolePermission::where('role_id', $role->id)->delete();

        $records = collect($permissions)->map(function ($code) use ($role) {
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