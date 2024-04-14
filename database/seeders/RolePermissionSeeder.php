<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                "name" => "super_admin",
                "guard_name" => "web",
            ],
            [
                "name" => "petugas",
                "guard_name" => "web",
            ],
            [
                "name" => "user",
                "guard_name" => "web",
            ],
        ];

        $permissions = [
            [
                "name" => "view credit",
                "guard_name" => "web",
            ],
            [
                "name" => "create credit",
                "guard_name" => "web",
            ],
            [
                "name" => "update credit",
                "guard_name" => "web",
            ],
            [
                "name" => "delete credit",
                "guard_name" => "web",
            ],
            [
                "name" => "view book",
                "guard_name" => "web",
            ],
            [
                "name" => "create book",
                "guard_name" => "web",
            ],
            [
                "name" => "update book",
                "guard_name" => "web",
            ],
            [
                "name" => "delete book",
                "guard_name" => "web",
            ],
            [
                "name" => "view category",
                "guard_name" => "web",
            ],
            [
                "name" => "create category",
                "guard_name" => "web",
            ],
            [
                "name" => "update category",
                "guard_name" => "web",
            ],
            [
                "name" => "delete category",
                "guard_name" => "web",
            ],
            [
                "name" => "view user",
                "guard_name" => "web",
            ],
            [
                "name" => "create user",
                "guard_name" => "web",
            ],
            [
                "name" => "update user",
                "guard_name" => "web",
            ],
            [
                "name" => "delete user",
                "guard_name" => "web",
            ],
            [
                "name" => "view role",
                "guard_name" => "web",
            ],
            [
                "name" => "create role",
                "guard_name" => "web",
            ],
            [
                "name" => "update role",
                "guard_name" => "web",
            ],
            [
                "name" => "delete role",
                "guard_name" => "web",
            ],
        ];

        foreach ($roles as $role) 
        {
            $role = Role::updateOrCreate(
                [
                    'name' => $role['name'],
                    "guard_name" => $role['guard_name']
                ],
                $role
            );

            foreach($permissions as $permission) {
                $permission = Permission::updateOrCreate(
                    ["name" => $permission['name'], "guard_name" => $permission['guard_name']],
                    $permission
                );
                $role->givePermissionTo($permission);
            }
        }


    }
}
