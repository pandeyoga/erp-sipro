<?php
        
namespace App\Repositories;

use App\Models\RolePermission;
use App\Models\User;

class UserManagementRepository
{
    public function getAllUsers($page, $perPage, $search, $role_id, $is_active)
    {
        $adminRoleId = $this->getAdminRoleID();
        return User::with('roles:id,name')
            ->where('role_id', '!=', $adminRoleId)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->when($role_id, function ($query, $role_id) {
                return $query->where('role_id', $role_id);
            })->when($is_active, function ($query, $is_active) {
                return $query->where('is_active', $is_active);
            })->orderBy('created_at', 'desc')
            ->orderBy('is_active', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getAdminRoleID()
    {
        $adminPermission = RolePermission::where('permission_code', 'all_access')->first();
        return $adminPermission->role_id;
    }

    public function createUser($userData)
    {
        return User::create($userData);
    }
}