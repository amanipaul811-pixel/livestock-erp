<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'batch.create', 'batch.update', 'batch.close',
            'animal.create', 'weighin.create', 'feedlog.create',
            'healthrecord.create', 'salesorder.create', 'sale.approve',
            'dashboard.view', 'user.manage',
        ];

        foreach ($permissions as $code) {
            Permission::firstOrCreate(['code' => $code]);
        }

        $roles = [
            'Admin' => $permissions,
            'Farm Manager' => ['batch.create', 'batch.update', 'batch.close', 'animal.create', 'dashboard.view'],
            'Feeder' => ['feedlog.create', 'weighin.create'],
            'Vet' => ['healthrecord.create'],
            'Sales' => ['salesorder.create', 'sale.approve', 'dashboard.view'],
        ];

        foreach ($roles as $name => $codes) {
            $role = Role::firstOrCreate(['name' => $name]);
            $role->permissions()->sync(Permission::whereIn('code', $codes)->pluck('id'));
        }
    }
}
