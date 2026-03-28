<?php
        
namespace App\Repositories\Auth;

use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function getAllUsers($page, $perPage, $search, $role_id, $is_active) : array
    {
        try {
            $adminRoleId = $this->getAdminRoleID();
            $users = User::with('role:id,name')
                ->where('role_id', '!=', $adminRoleId)
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%");
                })->when($role_id, function ($query, $role_id) {
                    return $query->where('role_id', $role_id);
                })->when($is_active, function ($query, $is_active) {
                    return $query->where('is_active', $is_active);
                })->orderBy('created_at', 'desc')
                ->orderBy('is_active', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null,
            ];
        }

        return [
            'error' =>  null,
            'code' => 200,
            'result' => $users,
        ];
    }

    public function getAdminRoleID() : string
    {
        $adminPermission = RolePermission::where('permission_code', 'all_access')->first();
        return $adminPermission->role_id;
    }

    public function createUser($userData) : array
    {
        try {
            DB::beginTransaction();
            $create = User::create($userData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $create
        ];
    }

    public function updateUser($id, $userData) : array
    {
        $data = [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role_id' => $userData['role_id'],
        ];

        if ($userData['password'] !== null) {
            $data['password'] = $userData['password'];
        }

        if ($userData['is_active'] !== null) {
            $data['is_active'] = $userData['is_active'];
        }

        try {
            DB::beginTransaction();
            $user = User::where('id', $id)->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null,
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $user
        ];
    }

    public function getUserById($id) : array
    {
        try {
            $adminRoleId = $this->getAdminRoleID();
            $user = User::with('role:id,name')
                ->where('role_id', '!=', $adminRoleId)
                ->where('id', $id)->first();
            if (!$user) {
                return [
                    'error' => "User not found",
                    'code' => 404,
                    'result' => null,
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null,
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $user
        ];
    }

    public function deleteUser($id) : array
    {
        $adminRoleId = $this->getAdminRoleID();
        try {
            DB::beginTransaction();
            $user = User::where('id', $id)->where('role_id', '!=', $adminRoleId)->first();
            if (!$user) {
                DB::rollBack();
                return [
                    'error' => "User not found",
                    'code' => 404,
                    'result' => null,
                ];
            }
            $user->delete();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $user
        ];
    }
}