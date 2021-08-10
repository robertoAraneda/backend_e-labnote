<?php

use App\Http\Controllers\AnalyteController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FonasaController;
use App\Http\Controllers\AdministrativeGenderController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\LoincController;
use App\Http\Controllers\MedicalRequestTypeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ObservationServiceRequestController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProcessTimeController;
use App\Http\Controllers\RelObservationServiceRequestSamplingConditionController;
use App\Http\Controllers\RelLaboratoryModuleController;
use App\Http\Controllers\RelModulePermissionController;
use App\Http\Controllers\RelSpecimenSamplingIndicationController;
use App\Http\Controllers\ResponseTimeController;
use App\Http\Controllers\SampleQuantityController;
use App\Http\Controllers\ServiceRequestCategoryController;
use App\Http\Controllers\ServiceRequestIntentController;
use App\Http\Controllers\ServiceRequestPriorityController;
use App\Http\Controllers\ServiceRequestStatusController;
use App\Http\Controllers\SpecimenController;
use App\Http\Controllers\SamplingConditionController;
use App\Http\Controllers\SamplingIndicationController;
use App\Http\Controllers\StateController;
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
    Route::apiResource('permissions', PermissionController::class)
        ->whereNumber('permission')
        ->names('api.permissions');

    Route::apiResource('users', UserController::class)
        ->whereNumber('user')
        ->names('api.users');

    Route::apiResource('roles', RoleController::class)
        ->whereNumber('role')
        ->names('api.roles');

    Route::apiResource('laboratories', LaboratoryController::class)
        ->whereNumber('laboratory')
        ->names('api.laboratories');

    Route::apiResource('modules', ModuleController::class)
        ->whereNumber('module')
        ->names('api.modules');

    Route::apiResource('menus', MenuController::class)
        ->whereNumber('menu')
        ->names('api.menus');

    Route::apiResource('workareas', WorkareaController::class)
        ->whereNumber('workarea')
        ->names('api.workareas');

    Route::apiResource('availabilities', AvailabilityController::class)
        ->whereNumber('availability')
        ->names('api.availabilities');

    Route::apiResource('process-times', ProcessTimeController::class)
        ->whereNumber('process_time')
        ->names('api.process-times');

    Route::apiResource('response-times', ResponseTimeController::class)
        ->whereNumber('response_time')
        ->names('api.response-times');

    Route::apiResource('medical-request-types', MedicalRequestTypeController::class)
        ->whereNumber('medical_request_type')
        ->names('api.medical-request-types');

    Route::apiResource('fonasas', FonasaController::class)
        ->names('api.fonasas');

    Route::apiResource('sample-quantities', SampleQuantityController::class)
        ->whereNumber('sample_quantity')
        ->names('api.sample-quantities');

    Route::apiResource('sampling-conditions', SamplingConditionController::class)
        ->whereNumber('sampling_condition')
        ->names('api.sampling-conditions');

    Route::apiResource('analytes', AnalyteController::class)
        ->whereNumber('analyte')
        ->names('api.analytes');

    Route::apiResource('loincs', LoincController::class)
        ->names('api.loincs');

    Route::apiResource('containers', ContainerController::class)
        ->whereNumber('container')
        ->names('api.containers');

    Route::apiResource('specimens', SpecimenController::class)
        ->whereNumber('specimen')
        ->names('api.specimens');

    Route::apiResource('sampling-indications', SamplingIndicationController::class)
        ->whereNumber('sampling_indication')
        ->names('api.sampling-indications');

    Route::apiResource('observation-service-requests', ObservationServiceRequestController::class)
        ->whereNumber('observation_service_request')
        ->names('api.observation-service-requests');

    Route::apiResource('states', StateController::class)
        ->names('api.states');

    Route::apiResource('cities', CityController::class)
        ->names('api.cities');

    Route::apiResource('patients', PatientController::class)
        ->whereNumber('patient')
        ->names('api.patients');

    Route::apiResource('administrative-genders', AdministrativeGenderController::class)
        ->whereNumber('administrative_gender')
        ->names('api.administrative-genders');

    Route::apiResource('service-request-categories', ServiceRequestCategoryController::class)
        ->whereNumber('service_request_category')
        ->names('api.service-request-categories');

    Route::apiResource('service-request-intents', ServiceRequestIntentController::class)
        ->whereNumber('service_request_intent')
        ->names('api.service-request-intents');

    Route::apiResource('service-request-priorities', ServiceRequestPriorityController::class)
        ->whereNumber('service_request_priority')
        ->names('api.service-request-priorities');

    Route::apiResource('service-request-statuses', ServiceRequestStatusController::class)
        ->whereNumber('service_request_status')
        ->names('api.service-request-statuses');


    Route::post('roles/{role}/permissions', [RoleController::class, 'syncRolesPermission']);
    //Route::post('laboratories/{laboratory}/modules', [LaboratoryController::class, 'syncModulesLaboratory']);


    //rels one to many
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissionsByRole']);
    Route::get('modules/{module}/menus', [ModuleController::class, 'menusByModule'])->name('api.module.menus');
    Route::get('states/{state}/cities', [StateController::class, 'districts'])->name('api.state.districts');
    Route::get('patients/{patient}/names', [PatientController::class, 'names'])->name('api.patients.names');
    Route::get('patients/{patient}/telecoms', [PatientController::class, 'telecoms'])->name('api.patients.telecoms');
    Route::get('patients/{patient}/addresses', [PatientController::class, 'addresses'])->name('api.patients.addresses');
    Route::get('patients/{patient}/contacts', [PatientController::class, 'contacts'])->name('api.patients.contacts');


    //rels many to many
    Route::apiResource('modules.permissions', RelModulePermissionController::class)
        ->only('index', 'store')
        ->whereNumber('module')
        ->names('api.modules.permissions');

    Route::apiResource('laboratories.modules', RelLaboratoryModuleController::class)
        ->only('index', 'store')
        ->whereNumber('laboratory')->names('api.laboratories.modules');

    Route::apiResource('observation-service-requests.sampling-conditions', RelObservationServiceRequestSamplingConditionController::class)
        ->only('index', 'store')
        ->whereNumber('observation_service_request')
        ->names('api.observation-service-request.sampling-conditions');

    Route::apiResource('specimens.sampling-indications', RelSpecimenSamplingIndicationController::class)
        ->only('index', 'store')
        ->whereNumber('specimen')
        ->names('api.specimens.sampling-indications');


    //search queries
    Route::get('modules/search', [ModuleController::class, 'searchByParams']);
    Route::get('patients/search', [PatientController::class, 'searchByParams']);
    Route::get('observation-service-requests/search', [ObservationServiceRequestController::class, 'searchByParams']);


    //change active attribute mode
    Route::put('roles/{role}/status', [RoleController::class, 'changeActiveAttribute']);
    Route::put('users/{user}/status', [UserController::class, 'changeActiveAttribute']);
    Route::put('laboratories/{laboratory}/status', [LaboratoryController::class, 'changeActiveAttribute']);
    Route::put('workareas/{workarea}/status', [WorkareaController::class, 'changeActiveAttribute']);
    Route::put('analytes/{analyte}/status', [AnalyteController::class, 'changeActiveAttribute']);
    Route::put('availabilities/{availability}/status', [AvailabilityController::class, 'changeActiveAttribute']);
    Route::put('containers/{container}/status', [ContainerController::class, 'changeActiveAttribute']);
    Route::put('process-times/{process_time}/status', [ProcessTimeController::class, 'changeActiveAttribute']);
    Route::put('response-times/{response_time}/status', [ResponseTimeController::class, 'changeActiveAttribute']);
    Route::put('medical-request-types/{medical_request_type}/status', [MedicalRequestTypeController::class, 'changeActiveAttribute']);
    Route::put('sample-quantities/{sample_quantity}/status', [SampleQuantityController::class, 'changeActiveAttribute']);
    Route::put('sampling-conditions/{sampling_condition}/status', [SamplingConditionController::class, 'changeActiveAttribute']);
    Route::put('fonasas/{fonasa}/status', [FonasaController::class, 'changeActiveAttribute']);
    Route::put('menus/{menu}/status', [MenuController::class, 'changeActiveAttribute']);
    Route::put('modules/{module}/status', [ModuleController::class, 'changeActiveAttribute']);
    Route::put('observation-service-requests/{observation_service_request}/status', [ObservationServiceRequestController::class, 'changeActiveAttribute']);
    Route::put('states/{state}/status', [StateController::class, 'changeActiveAttribute']);
    Route::put('cities/{city}/status', [CityController::class, 'changeActiveAttribute']);
    Route::put('administrative-genders/{administrative_gender}/status', [AdministrativeGenderController::class, 'changeActiveAttribute']);
    Route::put('service-request-categories/{service_request_category}/status', [ServiceRequestCategoryController::class, 'changeActiveAttribute']);
    Route::put('service-request-intents/{service_request_intent}/status', [ServiceRequestIntentController::class, 'changeActiveAttribute']);
    Route::put('service-request-priorities/{service_request_priority}/status', [ServiceRequestPriorityController::class, 'changeActiveAttribute']);
    Route::put('service-request-statuses/{service_request_status}/status', [ServiceRequestStatusController::class, 'changeActiveAttribute']);


    //test routes
    Route::get('roles/assign/super-admin', [RoleController::class, 'assignSuperUser']);
    Route::get('/tests/{id}', [ModuleController::class, 'findById']);


    //closures Routes
    Route::get('identifier-types', function(){
       $identifierTypes =  \App\Models\IdentifierType::all()->map(function ($identifier){
           return [
               'id' => $identifier->id,
               'code' => $identifier->code,
               'display' => $identifier->display
           ];
       });

        return response()->json(['collection' => $identifierTypes], 200);
    });

    Route::get('identifier-uses', function(){
        $identifierUses =  \App\Models\IdentifierUse::all()->map(function ($identifier){
            return [
                'id' => $identifier->id,
                'code' => $identifier->code,
                'display' => $identifier->display
            ];
        });

        return response()->json(['collection' => $identifierUses], 200);
    });


});
