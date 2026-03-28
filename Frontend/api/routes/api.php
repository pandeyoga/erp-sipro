<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RoleManagement\RoleController;
use App\Http\Controllers\UserManagement\UserManagementController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::post('/check-permissions', [AuthController::class, 'checkPermissions']);
    
    // route group role management
    Route::group(['prefix' => 'manage/role'], function () {
        Route::get('/permissions', [RoleController::class, 'getAllPermissionItems'])
            ->middleware('permission:role.get_all_permission_items');
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:role.get_all');
        Route::get('/select', [RoleController::class, 'getAllRoleForSelect'])
            ->middleware('permission:role.get_all');
        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:role.create');
        Route::get('/{id}', [RoleController::class, 'show'])
            ->middleware('permission:role.show');
        Route::put('/{id}', [RoleController::class, 'update'])
            ->middleware('permission:role.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])
            ->middleware('permission:role.delete');
    });

    // route group user management
    Route::group(['prefix' => 'manage/user'], function () {
        Route::get('/', [UserManagementController::class, 'index'])
            ->middleware('permission:user.get_all');
        Route::post('/', [UserManagementController::class, 'create'])
            ->middleware('permission:user.create');
    });
});