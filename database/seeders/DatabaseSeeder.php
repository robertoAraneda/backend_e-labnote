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
            LaboratorySeeder::class,
            ModuleSeeder::class,
            AvailabilityPermissionsSeeder::class,
            ProcessTimePermissionsSeeder::class,
            RolePermissionsSeeder::class,
            UserPermissionSeeder::class,
            WorkareaPermissionsSeeder::class,
            WorkareaSeeder::class,
            AnalyteSeeder::class,
            SamplingConditionPermissionsSeeder::class,
            SampleQuantityPermissionsSeeder::class,
            ResponseTimePermissionsSeeder::class,
            ProcessTimePermissionsSeeder::class,
            ProcessTimeSeeder::class,
            MedicalRequestTypePermissionsSeeder::class,
            MedicalRequestTypeSeeder::class,
            AvailabilityPermissionsSeeder::class,
            AvailabilitySeeder::class,
            ContainerPermissionSeeder::class,
            SpecimenCodePermissionSeeder::class,
            StatePermissionSeeder::class,
            CityPermissionSeeder::class,
            IdentifierTypeSeeder::class,
            IdentifierUseSeeder::class,
            AdministrativeGenderSeeder::class,
            AdministrativeGenderPermissionSeeder::class,
            LocationStatusSeeder::class,
            LocationPhysicalTypeSeeder::class,
            LocationTypeSeeder::class,
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
            SpecimenCodePermissionSeeder::class,
            UserPermissionSeeder::class,
        ]);
    }
}
