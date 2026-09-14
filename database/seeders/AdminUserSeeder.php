<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->firstOrFail();

        User::firstOrCreate(
            ['email' => 'admin@livestock-erp.test'],
            [
                'role_id' => $adminRole->id,
                'full_name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
