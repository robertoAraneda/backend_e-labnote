<?php

use App\Http\Controllers\DisponibilityController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\WorkareaController;
use Illuminate\Http\Request;
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
    Route::apiResource('permissions', PermissionController::class)->whereNumber('permission');;
    Route::apiResource('users', UserController::class)->whereNumber('user');;
    Route::apiResource('roles', RoleController::class)->whereNumber('role');;
    Route::apiResource('laboratories', LaboratoryController::class)->whereNumber('laboratory');;
    Route::apiResource('modules', ModuleController::class)->whereNumber('module');;
    Route::apiResource('menus', MenuController::class)->whereNumber('menu');;
    Route::apiResource('workareas', WorkareaController::class)->whereNumber('workarea');
    Route::apiResource('disponibilities', DisponibilityController::class)->whereNumber('disponibility');


    Route::post('roles/{role}/permissions', [RoleController::class, 'syncRolesPermission']);
    Route::post('laboratories/{laboratory}/modules', [LaboratoryController::class, 'syncModulesLaboratory']);


    Route::get('roles/{role}/permissions', [RoleController::class, 'permissionsByRole']);
    Route::get('laboratories/{laboratory}/modules', [LaboratoryController::class, 'modulesByLaboratory']);







    Route::get('roles/assign/super-admin', [RoleController::class, 'assignSuperUser']);








    Route::get('/tests/{id}', [ModuleController::class, 'findById']);




});
