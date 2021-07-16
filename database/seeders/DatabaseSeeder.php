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
            WorkareaSeeder::class,
            AnalytePermissionSeeder::class,
            AnalyteSeeder::class,
            SamplingConditionPermissionsSeeder::class,
            SamplingConditionSeeder::class,
            SampleQuantityPermissionsSeeder::class,
            SampleQuantitySeeder::class,
            ResponseTimePermissionsSeeder::class,
            ResponseTimeSeeder::class,
            ProcessTimePermissionsSeeder::class,
            ProcessTimeSeeder::class,
            MedicalRequestTypePermissionsSeeder::class,
            MedicalRequestTypeSeeder::class,
            DisponibilityPermissionsSeeder::class,
            AvailabilitySeeder::class,
            ContainerSeeder::class,
            ContainerPermissionSeeder::class,
            SampleTypeSeeder::class,
            SampleTypePermissionSeeder::class
        ]);
    }
}
