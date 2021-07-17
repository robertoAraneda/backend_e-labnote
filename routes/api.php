<?php

use App\Http\Controllers\AnalyteController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\FonasaController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\LoincController;
use App\Http\Controllers\MedicalRequestTypeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ObservationServiceRequestController;
use App\Http\Controllers\ProcessTimeController;
use App\Http\Controllers\RelAnalyteSamplingConditionController;
use App\Http\Controllers\RelLaboratoryModuleController;
use App\Http\Controllers\RelModulePermissionController;
use App\Http\Controllers\RelSpecimenSamplingIndicationController;
use App\Http\Controllers\ResponseTimeController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SampleQuantityController;
use App\Http\Controllers\SpecimenController;
use App\Http\Controllers\SamplingConditionController;
use App\Http\Controllers\SamplingIndicationController;
use App\Http\Controllers\WorkareaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
  'prefix' => 'v1/auth'
], function () {
    Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('signup', [App\Http\Controllers\AuthController::class, 'signup']);

    Route::group([
    'middleware' => 'auth:api'
  ], function () {
      Route::get('user', [App\Http\Controllers\AuthController::class, 'user']);
     // Route::get('logout', 'AuthController@logout');
  });
});

Route::group([
  'prefix' => 'v1',
  'middleware' => 'auth:api'
], function () {
    Route::apiResource('permissions', PermissionController::class)->whereNumber('permission')->names('api.permissions');
    Route::apiResource('users', UserController::class)->whereNumber('user')->names('api.users');
    Route::apiResource('roles', RoleController::class)->whereNumber('role')->names('api.roles');
    Route::apiResource('laboratories', LaboratoryController::class)->whereNumber('laboratory')->names('api.laboratories');
    Route::apiResource('modules', ModuleController::class)->whereNumber('module')->names('api.modules');
    Route::apiResource('menus', MenuController::class)->whereNumber('menu')->names('api.menus');
    Route::apiResource('workareas', WorkareaController::class)->whereNumber('workarea')->names('api.workareas');
    Route::apiResource('availabilities', AvailabilityController::class)->whereNumber('availability')->names('api.availabilities');
    Route::apiResource('process-times', ProcessTimeController::class)->whereNumber('process_time')->names('api.process-times');
    Route::apiResource('response-times', ResponseTimeController::class)->whereNumber('response_time');
    Route::apiResource('medical-request-types', MedicalRequestTypeController::class)->whereNumber('medical_request_type')->names('api.medical-request-types');
    Route::apiResource('fonasas', FonasaController::class)->whereNumber('fonasa');
    Route::apiResource('sample-quantities', SampleQuantityController::class)->whereNumber('sample_quantity');
    Route::apiResource('sampling-conditions', SamplingConditionController::class)->whereNumber('sampling_condition')->names('api.sampling-conditions');
    Route::apiResource('analytes', AnalyteController::class)->whereNumber('analyte')->names('api.analytes');
    Route::apiResource('loincs', LoincController::class)->names('api.loincs');
    Route::apiResource('containers', ContainerController::class)->whereNumber('container')->names('api.containers');
    Route::apiResource('specimens', SpecimenController::class)->whereNumber('specimen')->names('api.specimens');
    Route::apiResource('sampling-indications', SamplingIndicationController::class)->whereNumber('sampling_indication')->names('api.sampling-indications');
    Route::apiResource('observation-service-requests', ObservationServiceRequestController::class)->whereNumber('observation_service_request')->names('api.observation-service-requests');



    Route::post('roles/{role}/permissions', [RoleController::class, 'syncRolesPermission']);
    Route::post('laboratories/{laboratory}/modules', [LaboratoryController::class, 'syncModulesLaboratory']);


    Route::get('roles/{role}/permissions', [RoleController::class, 'permissionsByRole']);
    Route::get('laboratories/{laboratory}/modules', [LaboratoryController::class, 'modulesByLaboratory']);
    Route::get('modules/{module}/menus', [ModuleController::class, 'menusByModule'] )->name('api.module.menus');



    //rels
    Route::apiResource('modules.permissions', RelModulePermissionController::class)->only('index', 'store')->whereNumber('module')->names('api.modules.permissions');
    Route::apiResource('laboratories.modules', RelLaboratoryModuleController::class)->only('index', 'store')->whereNumber('laboratory')->names('api.laboratories.modules');
    Route::apiResource('analytes.sampling-conditions', RelAnalyteSamplingConditionController::class)->only('index', 'store')->whereNumber('analyte')->names('api.analytes.sampling-conditions');
    Route::apiResource('specimens.sampling-indications', RelSpecimenSamplingIndicationController::class)->only('index', 'store')->whereNumber('specimen')->names('api.specimens.sampling-indications');

    //search queries
    Route::get('modules/search', [ModuleController::class, 'searchByParams']);


    //change active attribute mode
    Route::put('roles/{role}/status', [RoleController::class, 'changeActiveAttribute']);
    Route::put('users/{user}/status', [UserController::class, 'changeActiveAttribute']);
    Route::put('laboratories/{laboratory}/status', [LaboratoryController::class, 'changeActiveAttribute']);
    Route::put('workareas/{workarea}/status', [WorkareaController::class, 'changeActiveAttribute']);





    Route::get('roles/assign/super-admin', [RoleController::class, 'assignSuperUser']);
    Route::get('/tests/{id}', [ModuleController::class, 'findById']);




});
