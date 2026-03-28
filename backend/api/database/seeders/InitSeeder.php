<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //     $faker = Faker::create('id_ID');
    //     // create user marketing agent
    //    $role =Role::create([
    //         'name' => 'Marketing Agent Team 1',
    //         'description' => 'Marketing Agent',
    //         'group' => 'marketing_agent',
    //     ]);

        // User::factory(10)->create([
        //     'role_id' => $role->id,
        //     'password' => bcrypt('password'),
        // ]);

        // create user marketing internal
        // $role = Role::create([
        //     'name' => 'Marketing Prospect',
        //     'description' => 'Marketing Internal yang mengurusi Prospect',
        //     'group' => 'marketing_internal',
        // ]);
        
        // $permission = [
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "contact.get_all",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "contact.create",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "contact.show",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "contact.update",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "contact.delete",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "lead.get_all",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "lead.create",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "lead.show",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "lead.update",
        //     ],
        //     [
        //         'role_id' => $role->id,
        //         "permission_code" => "lead.update_status",
        //     ],
        // ];

        // foreach ($permission as $key => $value) {
        //     RolePermission::create($value);
        // }

        // $user = User::factory()->create([
        //     'email' => 'marketing-prospect@landpro.com',
        //     'role_id' => $role->id,
        //     'password' => bcrypt('password'),
        // ]);
    }
}
