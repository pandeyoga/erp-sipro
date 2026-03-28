<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Faker\Core\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleId = Str::uuid();
        
        Role::create([
            'id' => $roleId,
            'name' => 'Admin',
        ]);

        RolePermission::create([
            'role_id' => $roleId,
            'permission_code' => 'all_access',
        ]);

        $userId = Str::uuid();
        User::create([
            'id' => $userId,
            'name' => 'Admin',
            'email' => 'admin@landpro.com',
            'role_id' => $roleId,
            'password' => bcrypt('password'),
        ]);
    }
}
