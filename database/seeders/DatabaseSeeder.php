<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            LaboratorySeeder::class,
            ModuleSeeder::class,
            DisponibilityPermissionsSeeder::class,
            ProcessTimePermissionsSeeder::class,
            RolePermissionsSeeder::class,
            RelModuleMenuPermissionsSeeder::class,
            RelModulePermissionPermissionsSeeder::class,
            UserPermissionSeeder::class,
            WorkareaPermissionsSeeder::class,
            WorkareaSeeder::class
        ]);
    }
}
