<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            "name" => "Super Admin",
            "username" => "superadmin",
            "email" => "superadmin@gmail.com",
            "password" => Hash::make("password")
        ];
        User::insert($data);
    }
}
