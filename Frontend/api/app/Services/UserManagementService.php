<?php

namespace App\Services;

use App\Repositories\UserManagementRepository;

class UserManagementService
{
    public function __construct(protected UserManagementRepository $repository) {}
    
    public function getAllUsers($req)
    {
        $page = $req['page'] ?? 1;
        $perPage = $req['per_page'] ?? 10;
        $search = $req['search'] ?? null;
        $role_id = $req['role_id'] ?? null;
        $is_active = $req['is_active'] ?? null;
        
        $userPaginationData = $this->repository->getAllUsers($page, $perPage, $search, $role_id, $is_active);
        
        
        $users = collect($userPaginationData->items())->map(function ($user) {
            $role = $user->roles?->name ?? 'No Role';
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
        
        return $userPaginationData;
    }

    public function createUser($requestData)
    {
        $password = bcrypt($requestData['password'] ?? config('app.default_password'));
        $userData = [
            'name' => $requestData['name'],
            'email' => $requestData['email'],
            'role_id' => $requestData['role_id'],
            'password' => $password
        ];

        $responseDB = $this->repository->createUser($userData);

        $response = [
            'id' => $responseDB->id,
            'name' => $responseDB->name,
            'email' => $responseDB->email,
            'role_id' => $responseDB->role_id,
            'role' => $responseDB->roles->name,
            'is_active' => true,
            'created_at' => $responseDB->created_at,
        ];

        return $response;
    }
}