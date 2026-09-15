<?php

namespace Tests\Concerns;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

trait CreatesUsers
{
    protected function seedRoles(): void
    {
        $this->seed(RolePermissionSeeder::class);
    }

    protected function userWithRole(string $roleName): User
    {
        $this->seedRoles();

        $role = Role::where('name', $roleName)->firstOrFail();

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function adminUser(): User
    {
        return $this->userWithRole('Admin');
    }
}
