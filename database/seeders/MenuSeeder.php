<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configurationId = Module::where('name', 'Configuración')->first()->id;
        $serviceRequestId = Module::where('name', 'Solicitud de medios')->first()->id;
        $advandedSettingId = Module::where('name', 'Configuración avanzada')->first()->id;
        Menu::create([
            'name' => 'Roles',
            'icon' => 'mdi-account-cog',
            'url' => 'roles',
            'module_id' => $advandedSettingId,
            'permission_id' => Permission::where('name', 'role.index')->first()->id,
            'order' => 1
        ]);

        Menu::create([
            'name' => 'Permisos',
            'icon' => 'mdi-gate',
            'url' => 'permissions',
            'module_id' => $advandedSettingId,
            'permission_id' => Permission::where('name', 'permission.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Módulos',
            'icon' => 'mdi-view-module',
            'url' => 'modules',
            'module_id' => $advandedSettingId,
            'permission_id' => Permission::where('name', 'module.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Usuarios',
            'icon' => 'mdi-account-group',
            'url' => 'users',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'user.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Laboratorios',
            'icon' => 'mdi-hospital-building',
            'url' => 'laboratories',
            'module_id' => $advandedSettingId,
            'permission_id' => Permission::where('name', 'laboratory.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Menus',
            'icon' => 'mdi-menu',
            'url' => 'menus',
            'module_id' => $advandedSettingId,
            'permission_id' => Permission::where('name', 'menu.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Areas de trabajo',
            'icon' => 'mdi-floor-plan',
            'url' => 'workareas',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'workarea.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Exámenes',
            'icon' => 'mdi-gate',
            'url' => 'analytes',
            'module_id' => $advandedSettingId,
            'permission_id' => Permission::where('name', 'analyte.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Prestaciones',
            'icon' => 'mdi-test-tube',
            'url' => 'observationServiceRequests',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'serviceRequestObservationCode.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Tipos disponibilidad',
            'icon' => 'mdi-check-all',
            'url' => 'availabilities',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'availability.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Tipos tiempo proceso',
            'icon' => 'mdi-clock-time-eight-outline',
            'url' => 'processTimes',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'processTime.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Tipos tiempo respuesta',
            'icon' => 'mdi-clock-fast',
            'url' => 'responseTimes',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'responseTime.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Tipos solicitud médica',
            'icon' => 'mdi-file-settings-outline',
            'url' => 'medicalRequestTypes',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'medicalRequestType.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Tipos cantidad muestra',
            'icon' => 'mdi-cup',
            'url' => 'sampleQuantities',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'sampleQuantity.index')->first()->id,
            'order' => 2
        ]);
        Menu::create([
            'name' => 'Tipos condición toma muestra',
            'icon' => 'mdi-check',
            'url' => 'samplingConditions',
            'module_id' => $configurationId,
            'permission_id' => Permission::where('name', 'samplingCondition.index')->first()->id,
            'order' => 2
        ]);

        Menu::create([
            'name' => 'Admitir paciente',
            'icon' => 'mdi-cog',
            'url' => 'admitPatient',
            'module_id' => $serviceRequestId,
            'permission_id' => Permission::where('name', 'patient.index')->first()->id,
            'order' => 3
        ]);

        Menu::create([
            'name' => 'Crear solicitud',
            'icon' => 'mdi-cog',
            'url' => 'createServiceRequest',
            'module_id' => $serviceRequestId,
            'permission_id' => Permission::where('name', 'serviceRequest.index')->first()->id,
            'order' => 4
        ]);
    }
}
