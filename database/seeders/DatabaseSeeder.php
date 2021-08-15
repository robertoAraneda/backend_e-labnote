<?php

namespace Database\Seeders;

use App\Models\Organization;
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
            AvailabilityPermissionsSeeder::class,
            ProcessTimePermissionsSeeder::class,
            RolePermissionsSeeder::class,
            UserPermissionSeeder::class,
            WorkareaPermissionsSeeder::class,
            SamplingConditionPermissionsSeeder::class,
            SampleQuantityPermissionsSeeder::class,
            ResponseTimePermissionsSeeder::class,
            MedicalRequestTypePermissionsSeeder::class,
            ContainerPermissionSeeder::class,
            SpecimenCodePermissionSeeder::class,
            StatePermissionSeeder::class,
            CityPermissionSeeder::class,
            AdministrativeGenderPermissionSeeder::class,
            PermissionPermissionSeeder::class,
            AnalytePermissionSeeder::class,
            LocationPhysicalTypePermissionsSeeder::class,
            FonasaPermissionsSeeder::class,
            LaboratoryPermissionSeeder::class,
            LocationStatusPermissionsSeeder::class,
            LocationPermissionsSeeder::class,
            LocationTypePermissionsSeeder::class,
            ModulePermissionSeeder::class,
            MenuPermissionSeeder::class,
            OrganizationPermissionsSeeder::class,
            PatientPermissionSeeder::class,
            PractitionerPermissionsSeeder::class,
            SamplingIndicationPermissionSeeder::class,
            ServiceRequestCategoryPermissionsSeeder::class,
            ServiceRequestIntentPermissionsSeeder::class,
            ServiceRequestObservationCodePermissionsSeeder::class,
            ServiceRequestPriorityPermissionsSeeder::class,
            ServiceRequestStatusPermissionsSeeder::class,
            ServiceRequestPermissionsSeeder::class,
            ModuleSeeder::class,
            LaboratorySeeder::class,
            MenuSeeder::class,
            WorkareaSeeder::class,
            AnalyteSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            AvailabilitySeeder::class,
            FonasaSeeder::class,
            MedicalRequestTypeSeeder::class,
            ProcessTimeSeeder::class,
            AdministrativeGenderSeeder::class,
            LocationPhysicalTypeSeeder::class,
            LocationStatusSeeder::class,
            LocationTypeSeeder::class,
            IdentifierTypeSeeder::class,
            IdentifierUseSeeder::class,
        ]);
    }
}
