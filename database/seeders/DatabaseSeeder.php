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
            UserSeeder::class,
            RoleSeeder::class,
            ModuleSeeder::class,
            LaboratorySeeder::class,

            AppointmentPermissionsSeeder::class,
            SlotPermissionsSeeder::class,
            AppointmentTypePermissionsSeeder::class,
            AppointmentStatusPermissionsSeeder::class,
            SpecimenPermissionsSeeder::class,
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
            SpecimenStatusPermissionsSeeder::class,
            SamplingRoomPermissionsSeeder::class,
            BusinessStatusPermissionsSeeder::class,
            NobilisAnalytePermissionsSeeder::class,

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
            LocationSeeder::class,
            LocationPhysicalTypeSeeder::class,
            LocationStatusSeeder::class,
            LocationTypeSeeder::class,
            IdentifierTypeSeeder::class,
            IdentifierUseSeeder::class,
            SpecimenCodeSeeder::class,
            ContainerSeeder::class,
            SpecimenStatusSeeder::class,
            ServiceRequestPrioritySeeder::class,
            ServiceRequestIntentSeeder::class,
            ServiceRequestStatusSeeder::class,
            ServiceRequestCategorySeeder::class,
            AppointmentStatusSeeder::class,
            AppointmentTypeSeeder::class,
            BusinessStatusSeeder::class,
        ]);
    }
}
