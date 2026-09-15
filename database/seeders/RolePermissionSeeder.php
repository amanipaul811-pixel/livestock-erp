<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Every code here gates a real route (see routes/api.php and
        // routes/web.php) -- no aspirational permissions for features that
        // don't exist. batch.update already covers closing a batch (status
        // is just one of the updatable fields), so there's no separate
        // batch.close; sales settle immediately with no approval step, so
        // there's no sale.approve either.
        $permissions = [
            'batch.create', 'batch.update',
            'animal.create', 'animalmovement.create',
            'weighin.create', 'feedlog.create', 'healthrecord.create',
            'expense.create', 'rationformula.create',
            'salesorder.create',
            'purchaseorder.create', 'purchaseorder.update',
            'supplier.create', 'warehouse.create',
            'dashboard.view', 'user.manage',
        ];

        foreach ($permissions as $code) {
            Permission::firstOrCreate(['code' => $code]);
        }

        $roles = [
            'Admin' => $permissions,
            'Farm Manager' => [
                'batch.create', 'batch.update', 'animal.create',
                'animalmovement.create', 'expense.create', 'rationformula.create',
                'purchaseorder.create', 'purchaseorder.update', 'supplier.create',
                'warehouse.create', 'dashboard.view',
            ],
            'Feeder' => ['feedlog.create', 'weighin.create', 'animalmovement.create'],
            'Vet' => ['healthrecord.create'],
            'Sales' => ['salesorder.create', 'dashboard.view'],
        ];

        foreach ($roles as $name => $codes) {
            $role = Role::firstOrCreate(['name' => $name]);
            $role->permissions()->sync(Permission::whereIn('code', $codes)->pluck('id'));
        }
    }
}
