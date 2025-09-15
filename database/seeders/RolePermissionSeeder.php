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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'report incident']);
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'manage reports']);
        Permission::create(['name' => 'manage roles']);

        // create roles and assign created permissions
        $role1 = Role::create(['name' => 'community member']);
        $role1->givePermissionTo('report incident');

        $role2 = Role::create(['name' => 'council member']);
        $role2->givePermissionTo(['view reports', 'manage reports']);

        $role3 = Role::create(['name' => 'administrator']);
        $role3->givePermissionTo(Permission::all());

        // We can create a test user here and assign them a role
        $user = \App\Models\User::factory()->create([
            'name' => 'Test Council Member',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => "+237671946698"
        ]);
        $user->assignRole('council member');
    }
}
