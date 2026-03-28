<?php

namespace App\Services\Auth;

use App\Repositories\Auth\UserRepository;

class UserService
{
    public function __construct(protected UserRepository $repository) {}
    
    public function getAllUsers($req) : array
    {
        $page = $req['page'] ?? 1;
        $perPage = $req['per_page'] ?? 10;
        $search = $req['search'] ?? null;
        $role_id = $req['role_id'] ?? null;
        $is_active = $req['is_active'] ?? null;
        
        $userPaginationData = $this->repository->getAllUsers($page, $perPage, $search, $role_id, $is_active);
        if ($userPaginationData['error']) {
            return $userPaginationData;
        }
        $userPaginationData = $userPaginationData['result'];
        
        
        $users = collect($userPaginationData->items())->map(function ($user) {
            $role = $user->role?->name ?? 'No Role';
            return [
                'id' => $user->id,
                'role_id' => $user->role_id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
            ];
        });
        
        $userPaginationData->setCollection($users);
        
        return [
            'error' => null,
            'code' => 200,
            'result' => $userPaginationData,
        ];
    }

    public function createUser($requestData) : array
    {
        $password = bcrypt($requestData['password'] ?? config('app.default_password'));
        $userData = [
            'name' => $requestData['name'],
            'email' => $requestData['email'],
            'role_id' => $requestData['role_id'],
            'password' => $password
        ];

        $responseDB = $this->repository->createUser($userData);
        if ($responseDB['error']) {
            return $responseDB;
        }
        $responseDB = $responseDB['result'];

        $response = [
            'id' => $responseDB->id,
            'name' => $responseDB->name,
            'email' => $responseDB->email,
            'role_id' => $responseDB->role_id,
            'role' => $responseDB->role->name,
            'is_active' => true,
            'created_at' => $responseDB->created_at,
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $response
        ];
    }

    public function getUserById($id) : array
    {
        $user = $this->repository->getUserById($id);
        if ($user['error']) {
            return $user;
        }
        $user = $user['result'];

        $user = [
            'id' => $user->id,
            'role_id' => $user->role_id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role?->name,
            'is_active' => $user->is_active,
            'created_at' => $user->created_at,
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $user
        ];
    }

    public function updateUser($id, $requestData) : array
    {
        $password = bcrypt($requestData['password'] ?? null);
        $userData = [
            'name' => $requestData['name'],
            'email' => $requestData['email'],
            'role_id' => $requestData['role_id'],
            'is_active' => $requestData['is_active'] ?? null,
            'password' => $password
        ];

        $updateUser = $this->repository->updateUser($id, $userData);
        if ($updateUser['error']) {
            return $this->repository;
        }

        $user = $this->repository->getUserById($id);
        if ($user['error']) {
            return $user;
        }
        $user = $user['result'];

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role->name,
            'is_active' => $user->is_active,
            'updated_at' => $user->updated_at
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $response
        ];
    }

    public function deleteUser($id) : array
    {
        $deleteUser = $this->repository->deleteUser($id);
        if ($deleteUser['error']) {
            return $deleteUser;
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => 'User deleted successfully'
        ];
    }

    public function toggleUserStatus($id) : array
    {
        $user = $this->repository->getUserById($id);
        if ($user['error']) {
            return $user;
        }
        $user = $user['result'];

        $user->is_active = !$user->is_active;

        $update = $this->repository->updateUser($id, $user);
        if ($update['error']) {
            return $update;
        }

        $result = [
            'user_id' => $user->id,
            'status' => $user->is_active ? 'active' : 'inactive'
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $result
        ];
    }
}